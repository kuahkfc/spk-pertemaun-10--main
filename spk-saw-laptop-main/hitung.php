<?php
// hitung.php (versi perbaikan typo nama tabel)
include 'components/head.php';
include 'koneksi.php';
?>

<body>
    <div class="wrapper d-flex align-items-stretch">
        <?php include 'components/sidebar.php'; ?>
        <div id="content" class="p-4 p-md-5">
            <?php include 'components/navbar.php'; ?>

            <section id="main-content">
                <section class="wrapper">
                    <div class="row">
                        <div class="col-lg-12">
                            <ol class="breadcrumb">
                                <li><i class="fa fa-cogs"></i><a href="hitung.php"> Hitung SAW</a></li>
                            </ol>
                        </div>
                    </div>

                    <?php
// ----- 1) Load bobot kriteria -----
$kq = mysqli_query($conn, "SELECT * FROM saw_kriteria LIMIT 1");
if (!$kq || mysqli_num_rows($kq) == 0) {
    echo "<div class='alert alert-danger'>Bobot kriteria tidak ditemukan di tabel <strong>saw_kriteria</strong>. Silakan isi bobot di menu Kriteria.</div>";
    echo "</section></section></div></div>";
    echo "<script src='js/jquery.min.js'></script><script src='js/popper.js'></script><script src='js/bootstrap.min.js'></script><script src='js/main.js'></script></body></html>";
    exit;
}
$b = mysqli_fetch_assoc($kq);

$cols = [
  'peringkat' => ['type'=>'cost','label'=>'Harga (Rp)'],
  'ukuran'    => ['type'=>'benefit','label'=>'RAM (GB)'],
  'unduhan'   => ['type'=>'benefit','label'=>'SSD (GB)'],
  'aktif'     => ['type'=>'benefit','label'=>'CPU (PassMark)'],
  'manfaat'   => ['type'=>'cost','label'=>'Berat (Kg)'],
];

$weights = [];
foreach ($cols as $col => $m) {
    $weights[$col] = isset($b[$col]) ? floatval($b[$col]) : 0.0;
}

// ----- 2) Ambil data penilaian (matrix X) -----
$pq = mysqli_query($conn, "SELECT * FROM saw_penilaian ORDER BY nama ASC");
if (!$pq) {
    echo "<div class='alert alert-danger'>Gagal mengambil data penilaian: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
    echo "</section></section></div></div>";
    exit;
}
$rows = [];
while ($r = mysqli_fetch_assoc($pq)) $rows[] = $r;

if (count($rows) == 0) {
    echo "<div class='alert alert-warning'>Belum ada data penilaian. Tambahkan data di menu Penilaian terlebih dahulu.</div>";
} else {
    // ----- 3) Cari max/min per kolom -----
    $max = []; $min = [];
    foreach ($cols as $col => $m) {
        $vals = array_map(function($it) use ($col){ return floatval($it[$col]); }, $rows);
        $max[$col] = (count($vals)>0) ? max($vals) : 0;
        $min[$col] = (count($vals)>0) ? min($vals) : 0;
    }

    // ----- 4) TAMPILKAN MATRIX X -----
    echo "<h4>MATRIX X (Nilai Mentah)</h4>";
    echo "<div class='table-responsive'><table class='table table-striped table-bordered'><thead><tr><th>No</th><th>Alternatif</th>";
    foreach ($cols as $c) echo "<th>".htmlspecialchars($c['label'])."</th>";
    echo "</tr></thead><tbody>";
    $no = 0;
    foreach ($rows as $r) {
        echo "<tr>";
        echo "<td>".(++$no)."</td>";
        echo "<td>".htmlspecialchars($r['nama'])."</td>";
        foreach ($cols as $col => $m) {
            echo "<td align='center'>".htmlspecialchars($r[$col])."</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";

    // ----- 5) Normalisasi aman & hitung skor -----
    $norm = []; $scores = [];
    foreach ($rows as $r) {
        $name = $r['nama'];
        $scores[$name] = 0.0;
        $norm[$name] = [];
        foreach ($cols as $col => $meta) {
            $val = floatval($r[$col]);
            if ($meta['type'] == 'benefit') {
                $n = ($max[$col] == 0) ? 0.0 : ($val / $max[$col]);
            } else {
                $n = ($val == 0 || $min[$col] == 0) ? 0.0 : ($min[$col] / $val);
            }
            $norm[$name][$col] = $n;
            $scores[$name] += $n * (isset($weights[$col]) ? $weights[$col] : 0.0);
        }
    }

    // ----- 6) TAMPILKAN NORMALISASI -----
    echo "<h4>Normalisasi (R)</h4>";
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr><th>No</th><th>Alternatif</th>";
    foreach ($cols as $c) echo "<th>".htmlspecialchars($c['label'])."</th>";
    echo "</tr></thead><tbody>";
    $no = 0;
    foreach ($rows as $r) {
        $name = $r['nama'];
        echo "<tr>";
        echo "<td>".(++$no)."</td>";
        echo "<td>".htmlspecialchars($name)."</td>";
        foreach ($cols as $col => $m) {
            echo "<td align='center'>".(isset($norm[$name][$col]) ? round($norm[$name][$col],4) : '0')."</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";

    // ----- 7) Hitung nilai preferensi & simpan ke saw_perankingan (BENAR) -----
    arsort($scores);

    // TRUNCATE the correct table name
    if (!mysqli_query($conn, "TRUNCATE TABLE saw_perankingan")) {
        echo "<div class='alert alert-warning'>Gagal clear tabel perankingan: ".htmlspecialchars(mysqli_error($conn))."</div>";
    }

    // Prepare insert into the correct table name (saw_perankingan)
    $ins = $conn->prepare("INSERT INTO saw_perankingan (`no`,`nama`,`nilai_akhir`) VALUES (?, ?, ?)");
    if (!$ins) {
        echo "<div class='alert alert-danger'>Prepare failed: ".htmlspecialchars(mysqli_error($conn))."</div>";
    } else {
        $no = 1;
        foreach ($scores as $name => $score) {
            $rounded = round($score,4);
            // bind types: i = integer, s = string, d = double
            $ins->bind_param("isd", $no, $name, $rounded);
            $ins->execute();
            $no++;
        }
        $ins->close();
    }

    // ----- 8) TAMPILKAN NILAI PREFERENSI -----
    echo "<h4>Nilai Preferensi (Skor Akhir)</h4>";
    echo "<div class='table-responsive'><table class='table table-hover'><thead><tr><th>No</th><th>Alternatif</th><th>Nilai Akhir</th></tr></thead><tbody>";
    $rankNo = 1;
    foreach ($scores as $name => $score) {
        echo "<tr><td>".$rankNo."</td><td>".htmlspecialchars($name)."</td><td>".round($score,4)."</td></tr>";
        $rankNo++;
    }
    echo "</tbody></table></div>";

    // ----- 9) TAMPILKAN TABEL PERANKINGAN (ambil dari DB) -----
    echo "<h4>Perankingan (Tabel)</h4>";
    $pq2 = mysqli_query($conn, "SELECT * FROM saw_perankingan ORDER BY nilai_akhir DESC");
    echo "<div class='table-responsive'><table class='table table-striped'><thead><tr><th>No</th><th>Nama</th><th>Nilai</th></tr></thead><tbody>";
    if ($pq2 && mysqli_num_rows($pq2) > 0) {
        while ($r = mysqli_fetch_assoc($pq2)) {
            echo "<tr><td>".htmlspecialchars($r['no'])."</td><td>".htmlspecialchars($r['nama'])."</td><td>".htmlspecialchars($r['nilai_akhir'])."</td></tr>";
        }
    } else {
        echo "<tr><td colspan='3'>Belum ada data perankingan.</td></tr>";
    }
    echo "</tbody></table></div>";
} // end else rows exist
?>

                </section>
            </section>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>