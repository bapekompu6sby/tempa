<?php

namespace App\Imports;

use App\Models\Asn;
use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AsnImport implements ToCollection, WithHeadingRow
{
    protected ?int $eventId;
    protected int $importedCount = 0;
    protected int $attachedCount = 0;

    public function __construct($eventId = null)
    {
        $this->eventId = !empty($eventId) ? (int) $eventId : null;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getAttachedCount(): int
    {
        return $this->attachedCount;
    }

    protected function plainValue($value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        }
        $str = trim((string) $value);
        return $str === '' ? null : $str;
    }

    public function collection(Collection $rows)
    {
        $defaultEvent = $this->eventId ? Event::find($this->eventId) : null;
        $eventCache = [];
        if ($defaultEvent) {
            $eventCache[$defaultEvent->id] = $defaultEvent;
        }

        foreach ($rows as $row) {
            $name = $this->plainValue($row['name'] ?? $row['nama'] ?? $row['nama_lengkap'] ?? null);

            // Skip empty rows where name is not present
            if (empty($name)) {
                continue;
            }

            // Parse birth_date as plain data
            $rawBirthDate = $row['birth_date'] ?? $row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? null;
            if ($rawBirthDate instanceof RichText) {
                $rawBirthDate = $rawBirthDate->getPlainText();
            }
            $birthDate = null;
            if (!empty($rawBirthDate)) {
                if (is_numeric($rawBirthDate) && $rawBirthDate > 1000 && $rawBirthDate < 100000) {
                    try {
                        $birthDate = Date::excelToDateTimeObject($rawBirthDate)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $birthDate = null;
                    }
                } else {
                    $ts = strtotime(trim((string) $rawBirthDate));
                    if ($ts !== false) {
                        $birthDate = date('Y-m-d', $ts);
                    }
                }
            }

            // Parse gender (L / P)
            $rawGender = $this->plainValue($row['gender'] ?? $row['jenis_kelamin'] ?? $row['jk'] ?? null);
            $gender = null;
            if ($rawGender !== null) {
                $g = strtoupper(substr($rawGender, 0, 1));
                if (in_array($g, ['L', 'P'])) {
                    $gender = $g;
                }
            }

            // Parse asn_type
            $rawType = $this->plainValue($row['asn_type'] ?? $row['jenis_asn'] ?? $row['tipe_asn'] ?? null);
            $asnType = null;
            if ($rawType !== null) {
                $type = strtolower($rawType);
                if (in_array($type, ['pns', 'cpns', 'pppk', 'lainnya'])) {
                    $asnType = $type;
                }
            }

            // Parse asn_source
            $rawSource = $this->plainValue($row['asn_source'] ?? $row['asal_asn'] ?? $row['sumber_asn'] ?? null);
            $asnSource = null;
            if ($rawSource !== null) {
                $source = strtolower($rawSource);
                if (in_array($source, ['pusat', 'daerah', 'lainnya'])) {
                    $asnSource = $source;
                }
            }

            // Parse NIP cleanly (treat as plain digits/text, avoiding float formatting issues)
            $rawNip = $row['nip'] ?? null;
            $nip = null;
            if ($rawNip !== null) {
                if ($rawNip instanceof RichText) {
                    $rawNip = $rawNip->getPlainText();
                }
                if (is_float($rawNip) || (is_numeric($rawNip) && stripos((string) $rawNip, 'e') !== false)) {
                    $rawNip = number_format((float) $rawNip, 0, '', '');
                }
                $nipStr = trim((string) $rawNip);
                $nipStr = ltrim($nipStr, "'"); // strip leading apostrophe if used in Excel
                if ($nipStr !== '') {
                    $nip = $nipStr;
                }
            }

            $asnData = [
                'name' => $name,
                'job_title' => $this->plainValue($row['job_title'] ?? $row['jabatan'] ?? null),
                'phone_number' => $this->plainValue($row['phone_number'] ?? $row['no_hp'] ?? $row['telepon'] ?? null),
                'email' => $this->plainValue($row['email'] ?? null),
                'birth_city' => $this->plainValue($row['birth_city'] ?? $row['tempat_lahir'] ?? null),
                'birth_date' => $birthDate,
                'gender' => $gender,
                'rank_grade' => $this->plainValue($row['rank_grade'] ?? $row['pangkat_golongan'] ?? $row['pangkat'] ?? null),
                'latest_education' => $this->plainValue($row['latest_education'] ?? $row['pendidikan_terakhir'] ?? $row['pendidikan'] ?? null),
                'office_address' => $this->plainValue($row['office_address'] ?? $row['alamat_kantor'] ?? $row['instansi'] ?? null),
                'asn_type' => $asnType,
                'asn_source' => $asnSource,
            ];

            if ($nip) {
                $asn = Asn::updateOrCreate(
                    ['nip' => $nip],
                    $asnData
                );
            } else {
                $asn = Asn::create($asnData);
            }

            $this->importedCount++;

            // Determine event relationship: either from form selection ($this->eventId) or row's event_id column
            $rowEventId = $this->plainValue($row['event_id'] ?? $row['id_pelatihan'] ?? null);
            $targetEventId = $this->eventId ?: ($rowEventId && is_numeric($rowEventId) ? (int) $rowEventId : null);

            if ($targetEventId) {
                if (!array_key_exists($targetEventId, $eventCache)) {
                    $eventCache[$targetEventId] = Event::find($targetEventId);
                }
                $targetEvent = $eventCache[$targetEventId];
                if ($targetEvent) {
                    $targetEvent->asns()->syncWithoutDetaching([$asn->id]);
                    $this->attachedCount++;
                }
            }
        }
    }
}
