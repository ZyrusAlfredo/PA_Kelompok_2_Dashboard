<?php
include 'koneksi.php';

if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM periode WHERE id_periode = '$id_hapus'");
    header("Location: periode.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_periode'];
    mysqli_query($conn, "INSERT INTO periode (nama_periode) VALUES ('$nama')");
    header("Location: periode.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Periode - SPK SAW</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Desain dasar sama dengan index.php untuk konsistensi */
        body { font-family: 'Nunito', sans-serif; background-color: #fdfaf6; margin: 0; display: flex; color: #3e2723; }
        .sidebar { width: 250px; background-color: #4e342e; height: 100vh; padding-top: 20px; position: fixed; }
        .sidebar-brand { text-align: center; font-size: 20px; font-weight: bold; color: #d7ccc8; margin-bottom: 30px; }
        .sidebar-brand i { color: #ffb300; margin-right: 8px;}
        .nav-item { padding: 15px 25px; display: block; color: #bcaaa4; text-decoration: none; font-weight: 600; transition: 0.2s;}
        .nav-item:hover, .nav-item.active { color: #fff; background-color: #5d4037; border-left: 4px solid #ffb300; }
        .nav-item i { width: 25px; }

        .main-content { margin-left: 250px; padding: 30px; width: 100%; box-sizing: border-box; }
        .header { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;}
        
        /* Desain Box CRUD */
        .crud-box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 30px;}
        .crud-box h3 { margin-top: 0; color: #4e342e; border-bottom: 2px solid #efebe9; padding-bottom: 10px;}
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #efebe9; }
        th { background-color: #f5f5f5; color: #5d4037; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; text-decoration: none; font-size: 13px;}
        .btn-add { background-color: #1cc88a; } /* Hijau seperti referensi Aslab */
        .btn-del { background-color: #e74a3b; }
        .btn:hover { opacity: 0.9; }
        
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #d7ccc8; border-radius: 4px; margin-bottom: 15px; box-sizing: border-box; font-family: inherit;}
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-pie"></i> SPK Metode SAW</div>
        <a href="index.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="periode.php" class="nav-item active"><i class="fas fa-calendar-alt"></i> Periode (Seleksi)</a>
        <a href="kriteria.php" class="nav-item"><i class="fas fa-list"></i> Kriteria</a>
        <a href="alternatif.php" class="nav-item"><i class="fas fa-users"></i> Alternatif</a>
        <a href="penilaian.php" class="nav-item"><i class="fas fa-edit"></i> Penilaian</a>
        <a href="hitung.php" class="nav-item"><i class="fas fa-calculator"></i> Seleksi SAW</a>
    </div>

    <!-- Konten Utama -->
    <div class="main-content">
        <div class="header">
            <h2 style="margin: 0; color: #4e342e;">Daftar Periode Seleksi</h2>
        </div>

        <!-- Form Tambah Periode -->
        <div class="crud-box">
            <h3><i class="fas fa-plus"></i> Tambah Periode Baru</h3>
            <form action="" method="POST">
                <label style="display:block; margin-bottom:5px; color:#6d4c41;">Nama Periode (Contoh: Seleksi Supplier Nov 2026)</label>
                <input type="text" name="nama_periode" required placeholder="Masukkan nama periode...">
                <button type="submit" name="simpan" class="btn btn-add"><i class="fas fa-save"></i> Simpan Data</button>
            </form>
        </div>

        <!-- Tabel Tampil Periode -->
        <div class="crud-box">
            <h3><i class="fas fa-table"></i> Data Periode Tersimpan</h3>
            <table>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Periode Seleksi</th>
                    <th width="15%" style="text-align: center;">Aksi</th>
                </tr>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($query)) { 
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_periode']); ?></td>
                    <td style="text-align: center;">
                        <a href="?hapus=<?= $row['id_periode']; ?>" class="btn btn-del" onclick="return confirm('Yakin ingin menghapus periode ini? Seluruh data kandidat di periode ini juga akan terhapus!');"><i class="fas fa-trash"></i> Hapus</a>
                    </td>
                </tr>
                <?php } ?>
                <?php if(mysqli_num_rows($query) == 0) { echo "<tr><td colspan='3' style='text-align:center;'>Belum ada data periode.</td></tr>"; } ?>
            </table>
        </div>

    </div>

</body>
</html>