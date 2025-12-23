<!doctype html>
<html lang="en">

<?php
include 'components/head.php';
?>

<body>

    <div class="wrapper d-flex align-items-stretch">
        <?php
    include 'components/sidebar.php';
    ?>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5">

            <?php
      include 'components/navbar.php';
      include 'koneksi.php';
      ?>

            <section id="main-content">
                <section class="wrapper">
                    <!--overview start-->
                    <div class="row">
                        <div class="col-lg-12">
                            <ol class="breadcrumb">
                                <li><i class="fa fa-sticky-note"></i><a href="kriteria.php"> Kriteria Penilaian</a></li>
                            </ol>
                        </div>
                    </div>

                    <!--START SCRIPT HITUNG-->
                    <script>
                    function fungsiku() {
                        // ambil angka pertama (1..5) dari setiap opsi
                        var a = (document.getElementById("peringkat_param").value).substring(0, 1);
                        var b = (document.getElementById("ukuran_param").value).substring(0, 1);
                        var c = (document.getElementById("unduhan_param").value).substring(0, 1);
                        var d = (document.getElementById("aktif_param").value).substring(0, 1);
                        var e = (document.getElementById("manfaat_param").value).substring(0, 1);
                        var f = (document.getElementById("kelebihan_param").value).substring(0, 1);
                        var total = Number(a) + Number(b) + Number(c) + Number(d) + Number(e) + Number(f);
                        if (total === 0) {
                            alert('Total bobot pilihan nol, pilih nilai terlebih dahulu.');
                            return;
                        }
                        document.getElementById("peringkat").value = (Number(a) / total).toFixed(2);
                        document.getElementById("ukuran").value = (Number(b) / total).toFixed(2);
                        document.getElementById("unduhan").value = (Number(c) / total).toFixed(2);
                        document.getElementById("aktif").value = (Number(d) / total).toFixed(2);
                        document.getElementById("manfaat").value = (Number(e) / total).toFixed(2);
                        document.getElementById("kelebihan").value = (Number(f) / total).toFixed(2);
                    }
                    </script>
                    <!--END SCRIPT HITUNG-->

                    <?php
          // proses simpan bobot (insert atau update)
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
              // sanitize / cast
              $peringkat = isset($_POST['peringkat']) ? floatval($_POST['peringkat']) : 0;
              $ukuran    = isset($_POST['ukuran']) ? floatval($_POST['ukuran']) : 0;
              $unduhan   = isset($_POST['unduhan']) ? floatval($_POST['unduhan']) : 0;
              $aktif     = isset($_POST['aktif']) ? floatval($_POST['aktif']) : 0;
              $manfaat   = isset($_POST['manfaat']) ? floatval($_POST['manfaat']) : 0;
              $kelebihan = isset($_POST['kelebihan']) ? floatval($_POST['kelebihan']) : 0;

              // cek minimal values
              if ($peringkat == 0 && $ukuran == 0 && $unduhan == 0 && $aktif == 0 && $manfaat == 0 && $kelebihan == 0) {
                  echo "<div class='alert alert-danger'>Tolong isi bobot atau klik tombol Hitung.</div>";
              } else {
                  // cek ada baris di saw_kriteria
                  $checkQ = $conn->query("SELECT COUNT(*) AS c FROM saw_kriteria");
                  $cRow = $checkQ->fetch_assoc();
                  if ($cRow['c'] > 0) {
                      // update baris no=1
                      $stmt = $conn->prepare("UPDATE saw_kriteria SET peringkat=?, ukuran=?, unduhan=?, aktif=?, manfaat=?, kelebihan=? WHERE no=1");
                      $stmt->bind_param("dddddd", $peringkat, $ukuran, $unduhan, $aktif, $manfaat, $kelebihan);
                      if ($stmt->execute()) {
                          echo "<div class='alert alert-success'>Bobot berhasil diperbarui.</div>";
                      } else {
                          echo "<div class='alert alert-danger'>Gagal memperbarui bobot: " . htmlspecialchars($conn->error) . "</div>";
                      }
                      $stmt->close();
                  } else {
                      // insert baru (no = 1)
                      $stmt = $conn->prepare("INSERT INTO saw_kriteria (no, peringkat, ukuran, unduhan, aktif, manfaat, kelebihan) VALUES (1,?,?,?,?,?,?)");
                      $stmt->bind_param("ddddd d", $peringkat, $ukuran, $unduhan, $aktif, $manfaat, $kelebihan);
                      // Note: bind_param with "ddddd d" causes space; avoid — use "dddddd" instead
                      // We'll re-bind correctly:
                      $stmt->close();
                      $stmt = $conn->prepare("INSERT INTO saw_kriteria (no, peringkat, ukuran, unduhan, aktif, manfaat, kelebihan) VALUES (1,?,?,?,?,?,?)");
                      $stmt->bind_param("dddddd", $peringkat, $ukuran, $unduhan, $aktif, $manfaat, $kelebihan);
                      if ($stmt->execute()) {
                          echo "<div class='alert alert-success'>Bobot berhasil disimpan.</div>";
                      } else {
                          echo "<div class='alert alert-danger'>Gagal menyimpan bobot: " . htmlspecialchars($conn->error) . "</div>";
                      }
                      $stmt->close();
                  }
              }
          }
          ?>

                    <!--start inputan-->
                    <form class="form-validate form-horizontal" id="register_form" method="post" action="">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><b>Kriteria</b></label>
                            <div class="col-sm-3">
                                <label><b>Bobot (pilihan)</b></label>
                            </div>
                            <div class="col-sm-2">
                                <label><b>Hasil Bobot (otomatis)</b></label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Harga (Peringkat)</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="peringkat_param" id="peringkat_param">
                                    <option>1. Sangat Rendah</option>
                                    <option>2. Rendah</option>
                                    <option>3. Cukup</option>
                                    <option>4. Tinggi</option>
                                    <option>5. Sangat Tinggi</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control" name="peringkat" id="peringkat" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">RAM (Ukuran)</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="ukuran_param" id="ukuran_param">
                                    <option>1. Sangat Rendah</option>
                                    <option>2. Rendah</option>
                                    <option>3. Cukup</option>
                                    <option>4. Tinggi</option>
                                    <option>5. Sangat Tinggi</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control" name="ukuran" id="ukuran" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">SSD (Unduhan)</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="unduhan_param" id="unduhan_param">
                                    <option>1. Sangat Rendah</option>
                                    <option>2. Rendah</option>
                                    <option>3. Cukup</option>
                                    <option>4. Tinggi</option>
                                    <option>5. Sangat Tinggi</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control" name="unduhan" id="unduhan" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">CPU (Pengguna Aktif)</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="aktif_param" id="aktif_param">
                                    <option>1. Sangat Rendah</option>
                                    <option>2. Rendah</option>
                                    <option>3. Cukup</option>
                                    <option>4. Tinggi</option>
                                    <option>5. Sangat Tinggi</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control" name="aktif" id="aktif" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Berat (Manfaat)</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="manfaat_param" id="manfaat_param">
                                    <option>1. Sangat Rendah</option>
                                    <option>2. Rendah</option>
                                    <option>3. Cukup</option>
                                    <option>4. Tinggi</option>
                                    <option>5. Sangat Tinggi</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control" name="manfaat" id="manfaat" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kelebihan (unused)</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="kelebihan_param" id="kelebihan_param">
                                    <option>1. Sangat Rendah</option>
                                    <option>2. Rendah</option>
                                    <option>3. Cukup</option>
                                    <option>4. Tinggi</option>
                                    <option>5. Sangat Tinggi</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control" name="kelebihan" id="kelebihan" readonly>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-outline-success" type="button" id="hitung" onclick="fungsiku()"
                                    name="hitung"><i class="fa fa-calculator"></i> Hitung</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button class="btn btn-outline-primary" type="submit" name="submit"><i
                                    class="fa fa-save"></i> Simpan Bobot</button>
                        </div>
                    </form>

                    <hr>

                    <h5>Bobot Saat Ini</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-arrow-down"></i> Harga (peringkat)</th>
                                <th><i class="fa fa-arrow-down"></i> RAM (ukuran)</th>
                                <th><i class="fa fa-arrow-down"></i> SSD (unduhan)</th>
                                <th><i class="fa fa-arrow-down"></i> CPU (aktif)</th>
                                <th><i class="fa fa-arrow-down"></i> Berat (manfaat)</th>
                                <th><i class="fa fa-arrow-down"></i> Kelebihan</th>
                                <th><i class="fa fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
              // tampilkan bobot saat ini (jika ada)
              $sql = "SELECT * FROM saw_kriteria LIMIT 1";
              $res = $conn->query($sql);
              if ($res && $res->num_rows > 0) {
                $r = $res->fetch_assoc();
                echo "<tr>
                        <td align='center'>".htmlspecialchars($r['peringkat'])."</td>
                        <td align='center'>".htmlspecialchars($r['ukuran'])."</td>
                        <td align='center'>".htmlspecialchars($r['unduhan'])."</td>
                        <td align='center'>".htmlspecialchars($r['aktif'])."</td>
                        <td align='center'>".htmlspecialchars($r['manfaat'])."</td>
                        <td align='center'>".htmlspecialchars($r['kelebihan'])."</td>
                        <td>
                          <div class='btn-group'>
                            <a class='btn btn-danger' href='kriteria_hapus.php?id=1' onclick=\"return confirm('Hapus bobot saat ini?')\"><i class='fa fa-close'></i></a>
                          </div>
                        </td>
                      </tr>";
              } else {
                echo "<tr><td colspan='7'>Belum ada bobot yang disimpan.</td></tr>";
              }
              ?>
                        </tbody>
                    </table>

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