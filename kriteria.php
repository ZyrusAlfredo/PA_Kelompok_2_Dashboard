<?php
// kriteria.php
include 'koneksi.php';

// Ambil data periode untuk dropdown filter
$queryPeriode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$periodeAktif = isset($_GET['id_periode']) ? $_GET['id_periode'] : '';

// Jika tidak ada parameter GET, coba set periode aktif ke periode terbaru
if (empty($periodeAktif) && mysqli_num_rows($queryPeriode) > 0) {
    $firstPeriode = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_periode FROM periode ORDER BY id_periode DESC LIMIT 1"));
    $periodeAktif = $firstPeriode['id_periode'];
}

// Proses Hapus Kriteria
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kriteria WHERE id_kriteria = '$id_hapus'");
    header("Location: kriteria.php?id_periode=$periodeAktif");
    exit();
}

// Proses Tambah Kriteria
if (isset($_POST['simpan'])) {
    $id_per = $_POST['id_periode']; // Menangkap ID periode
    $kode = $_POST['kode_kriteria'];
    $nama = $_POST['nama_kriteria'];
    $tipe = $_POST['tipe'];
    $bobot = $_POST['bobot'];
    
    mysqli_query($conn, "INSERT INTO kriteria (id_periode, kode_kriteria, nama_kriteria, tipe, bobot) VALUES ('$id_per', '$kode', '$nama', '$tipe', '$bobot')");
    header("Location: kriteria.php?id_periode=$id_per");
    exit();
}

// Ambil kriteria HANYA untuk periode yang dipilih
$queryKriteria = mysqli_query($conn, "SELECT * FROM kriteria WHERE id_periode = '$periodeAktif' ORDER BY kode_kriteria ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kriteria - SPK SAW</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        .btn:hover { opacity: 0.9; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; align-items: flex-end;}
        .form-group { flex: 1; }
        .form-group label { display:block; margin-bottom:5px; color:#6d4c41; font-size: 14px;}
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #d7ccc8; border-radius: 4px; box-sizing: border-box; font-family: inherit;}
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .bg-benefit { background-color: #1cc88a; }
        .bg-cost { background-color: #e74a3b; }
        .filter-box { background: #efebe9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center;}
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-pie"></i> SPK Metode SAW</div>
        <a href="index.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="periode.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Periode (Seleksi)</a>
        <a href="kriteria.php" class="nav-item active"><i class="fas fa-list"></i> Kriteria</a>
        <a href="alternatif.php" class="nav-item"><i class="fas fa-users"></i> Alternatif</a>
        <a href="penilaian.php" class="nav-item"><i class="fas fa-edit"></i> Penilaian</a>
        <a href="hitung.php" class="nav-item"><i class="fas fa-calculator"></i> Seleksi SAW</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2 style="margin: 0; color: #4e342e;">Kelola Data Kriteria</h2>
        </div>

        <div class="filter-box">
            <strong style="color: #4e342e;">Kelola Kriteria Untuk Kasus/Periode:</strong>
            <form action="" method="GET" style="display: flex; gap: 10px; flex: 1;">
                <select name="id_periode" style="flex: 1;" onchange="this.form.submit()">
                    <option value="">-- Pilih Studi Kasus --</option>
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

        <?php if(!empty($periodeAktif)) { ?>
        <div class="crud-box">
            <h3><i class="fas fa-plus"></i> Tambah Kriteria Baru</h3>
            <form action="" method="POST">
                <input type="hidden" name="id_periode" value="<?= $periodeAktif ?>">
                <div class="form-row">
                    <div class="form-group" style="flex: 0.5;">
                        <label>Kode (Misal: C1)</label>
                        <input type="text" name="kode_kriteria" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label>Nama Kriteria</label>
                        <input type="text" name="nama_kriteria" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Atribut</label>
                        <select name="tipe" required>
                            <option value="Benefit">Benefit</option>
                            <option value="Cost">Cost</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Bobot</label>
                        <input type="number" step="0.01" name="bobot" required>
                    </div>
                    <div class="form-group" style="flex: 0.5;">
                        <button type="submit" name="simpan" class="btn btn-add" style="width: 100%; padding: 11px;"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="crud-box">
            <h3><i class="fas fa-table"></i> Daftar Kriteria pada Kasus Ini</h3>
            <table>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode</th>
                    <th>Nama Kriteria</th>
                    <th width="15%" style="text-align: center;">Tipe</th>
                    <th width="15%" style="text-align: center;">Bobot</th>
                    <th width="15%" style="text-align: center;">Aksi</th>
                </tr>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($queryKriteria)) { 
                    $badgeClass = ($row['tipe'] == 'Benefit') ? 'bg-benefit' : 'bg-cost';
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><b><?= htmlspecialchars($row['kode_kriteria']); ?></b></td>
                    <td><?= htmlspecialchars($row['nama_kriteria']); ?></td>
                    <td style="text-align: center;"><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($row['tipe']); ?></span></td>
                    <td style="text-align: center; font-weight: bold;"><?= htmlspecialchars($row['bobot']); ?></td>
                    <td style="text-align: center;">
                        <a href="?hapus=<?= $row['id_kriteria']; ?>&id_periode=<?= $periodeAktif ?>" class="btn btn-del" onclick="return confirm('Yakin ingin menghapus kriteria ini?');"><i class="fas fa-trash"></i> Hapus</a>
                    </td>
                </tr>
                <?php } ?>
                <?php if(mysqli_num_rows($queryKriteria) == 0) { echo "<tr><td colspan='6' style='text-align:center;'>Belum ada kriteria di kasus/periode ini.</td></tr>"; } ?>
            </table>
        </div>
        <?php } else { ?>
            <div class="crud-box">
                <p style="text-align: center; color: #e74a3b; font-weight: bold;">Silakan pilih Studi Kasus/Periode terlebih dahulu!</p>
            </div>
        <?php } ?>
    </div>
</body>
</html>