<?php

namespace App\Imports;

use App\Models\Asn;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AsnImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Skip empty rows where name is not present
        if (empty($row['name'])) {
            return null;
        }

        $birthDate = null;
        if (!empty($row['birth_date'])) {
            if (is_numeric($row['birth_date'])) {
                $birthDate = Date::excelToDateTimeObject($row['birth_date'])->format('Y-m-d');
            } else {
                $birthDate = date('Y-m-d', strtotime($row['birth_date']));
            }
        }

        $gender = null;
        if (!empty($row['gender'])) {
            $g = strtoupper(substr(trim($row['gender']), 0, 1));
            if (in_array($g, ['L', 'P'])) {
                $gender = $g;
            }
        }

        $asnType = null;
        if (!empty($row['asn_type'])) {
            $type = strtolower(trim($row['asn_type']));
            if (in_array($type, ['pns', 'cpns', 'pppk', 'lainnya'])) {
                $asnType = $type;
            }
        }

        $asnSource = null;
        if (!empty($row['asn_source'])) {
            $source = strtolower(trim($row['asn_source']));
            if (in_array($source, ['pusat', 'daerah', 'lainnya'])) {
                $asnSource = $source;
            }
        }

        return new Asn([
            'nip' => !empty($row['nip']) ? $row['nip'] : null,
            'name' => $row['name'],
            'job_title' => $row['job_title'] ?? null,
            'phone_number' => $row['phone_number'] ?? null,
            'email' => $row['email'] ?? null,
            'birth_city' => $row['birth_city'] ?? null,
            'birth_date' => $birthDate,
            'gender' => $gender,
            'rank_grade' => $row['rank_grade'] ?? null,
            'latest_education' => $row['latest_education'] ?? null,
            'office_address' => $row['office_address'] ?? null,
            'asn_type' => $asnType,
            'asn_source' => $asnSource,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'nip' => 'nullable|unique:asns,nip',
        ];
    }
}
