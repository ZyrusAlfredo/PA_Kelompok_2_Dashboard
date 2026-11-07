<?php
include 'koneksi.php';

$queryPeriode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$periodeAktif = isset($_GET['id_periode']) ? $_GET['id_periode'] : '';

if (empty($periodeAktif) && mysqli_num_rows($queryPeriode) > 0) {
    $firstPeriode = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_periode FROM periode ORDER BY id_periode DESC LIMIT 1"));
    $periodeAktif = $firstPeriode['id_periode'];
}

if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM alternatif WHERE id_alternatif = '$id_hapus'");
    header("Location: alternatif.php?id_periode=$periodeAktif");
    exit();
}

if (isset($_POST['simpan'])) {
    $id_per = $_POST['id_periode'];
    $kode = $_POST['kode_alternatif'];
    $nama = $_POST['nama_alternatif'];
    
    mysqli_query($conn, "INSERT INTO alternatif (id_periode, kode_alternatif, nama_alternatif) VALUES ('$id_per', '$kode', '$nama')");
    header("Location: alternatif.php?id_periode=$id_per");
    exit();
}

$queryAlternatif = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_periode = '$periodeAktif' ORDER BY kode_alternatif ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Alternatif - SPK SAW</title>
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
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #efebe9; }
        th { background-color: #f5f5f5; color: #5d4037; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; text-decoration: none; font-size: 13px;}
        .btn-add { background-color: #1cc88a; }
        .btn-del { background-color: #e74a3b; }
        .btn-filter { background-color: #4e73df; }
        .btn:hover { opacity: 0.9; }
        
        /* Form Layout */
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; align-items: flex-end;}
        .form-group { flex: 1; }
        .form-group label { display:block; margin-bottom:5px; color:#6d4c41; font-size: 14px;}
        input[type="text"], select { width: 100%; padding: 10px; border: 1px solid #d7ccc8; border-radius: 4px; box-sizing: border-box; font-family: inherit;}
        
        /* Filter Box */
        .filter-box { background: #efebe9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center;}
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-pie"></i> SPK Metode SAW</div>
        <a href="index.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="periode.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Periode (Seleksi)</a>
        <a href="kriteria.php" class="nav-item"><i class="fas fa-list"></i> Kriteria</a>
        <a href="alternatif.php" class="nav-item active"><i class="fas fa-users"></i> Alternatif</a>
        <a href="penilaian.php" class="nav-item"><i class="fas fa-edit"></i> Penilaian</a>
        <a href="hitung.php" class="nav-item"><i class="fas fa-calculator"></i> Seleksi SAW</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2 style="margin: 0; color: #4e342e;">Kelola Kandidat Alternatif</h2>
        </div>

        <!-- Filter Berdasarkan Periode -->
        <div class="filter-box">
            <strong style="color: #4e342e;">Tampilkan data untuk periode:</strong>
            <form action="" method="GET" style="display: flex; gap: 10px; flex: 1;">
                <select name="id_periode" style="flex: 1;" onchange="this.form.submit()">
                    <option value="">-- Pilih Periode --</option>
                    <?php 
                    mysqli_data_seek($queryPeriode, 0); // Reset pointer
                    while($p = mysqli_fetch_assoc($queryPeriode)) {
                        $selected = ($p['id_periode'] == $periodeAktif) ? 'selected' : '';
                        echo "<option value='{$p['id_periode']}' $selected>{$p['nama_periode']}</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <?php if(!empty($periodeAktif)) { ?>
        <div class="crud-box">
            <h3><i class="fas fa-plus"></i> Tambah Kandidat (Alternatif)</h3>
            <form action="" method="POST">
                <input type="hidden" name="id_periode" value="<?= $periodeAktif ?>">
                <div class="form-row">
                    <div class="form-group" style="flex: 0.5;">
                        <label>Kode (Misal: S1)</label>
                        <input type="text" name="kode_alternatif" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label>Nama Kandidat / Supplier</label>
                        <input type="text" name="nama_alternatif" required>
                    </div>
                    <div class="form-group" style="flex: 0.5;">
                        <button type="submit" name="simpan" class="btn btn-add" style="width: 100%; padding: 11px;"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="crud-box">
            <h3><i class="fas fa-users"></i> Daftar Kandidat pada Periode Ini</h3>
            <table>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Kandidat</th>
                    <th>Nama Kandidat</th>
                    <th width="15%" style="text-align: center;">Aksi</th>
                </tr>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($queryAlternatif)) { 
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><b><?= htmlspecialchars($row['kode_alternatif']); ?></b></td>
                    <td><?= htmlspecialchars($row['nama_alternatif']); ?></td>
                    <td style="text-align: center;">
                        <a href="?hapus=<?= $row['id_alternatif']; ?>&id_periode=<?= $periodeAktif ?>" class="btn btn-del" onclick="return confirm('Yakin ingin menghapus kandidat ini?');"><i class="fas fa-trash"></i> Hapus</a>
                    </td>
                </tr>
                <?php } ?>
                <?php if(mysqli_num_rows($queryAlternatif) == 0) { echo "<tr><td colspan='4' style='text-align:center;'>Belum ada kandidat di periode ini.</td></tr>"; } ?>
            </table>
        </div>
        <?php } else { ?>
            <div class="crud-box">
                <p style="text-align: center; color: #e74a3b; font-weight: bold;">Silakan pilih atau buat Periode Seleksi terlebih dahulu!</p>
            </div>
        <?php } ?>

    </div>

</body>
</html>