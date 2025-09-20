<?php
session_start();

// Load data dari file JSON
function loadData($filename) {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true);
    }
    return [];
}

// Simpan data ke file JSON
function saveData($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Inisialisasi file
$jadwalFile = "jadwal.json";
$tugasFile = "tugas.json";
$jadwal = loadData($jadwalFile);
$tugas = loadData($tugasFile);

// Tambah jadwal kuliah
if (isset($_POST['tambah_jadwal'])) {
    $jadwal[] = [
        "mata_kuliah" => $_POST['mata_kuliah'],
        "hari" => $_POST['hari'],
        "jam" => $_POST['jam'],
        "ruangan" => $_POST['ruangan'],
        "dosen" => $_POST['dosen'],
        "sks" => intval($_POST['sks'])
    ];
    saveData($jadwalFile, $jadwal);
}

// Tambah tugas
if (isset($_POST['tambah_tugas'])) {
    $tugas[] = [
        "mata_kuliah" => $_POST['mata_kuliah'],
        "deskripsi" => $_POST['deskripsi'],
        "deadline" => $_POST['deadline'],
        "status" => "Belum Selesai"
    ];
    saveData($tugasFile, $tugas);
}

// Update status tugas
if (isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    if (isset($tugas[$id])) {
        $tugas[$id]['status'] = "Selesai";
        saveData($tugasFile, $tugas);
    }
}

// Hitung total SKS
$total_sks = 0;
foreach ($jadwal as $j) {
    $total_sks += $j['sks'];
}

// Ambil jadwal hari ini
$hari_ini = date("l"); // English day
$mapHari = [
    "Monday" => "Senin",
    "Tuesday" => "Selasa",
    "Wednesday" => "Rabu",
    "Thursday" => "Kamis",
    "Friday" => "Jumat",
    "Saturday" => "Sabtu",
    "Sunday" => "Minggu"
];
$jadwal_hari_ini = array_filter($jadwal, fn($j) => $j['hari'] == $mapHari[$hari_ini]);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Agenda Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Agenda Digital Mahasiswa</h1>
    <h2>Dashboard</h2>
    <p>Total SKS: <?= $total_sks ?></p>

    <h3>Jadwal Hari Ini (<?= $mapHari[$hari_ini] ?>)</h3>
    <ul>
        <?php foreach ($jadwal_hari_ini as $j): ?>
            <li><?= $j['mata_kuliah'] ?> - <?= $j['jam'] ?> (<?= $j['ruangan'] ?>, <?= $j['dosen'] ?>)</li>
        <?php endforeach; ?>
    </ul>

    <h3>Tugas Belum Selesai</h3>
    <ul>
        <?php foreach ($tugas as $i => $t): ?>
            <?php if ($t['status'] == "Belum Selesai"): ?>
                <li><?= $t['mata_kuliah'] ?> - <?= $t['deskripsi'] ?> (Deadline: <?= $t['deadline'] ?>) 
                    <a href="?selesai=<?= $i ?>">[Selesai]</a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <h2>Tambah Jadwal Kuliah</h2>
    <form method="POST">
        <input type="text" name="mata_kuliah" placeholder="Mata Kuliah" required>
        <input type="text" name="hari" placeholder="Hari (Senin..Minggu)" required>
        <input type="text" name="jam" placeholder="Jam" required>
        <input type="text" name="ruangan" placeholder="Ruangan" required>
        <input type="text" name="dosen" placeholder="Dosen Pengampu" required>
        <input type="number" name="sks" placeholder="SKS" required>
        <button type="submit" name="tambah_jadwal">Tambah Jadwal</button>
    </form>

    <h2>Tambah Tugas</h2>
    <form method="POST">
        <input type="text" name="mata_kuliah" placeholder="Mata Kuliah" required>
        <input type="text" name="deskripsi" placeholder="Deskripsi Tugas" required>
        <input type="date" name="deadline" required>
        <button type="submit" name="tambah_tugas">Tambah Tugas</button>
    </form>
</body>
</html>
