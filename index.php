<?php
include 'koneksi.php';

$totalPeriode = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM periode"))['total'];
$totalKriteria = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kriteria"))['total'];
$totalAlternatif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alternatif"))['total'];

$queryPeriode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$periodeAktif = isset($_GET['id_periode']) ? $_GET['id_periode'] : '';

if (empty($periodeAktif) && mysqli_num_rows($queryPeriode) > 0) {
    $firstPeriode = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_periode FROM periode ORDER BY id_periode DESC LIMIT 1"));
    $periodeAktif = $firstPeriode['id_periode'];
}

$labels_chart = [];
$scores_chart = [];

if (!empty($periodeAktif)) {
    $kriteria = [];
    $alternatif = [];
    $nilai_mentah = [];

    // Ambil Kriteria
    $qKrit = mysqli_query($conn, "SELECT * FROM kriteria WHERE id_periode = '$periodeAktif' ORDER BY kode_kriteria ASC");
    $total_bobot = 0;
    while ($k = mysqli_fetch_assoc($qKrit)) {
        $kriteria[$k['id_kriteria']] = $k;
        $total_bobot += $k['bobot'];
    }

    foreach ($kriteria as $id_k => $k) {
        $kriteria[$id_k]['bobot_normal'] = ($total_bobot > 0) ? ($k['bobot'] / $total_bobot) : 0;
    }

    // Ambil Alternatif
    $qAlt = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_periode = '$periodeAktif'");
    while ($a = mysqli_fetch_assoc($qAlt)) {
        $alternatif[$a['id_alternatif']] = $a;
    }

    // Ambil Nilai Mentah & Cari Max/Min
    if (count($alternatif) > 0 && count($kriteria) > 0) {
        $minMax = [];
        foreach ($kriteria as $id_krit => $krit) {
            $arr_nilai = [];
            foreach ($alternatif as $id_alt => $alt) {
                $qNilai = mysqli_query($conn, "SELECT nilai FROM penilaian WHERE id_alternatif='$id_alt' AND id_kriteria='$id_krit'");
                $dataNilai = mysqli_fetch_assoc($qNilai);
                $nilai = ($dataNilai) ? $dataNilai['nilai'] : 0;
                $nilai_mentah[$id_alt][$id_krit] = $nilai;
                $arr_nilai[] = $nilai;
            }
            $minMax[$id_krit] = ($krit['tipe'] == 'Benefit') ? max($arr_nilai) : min($arr_nilai);
        }

        // Normalisasi & Perhitungan
        $hasilAkhir = [];
        foreach ($alternatif as $id_alt => $alt) {
            $total_skor = 0;
            foreach ($kriteria as $id_krit => $krit) {
                $nilai_asli = $nilai_mentah[$id_alt][$id_krit];
                $nilai_ekstrem = $minMax[$id_krit];
                $r = 0;
                if ($krit['tipe'] == 'Benefit') {
                    $r = ($nilai_ekstrem != 0) ? ($nilai_asli / $nilai_ekstrem) : 0;
                } else {
                    $r = ($nilai_asli != 0) ? ($nilai_ekstrem / $nilai_asli) : 0;
                }
                $total_skor += ($r * $krit['bobot_normal']);
            }
            $hasilAkhir[] = [
                'nama' => $alt['kode_alternatif'],
                'skor' => round($total_skor, 4)
            ];
        }

        // Urutkan dan ambil Top 5 untuk grafik
        usort($hasilAkhir, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        $top5 = array_slice($hasilAkhir, 0, 5);
        foreach ($top5 as $item) {
            $labels_chart[] = $item['nama'];
            $scores_chart[] = $item['skor'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SPK SAW - Kelompok 2</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Desain Utama (Desktop) */
        body { font-family: 'Nunito', sans-serif; background-color: #fdfaf6; margin: 0; display: flex; color: #3e2723; flex-wrap: wrap; }
        .sidebar { width: 250px; background-color: #4e342e; height: 100vh; padding-top: 20px; position: fixed; }
        .sidebar-brand { text-align: center; font-size: 20px; font-weight: bold; color: #d7ccc8; margin-bottom: 30px; }
        .sidebar-brand i { color: #ffb300; margin-right: 8px;}
        .nav-item { padding: 15px 25px; display: block; color: #bcaaa4; text-decoration: none; font-weight: 600; transition: 0.2s; }
        .nav-item:hover, .nav-item.active { color: #fff; background-color: #5d4037; border-left: 4px solid #ffb300; }
        .nav-item i { width: 25px; }

        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); box-sizing: border-box; }
        .header { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .header h2 { margin: 0; color: #4e342e; }

        .row-cards { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 25px;}
        .card-stat { background: #fff; flex: 1; min-width: 200px; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid; display: flex; justify-content: space-between; align-items: center;}
        .card-stat.gold { border-left-color: #ffb300; }
        .card-stat.brown { border-left-color: #8d6e63; }
        .card-stat.dark { border-left-color: #4e342e; }
        .stat-title { font-size: 13px; font-weight: bold; color: #6d4c41; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 28px; font-weight: bold; color: #3e2723; margin: 0; }
        .stat-icon { font-size: 35px; color: #efebe9; }
        
        .row-dashboard { display: flex; gap: 20px; flex-wrap: wrap; }
        .col-chart { flex: 2; min-width: 300px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .col-team { flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border-top: 4px solid #ffb300;}
        
        .box-title { margin-top: 0; color: #4e342e; border-bottom: 2px solid #efebe9; padding-bottom: 10px; font-size: 16px;}
        
        .team-list { list-style: none; padding: 0; margin: 0; }
        .team-list li { padding: 12px 0; border-bottom: 1px dashed #d7ccc8; display: flex; align-items: center; font-size: 14px; font-weight: 600;}
        .team-list li:last-child { border-bottom: none; }
        .team-list li i { color: #8d6e63; font-size: 20px; margin-right: 15px; }
        .nim-badge { display: block; font-size: 12px; color: #858796; font-weight: normal; margin-top: 3px;}
        
        .filter-select { padding: 8px; border: 1px solid #d7ccc8; border-radius: 4px; font-family: inherit; margin-bottom: 15px; width: 100%; box-sizing: border-box;}

        /* --- DESAIN RESPONSIVE (HP & TABLET) --- */
        @media (max-width: 768px) {
            body { display: block; }
            
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: relative; 
                padding: 15px 0; 
            }
            .sidebar-brand { margin-bottom: 10px; }
            
            .nav-item { 
                display: inline-block; 
                padding: 10px 12px; 
                font-size: 13px; 
                border-left: none;
                border-bottom: 2px solid transparent;
            }
            .nav-item:hover, .nav-item.active {
                border-left: none;
                border-bottom: 2px solid #ffb300;
            }
            .nav-item i { margin-right: 5px; width: auto; }

            .main-content { 
                margin-left: 0; 
                width: 100%; 
                padding: 15px; 
            }
            
            .header h2 { font-size: 18px; }
            
            .row-cards { flex-direction: column; gap: 15px; }
            .row-dashboard { flex-direction: column; gap: 15px; }
            .col-chart, .col-team { flex: unset; width: 100%; box-sizing: border-box;}
        }

        @media (max-width: 480px) {
            .nav-item { 
                display: block; 
                text-align: center;
                border-bottom: 1px solid #5d4037;
            }
            .nav-item:hover, .nav-item.active {
                border-bottom: 1px solid #ffb300;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-chart-pie"></i> SPK Metode SAW</div>
        <a href="index.php" class="nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="periode.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Periode (Seleksi)</a>
        <a href="kriteria.php" class="nav-item"><i class="fas fa-list"></i> Kriteria</a>
        <a href="alternatif.php" class="nav-item"><i class="fas fa-users"></i> Alternatif</a>
        <a href="penilaian.php" class="nav-item"><i class="fas fa-edit"></i> Penilaian</a>
        <a href="hitung.php" class="nav-item"><i class="fas fa-calculator"></i> Seleksi SAW</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Sistem Pendukung Keputusan (SAW) - Dashboard Utama</h2>
        </div>

        <div class="row-cards">
            <div class="card-stat gold">
                <div>
                    <div class="stat-title">Total Periode</div>
                    <p class="stat-value"><?= $totalPeriode ?></p>
                </div>
                <i class="fas fa-calendar-alt stat-icon"></i>
            </div>
            <div class="card-stat brown">
                <div>
                    <div class="stat-title">Total Kriteria</div>
                    <p class="stat-value"><?= $totalKriteria ?></p>
                </div>
                <i class="fas fa-list stat-icon"></i>
            </div>
            <div class="card-stat dark">
                <div>
                    <div class="stat-title">Total Alternatif</div>
                    <p class="stat-value"><?= $totalAlternatif ?></p>
                </div>
                <i class="fas fa-users stat-icon"></i>
            </div>
        </div>

        <div class="row-dashboard">
            <!-- Kolom Kiri: Chart KPI -->
            <div class="col-chart">
                <h3 class="box-title"><i class="fas fa-chart-bar"></i> Visualisasi Hasil: Top 5 Kandidat</h3>
                <form action="" method="GET">
                    <select name="id_periode" class="filter-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Studi Kasus/Periode --</option>
                        <?php 
                        mysqli_data_seek($queryPeriode, 0);
                        while($p = mysqli_fetch_assoc($queryPeriode)) {
                            $selected = ($p['id_periode'] == $periodeAktif) ? 'selected' : '';
                            echo "<option value='{$p['id_periode']}' $selected>{$p['nama_periode']}</option>";
                        }
                        ?>
                    </select>
                </form>
                
                <?php if(count($labels_chart) > 0) { ?>
                    <!-- Container tambahan agar chart tidak meluber di HP -->
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="rankingChart"></canvas>
                    </div>
                <?php } else { ?>
                    <p style="text-align: center; color: #858796; padding: 40px 0;">Data belum lengkap atau perhitungan belum tersedia untuk periode ini.</p>
                <?php } ?>
            </div>

            <!-- Kolom Kanan: Identitas Tim -->
            <div class="col-team">
                <h3 class="box-title"><i class="fas fa-users-cog"></i> Tim Developer (Kelompok 2)</h3>
                <p style="font-size: 13px; color: #6d4c41; margin-bottom: 15px;">Project Akhir Praktikum Sistem Pendukung Keputusan (SAW)</p>
                <ul class="team-list">
                    <li>
                        <i class="fas fa-user-circle"></i>
                        <div>Zyrus Alfredo Randan Malinggato <span class="nim-badge">NIM: 2409116120</span></div>
                    </li>
                    <li>
                        <i class="fas fa-user-circle"></i>
                        <div>Jemis Movid <span class="nim-badge">NIM: 2409116070</span></div>
                    </li>
                    <li>
                        <i class="fas fa-user-circle"></i>
                        <div>Maria Claudia Meo <span class="nim-badge">NIM: 2409116108</span></div>
                    </li>
                    <li>
                        <i class="fas fa-user-circle"></i>
                        <div>Fathinatuz Zaina <span class="nim-badge">NIM: 2409116016</span></div>
                    </li>
                    <li>
                        <i class="fas fa-user-circle"></i>
                        <div>Dimas Aji Mukti <span class="nim-badge">NIM: 2409116107</span></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Script Rendering Chart.js -->
    <script>
        <?php if(count($labels_chart) > 0) { ?>
        const ctx = document.getElementById('rankingChart').getContext('2d');
        const rankingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels_chart) ?>,
                datasets: [{
                    label: 'Skor Preferensi (V)',
                    data: <?= json_encode($scores_chart) ?>,
                    backgroundColor: [
                        '#ffb300', // Peringkat 1 (Emas)
                        '#8d6e63', // Peringkat 2
                        '#8d6e63',
                        '#8d6e63',
                        '#8d6e63'
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, /* Penting agar chart bisa mengecil di HP */
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1 // Nilai maksimal normalisasi SAW
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        <?php } ?>
    </script>
</body>
</html>
