<?php
include 'koneksi.php';

$queryPeriode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$periodeAktif = isset($_GET['id_periode']) ? $_GET['id_periode'] : '';

if (empty($periodeAktif) && mysqli_num_rows($queryPeriode) > 0) {
    $firstPeriode = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_periode FROM periode ORDER BY id_periode DESC LIMIT 1"));
    $periodeAktif = $firstPeriode['id_periode'];
}

$queryKriteria = mysqli_query($conn, "SELECT * FROM kriteria WHERE id_periode = '$periodeAktif' ORDER BY kode_kriteria ASC");
$queryAlternatif = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_periode = '$periodeAktif' ORDER BY kode_alternatif ASC");

$kriteria_list = [];
while ($k = mysqli_fetch_assoc($queryKriteria)) {
    $kriteria_list[] = $k;
}

if (isset($_POST['simpan'])) {
    $id_periode = $_POST['id_periode'];
    $nilai_data = $_POST['nilai']; // Bentuk array: $nilai_data[id_alternatif][id_kriteria]

    foreach ($nilai_data as $id_alt => $kriteria_nilai) {
        foreach ($kriteria_nilai as $id_krit => $nilai) {
            // Cek apakah nilai sudah ada sebelumnya
            $cek = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_alternatif='$id_alt' AND id_kriteria='$id_krit'");
            if (mysqli_num_rows($cek) > 0) {
                // Update jika sudah ada
                mysqli_query($conn, "UPDATE penilaian SET nilai='$nilai' WHERE id_alternatif='$id_alt' AND id_kriteria='$id_krit'");
            } else {
                // Insert jika belum ada
                mysqli_query($conn, "INSERT INTO penilaian (id_alternatif, id_kriteria, nilai) VALUES ('$id_alt', '$id_krit', '$nilai')");
            }
        }
    }
    header("Location: penilaian.php?id_periode=$id_periode&sukses=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Penilaian - SPK SAW</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Desain dasar konsisten */
        body { font-family: 'Nunito', sans-serif; background-color: #fdfaf6; margin: 0; display: flex; color: #3e2723; }
        .sidebar { width: 250px; background-color: #4e342e; height: 100vh; padding-top: 20px; position: fixed; }
        .sidebar-brand { text-align: center; font-size: 20px; font-weight: bold; color: #d7ccc8; margin-bottom: 30px; }
        .sidebar-brand i { color: #ffb300; margin-right: 8px;}
        .nav-item { padding: 15px 25px; display: block; color: #bcaaa4; text-decoration: none; font-weight: 600; transition: 0.2s;}
        .nav-item:hover, .nav-item.active { color: #fff; background-color: #5d4037; border-left: 4px solid #ffb300; }
        .nav-item i { width: 25px; }

        .main-content { margin-left: 250px; padding: 30px; width: 100%; box-sizing: border-box; }
        .header { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 20px;}
        
        .crud-box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 30px;}
        .crud-box h3 { margin-top: 0; color: #4e342e; border-bottom: 2px solid #efebe9; padding-bottom: 10px;}
        
        /* Tabel Input Dinamis */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: center; border: 1px solid #efebe9; }
        th { background-color: #f5f5f5; color: #5d4037; font-size: 13px; text-transform: uppercase;}
        
        input[type="number"] { width: 80px; padding: 8px; border: 1px solid #d7ccc8; border-radius: 4px; text-align: center; font-family: inherit; color: #3e2723;}
        input[type="number"]:focus { border-color: #8d6e63; outline: none; }
        
        .btn-simpan { background-color: #1cc88a; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 14px;}
        .btn-simpan:hover { background-color: #17a673; }
        
        .filter-box { background: #efebe9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center;}
        select { padding: 8px; border: 1px solid #d7ccc8; border-radius: 4px; font-family: inherit;}
        .alert-sukses { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;}
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-pie"></i> SPK Metode SAW</div>
        <a href="index.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="periode.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Periode (Seleksi)</a>
        <a href="kriteria.php" class="nav-item"><i class="fas fa-list"></i> Kriteria</a>
        <a href="alternatif.php" class="nav-item"><i class="fas fa-users"></i> Alternatif</a>
        <a href="penilaian.php" class="nav-item active"><i class="fas fa-edit"></i> Penilaian</a>
        <a href="hitung.php" class="nav-item"><i class="fas fa-calculator"></i> Seleksi SAW</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2 style="margin: 0; color: #4e342e;">Input Data Penilaian (Matriks Keputusan)</h2>
        </div>

        <?php if(isset($_GET['sukses'])) { echo "<div class='alert-sukses'><i class='fas fa-check-circle'></i> Data penilaian berhasil disimpan!</div>"; } ?>

        <!-- Filter Periode -->
        <div class="filter-box">
            <strong style="color: #4e342e;">Periode Penilaian:</strong>
            <form action="" method="GET" style="display: flex; gap: 10px; flex: 1;">
                <select name="id_periode" style="flex: 1;" onchange="this.form.submit()">
                    <option value="">-- Pilih Periode --</option>
                    <?php 
                    mysqli_data_seek($queryPeriode, 0);
                    while($p = mysqli_fetch_assoc($queryPeriode)) {
                        $selected = ($p['id_periode'] == $periodeAktif) ? 'selected' : '';
                        echo "<option value='{$p['id_periode']}' $selected>{$p['nama_periode']}</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <?php if(!empty($periodeAktif) && count($kriteria_list) > 0 && mysqli_num_rows($queryAlternatif) > 0) { ?>
        <div class="crud-box">
            <h3><i class="fas fa-edit"></i> Matriks Nilai Alternatif vs Kriteria</h3>
            <form action="" method="POST">
                <input type="hidden" name="id_periode" value="<?= $periodeAktif ?>">
                <table>
                    <tr>
                        <th style="text-align: left; background-color: #e0e0e0;">Kandidat / Alternatif</th>
                        <?php foreach($kriteria_list as $krit) { ?>
                            <th><?= $krit['kode_kriteria'] ?><br><small><?= $krit['nama_kriteria'] ?></small></th>
                        <?php } ?>
                    </tr>
                    
                    <?php 
                    while($alt = mysqli_fetch_assoc($queryAlternatif)) { 
                        $id_alt = $alt['id_alternatif'];
                    ?>
                    <tr>
                        <td style="text-align: left; font-weight: bold; color: #4e342e;"><?= $alt['kode_alternatif'] ?> - <?= $alt['nama_alternatif'] ?></td>
                        <?php 
                        foreach($kriteria_list as $krit) { 
                            $id_krit = $krit['id_kriteria'];
                            // Ambil nilai jika sudah pernah diinput sebelumnya
                            $qNilai = mysqli_query($conn, "SELECT nilai FROM penilaian WHERE id_alternatif='$id_alt' AND id_kriteria='$id_krit'");
                            $dataNilai = mysqli_fetch_assoc($qNilai);
                            $val = ($dataNilai) ? $dataNilai['nilai'] : '';
                        ?>
                            <td><input type="number" step="0.01" name="nilai[<?= $id_alt ?>][<?= $id_krit ?>]" value="<?= $val ?>" required></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </table>
                <div style="text-align: right;">
                    <button type="submit" name="simpan" class="btn-simpan"><i class="fas fa-save"></i> Simpan Penilaian</button>
                </div>
            </form>
        </div>
        <?php } else { ?>
            <div class="crud-box">
                <p style="text-align: center; color: #e74a3b; font-weight: bold;">Data Kriteria atau Alternatif pada periode ini masih kosong. Silakan lengkapi master data terlebih dahulu.</p>
            </div>
        <?php } ?>

    </div>

</body>
</html>