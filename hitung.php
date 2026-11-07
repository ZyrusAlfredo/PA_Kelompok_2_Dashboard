<?php
include 'koneksi.php';

$queryPeriode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$periodeAktif = isset($_GET['id_periode']) ? $_GET['id_periode'] : '';

if (empty($periodeAktif) && mysqli_num_rows($queryPeriode) > 0) {
    $firstPeriode = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_periode FROM periode ORDER BY id_periode DESC LIMIT 1"));
    $periodeAktif = $firstPeriode['id_periode'];
}

$kriteria = [];
$alternatif = [];
$nilai_mentah = [];

if (!empty($periodeAktif)) {
    // 1. Ambil Kriteria (untuk normalisasi bobot & tipe)
    $qKrit = mysqli_query($conn, "SELECT * FROM kriteria WHERE id_periode = '$periodeAktif' ORDER BY kode_kriteria ASC");
    $total_bobot_awal = 0;
    while ($k = mysqli_fetch_assoc($qKrit)) {
        $kriteria[$k['id_kriteria']] = $k;
        $total_bobot_awal += $k['bobot'];
    }

    // 2. Normalisasi Bobot W (Jika totalnya bukan 1 atau 100%)
    foreach ($kriteria as $id_k => $k) {
        $kriteria[$id_k]['bobot_normal'] = ($total_bobot_awal > 0) ? ($k['bobot'] / $total_bobot_awal) : 0;
    }

    // 3. Ambil Alternatif pada Periode
    $qAlt = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_periode = '$periodeAktif' ORDER BY kode_alternatif ASC");
    while ($a = mysqli_fetch_assoc($qAlt)) {
        $alternatif[$a['id_alternatif']] = $a;
    }

    // 4. Ambil Nilai Penilaian Mentah
    if (count($alternatif) > 0 && count($kriteria) > 0) {
        foreach ($alternatif as $id_alt => $alt) {
            foreach ($kriteria as $id_krit => $krit) {
                $qNilai = mysqli_query($conn, "SELECT nilai FROM penilaian WHERE id_alternatif='$id_alt' AND id_kriteria='$id_krit'");
                $dataNilai = mysqli_fetch_assoc($qNilai);
                $nilai_mentah[$id_alt][$id_krit] = ($dataNilai) ? $dataNilai['nilai'] : 0;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Seleksi SAW - SPK</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Desain dasar */
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
        th, td { padding: 10px; text-align: center; border-bottom: 1px solid #efebe9; font-size: 14px;}
        th { background-color: #f5f5f5; color: #5d4037; font-size: 12px; text-transform: uppercase; border-top: 2px solid #8d6e63;}
        
        .rank-1 { background-color: #ffecb3; color: #b71c1c; font-weight: bold; } 
        .rank-2 { background-color: #f5f5f5; font-weight: bold; } 
        .rank-3 { background-color: #efebe9; font-weight: bold; } 
        
        .filter-box { background: #efebe9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center;}
        select { padding: 8px; border: 1px solid #d7ccc8; border-radius: 4px; font-family: inherit;}
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
        <a href="penilaian.php" class="nav-item"><i class="fas fa-edit"></i> Penilaian</a>
        <a href="hitung.php" class="nav-item active"><i class="fas fa-calculator"></i> Seleksi SAW</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2 style="margin: 0; color: #4e342e;">Hasil Laporan Perhitungan SAW</h2>
        </div>

        <!-- Filter Periode -->
        <div class="filter-box">
            <strong style="color: #4e342e;">Tampilkan Hasil Periode:</strong>
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

        <?php if(!empty($periodeAktif) && count($alternatif) > 0 && count($nilai_mentah) > 0) { ?>
        
        <?php
        // --- LOGIKA SAW DIMULAI DI SINI ---

        // 1. Cari Nilai Max/Min untuk setiap Kriteria
        $minMax = [];
        foreach ($kriteria as $id_krit => $krit) {
            $arr_nilai = [];
            foreach ($alternatif as $id_alt => $alt) {
                $arr_nilai[] = $nilai_mentah[$id_alt][$id_krit];
            }
            if ($krit['tipe'] == 'Benefit') {
                $minMax[$id_krit] = max($arr_nilai); // Cari Terbesar
            } else {
                $minMax[$id_krit] = min($arr_nilai); // Cari Terkecil
            }
        }

        // 2. Normalisasi Matriks (R) & Hitung Nilai Preferensi (V)
        $hasilNormalisasi = [];
        $hasilAkhir = [];

        foreach ($alternatif as $id_alt => $alt) {
            $total_skor = 0;
            foreach ($kriteria as $id_krit => $krit) {
                $nilai_asli = $nilai_mentah[$id_alt][$id_krit];
                $nilai_ekstrem = $minMax[$id_krit];
                $r = 0;

                // Rumus Normalisasi SAW
                if ($krit['tipe'] == 'Benefit') {
                    $r = ($nilai_ekstrem != 0) ? ($nilai_asli / $nilai_ekstrem) : 0;
                } else { // Cost
                    $r = ($nilai_asli != 0) ? ($nilai_ekstrem / $nilai_asli) : 0;
                }
                
                $hasilNormalisasi[$id_alt][$id_krit] = $r;
                
                // Kalikan dengan Bobot Normal
                $total_skor += ($r * $krit['bobot_normal']);
            }
            
            $hasilAkhir[] = [
                'nama' => $alt['kode_alternatif'] . ' - ' . $alt['nama_alternatif'],
                'skor' => round($total_skor, 4)
            ];
        }

        // 3. Urutkan Ranking (Descending)
        usort($hasilAkhir, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });
        ?>

        <!-- Tampilkan Tabel Hasil Normalisasi (R) -->
        <div class="crud-box">
            <h3><i class="fas fa-border-all"></i> Matriks Normalisasi (R)</h3>
            <table>
                <tr>
                    <th style="text-align: left;">Kandidat</th>
                    <?php foreach($kriteria as $krit) { ?>
                        <th><?= $krit['kode_kriteria'] ?><br><small>(<?= $krit['tipe'] ?>)</small></th>
                    <?php } ?>
                </tr>
                <?php foreach ($alternatif as $id_alt => $alt) { ?>
                <tr>
                    <td style="text-align: left; font-weight: bold;"><?= $alt['kode_alternatif'] ?></td>
                    <?php foreach($kriteria as $id_krit => $krit) { ?>
                        <td><?= number_format($hasilNormalisasi[$id_alt][$id_krit], 4) ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Tampilkan Tabel Ranking Akhir (V) -->
        <div class="crud-box">
            <h3><i class="fas fa-trophy"></i> Hasil Akhir (Perankingan)</h3>
            <table>
                <tr>
                    <th width="10%">Peringkat</th>
                    <th style="text-align: left;">Nama Kandidat Alternatif</th>
                    <th>Skor Preferensi (V)</th>
                </tr>
                <?php 
                $rank = 1;
                foreach ($hasilAkhir as $hasil) { 
                    $trClass = "";
                    if($rank == 1) $trClass = "class='rank-1'";
                    elseif($rank == 2) $trClass = "class='rank-2'";
                    elseif($rank == 3) $trClass = "class='rank-3'";
                ?>
                <tr <?= $trClass ?>>
                    <td><b><?= $rank ?></b></td>
                    <td style="text-align: left;"><?= $hasil['nama'] ?></td>
                    <td><b><?= number_format($hasil['skor'], 4) ?></b></td>
                </tr>
                <?php $rank++; } ?>
            </table>
        </div>

        <?php } else { ?>
            <div class="crud-box">
                <p style="text-align: center; color: #e74a3b; font-weight: bold;">Data belum lengkap untuk dihitung. Pastikan Kriteria, Alternatif, dan Penilaian sudah diinput untuk periode ini.</p>
            </div>
        <?php } ?>

    </div>

</body>
</html>