# TEMPA — User Guide

This guide explains how to use the TEMPA application (Indonesian UI labels used throughout). It covers: starting at the home page, logging in as panitia, creating an event, checking event instructions, and uploading event documents.

---

## Quick overview

- Home page: hero shows TEMPA and a password form for panitia. Button: `Masuk Panitia`.
- When unlocked, the header shows links: `Instruksi` and `Pelatihan`.
- Event list cards show a learning model badge and three phase blocks (Persiapan / Pelaksanaan / Pelaporan).
- Documents editor uses staged uploads: selected files are staged client-side and uploaded only after pressing `Simpan`.

---

## 1) Open app and log in (home)

1. Open the application root URL in your browser.
2. Locate the password input labelled: **Masukkan Password untuk panitia**.
3. Type the panitia password and click the blue button **Masuk Panitia**.
   - On success you'll see the `Instruksi` and `Pelatihan` links in the header.
   - If you don't have the password, a read-only events list is shown below the login form.

Tip: if you updated views and the browser still shows old UI, run in the project root:

```cmd
php artisan view:clear
```

---

## 2) Create a new event (Pelatihan)

1. Click **Pelatihan** in the header or open **Daftar Pelatihan**.
2. Click the button labelled `Buat`, `Tambah`, or a `+` action to create a new event (this label may vary).
3. Fill required fields in the event form:
   - Nama Pelatihan (event name)
   - Start date / End date (tanggal awal / tanggal akhir)
   - Learning model (select: E-Learning / Distance / Blended / Klasikal)
   - Optional notes
4. Save/Submit the form.

Notes:
- The application may auto-compute two helper dates when an event is created:
  - `preparation_date` = start_date minus 30 days (used for Persiapan phase)
  - `report_date` = end_date plus 14 days (used for Pelaporan phase)
- If those columns are not available, run migrations (admins/maintainers only):

```cmd
php artisan migrate
```

---

## 3) Open an event and check instructions

1. From the **Daftar Pelatihan**, click **Lihat** on the desired event card to open event details.
2. The event detail page shows three phase blocks: **Persiapan**, **Pelaksanaan**, **Pelaporan**. Each block includes:
   - Date range for that phase (for example: `preparation_date → start_date`)
   - A small progress bar and counts like `checked / total`.
3. Instruction list:
   - Instructions are numbered (No.) for easy ordering.
   - Use the phase filter to show instructions for a specific phase (Persiapan / Pelaksanaan / Pelaporan) or `All`.
   - Mark instructions complete using the provided check controls.
4. Clicking a phase block often acts as a quick filter to show only instructions for that phase.

Tip: learning model values appear as badges. `classical` is displayed as **Klasikal**.

---

## 4) Upload event documents (Kelengkapan Dokumen)

1. From an event page, click the `Kelengkapan Dokumen` link for that event.
2. The page lists document types (rows). For each row you have two main actions:
   - **Lihat File** — shows a read-only view with:
     1. Link Dokumen (clickable anchor or `-` if not present)
     2. Lampiran — attached files, shown with blue **Download** buttons
     3. Catatan — read-only notes (whitespace preserved)
   - **Edit** — opens an inline edit row where you can change link, add files, and edit notes.

3. Editing and uploading flow (important):
   - Click **Pilih file** to choose files from your device. Selected files are staged client-side and appear in the attachments list with a **Batal** button.
   - Files are NOT uploaded immediately. They remain staged until you press **Simpan**.
   - Press **Simpan** to upload all staged files and save link/notes. On success the edit row closes and attachments appear under the document row.

4. Download / delete:
   - In the **Lihat File** panel use **Download** to download an attachment.
   - Deleting attachments is available in the edit mode (a `Hapus` button appears when editing). Deletion confirms and then removes the file from the server.

Important:
- Staged uploads let you pick multiple files and cancel before saving.
- `Lihat File` is strictly read-only.

---

## 5) UI conventions & colors

- Download buttons: blue with white text.
- Completed phase progress bars show green at 100%.
- Learning model badges are localized and colored.
- The site uses a subtle batik background and translucent cards to preserve legibility.

---

## 6) Troubleshooting

- Upload failures (`Network error`):
  - Confirm the server endpoints exist: `POST /documents/{id}/files` and `PATCH /documents/{id}`.
  - Check PHP/Laravel upload limits (`upload_max_filesize`, `post_max_size`) and server error logs.
- JS toggle issues (Lihat File / Edit not opening):
  - Confirm browser console for JS errors and that the app's scripts loaded.
- Missing event helper dates (`preparation_date`/`report_date`):
  - Run migrations: `php artisan migrate` (backup DB first).

---

## 7) Quick check-list for a new event

- [ ] Log in (Masuk Panitia)
- [ ] Create event (Pelatihan)
- [ ] Open event → review phases and instruction counts
- [ ] Go to `Kelengkapan Dokumen` → Edit a document → `Pilih file` → verify staged file appears → `Simpan`
- [ ] Verify attachments under `Lihat File` and download to confirm

---

## 8) Want this as a different format?

If you prefer this as a Word `.docx` file or a PDF, tell me which format and I can add that to the repo (I can also generate a `.docx` file and place it under the `docs/` folder).

---

Created on: 2025-11-03

