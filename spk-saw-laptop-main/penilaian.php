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
                                <li><i class="fa fa-list-ol"></i><a href="penilaian.php"> Penilaian</a></li>
                            </ol>
                        </div>
                    </div>

                    <?php
          // Handle form submit (insert atau update)
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
              // ambil & sanitize input
              $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
              $peringkat = isset($_POST['peringkat']) ? floatval($_POST['peringkat']) : 0; // Harga (Rp)
              $ukuran    = isset($_POST['ukuran']) ? floatval($_POST['ukuran']) : 0;       // RAM (GB)
              $unduhan   = isset($_POST['unduhan']) ? floatval($_POST['unduhan']) : 0;     // SSD (GB)
              $aktif     = isset($_POST['aktif']) ? floatval($_POST['aktif']) : 0;         // CPU (PassMark)
              $manfaat   = isset($_POST['manfaat']) ? floatval($_POST['manfaat']) : 0;     // Berat (Kg)
              $kelebihan = 0; // unused in our laptop model

              if ($nama === '' || $peringkat === 0) {
                  echo "<div class='alert alert-danger'>Tolong pilih Alternatif dan isi minimal Harga (Rp).</div>";
              } else {
                  // cek ada record penilaian untuk alternatif ini?
                  $chk = $conn->prepare("SELECT nama FROM saw_penilaian WHERE nama = ? LIMIT 1");
                  $chk->bind_param("s", $nama);
                  $chk->execute();
                  $cres = $chk->get_result();

                  if ($cres && $cres->num_rows > 0) {
                      // update
                      $u = $conn->prepare("UPDATE saw_penilaian SET peringkat=?, ukuran=?, unduhan=?, aktif=?, manfaat=?, kelebihan=? WHERE nama=?");
                      $u->bind_param("dddddds", $peringkat, $ukuran, $unduhan, $aktif, $manfaat, $kelebihan, $nama);
                      if ($u->execute()) {
                          echo "<div class='alert alert-success'>Nilai untuk <strong>" . htmlspecialchars($nama) . "</strong> berhasil diupdate.</div>";
                      } else {
                          echo "<div class='alert alert-danger'>Gagal update: " . htmlspecialchars($conn->error) . "</div>";
                      }
                      $u->close();
                  } else {
                      // insert
                      $i = $conn->prepare("INSERT INTO saw_penilaian (nama, peringkat, ukuran, unduhan, aktif, manfaat, kelebihan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                      $i->bind_param("sdddddd", $nama, $peringkat, $ukuran, $unduhan, $aktif, $manfaat, $kelebihan);
                      if ($i->execute()) {
                          echo "<div class='alert alert-success'>Penilaian untuk <strong>" . htmlspecialchars($nama) . "</strong> berhasil disimpan.</div>";
                      } else {
                          echo "<div class='alert alert-danger'>Gagal menyimpan: " . htmlspecialchars($conn->error) . "</div>";
                      }
                      $i->close();
                  }
                  $chk->close();
              }
          }
          ?>

                    <!--start inputan-->
                    <form method="POST" action="">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Alternatif Laptop</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="nama" required>
                                    <option value="">-- Pilih Laptop --</option>
                                    <?php
                  // load nama alternatif dari saw_aplikasi
                  $sql = "SELECT nama FROM saw_aplikasi ORDER BY nama ASC";
                  $hasil = $conn->query($sql);
                  if ($hasil && mysqli_num_rows($hasil) > 0) {
                      while ($r = mysqli_fetch_assoc($hasil)) {
                          echo '<option value="'.htmlspecialchars($r['nama']).'">'.htmlspecialchars($r['nama']).'</option>';
                      }
                  } else {
                      echo '<option value="">Belum ada alternatif. Tambah di menu Alternatif.</option>';
                  }
                  ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Harga (Rp)</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="peringkat" step="1"
                                    placeholder="Contoh: 7000000" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">RAM (GB)</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="ukuran" step="1" placeholder="Contoh: 8"
                                    required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">SSD (GB)</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="unduhan" step="1"
                                    placeholder="Contoh: 512" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">CPU (PassMark)</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="aktif" step="1"
                                    placeholder="Contoh: 9500" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Berat (Kg)</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="manfaat" step="0.1"
                                    placeholder="Contoh: 1.4" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="submit" name="submit" class="btn btn-outline-primary"><i
                                    class="fa fa-save"></i> Simpan Penilaian</button>
                        </div>
                    </form>

                    <hr>

                    <h5>Daftar Penilaian</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Alternatif</th>
                                    <th>Harga (Rp)</th>
                                    <th>RAM (GB)</th>
                                    <th>SSD (GB)</th>
                                    <th>CPU (PassMark)</th>
                                    <th>Berat (Kg)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
              $b = 0;
              $sql = "SELECT * FROM saw_penilaian ORDER BY nama ASC";
              $hasil = $conn->query($sql);
              if ($hasil && $hasil->num_rows > 0) {
                while ($row = $hasil->fetch_assoc()) {
                  $b++;
                  echo "<tr>";
                  echo "<td align='center'>".$b."</td>";
                  echo "<td>".htmlspecialchars($row['nama'])."</td>";
                  echo "<td align='center'>".htmlspecialchars($row['peringkat'])."</td>";
                  echo "<td align='center'>".htmlspecialchars($row['ukuran'])."</td>";
                  echo "<td align='center'>".htmlspecialchars($row['unduhan'])."</td>";
                  echo "<td align='center'>".htmlspecialchars($row['aktif'])."</td>";
                  echo "<td align='center'>".htmlspecialchars($row['manfaat'])."</td>";
                  echo "<td>
                          <a class='btn btn-danger' href='penilaian_hapus.php?nama=".urlencode($row['nama'])."' onclick=\"return confirm('Hapus penilaian ".htmlspecialchars($row['nama'])." ?')\"><i class='fa fa-close'></i></a>
                        </td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='8'>Belum ada data penilaian.</td></tr>";
              }
              ?>
                            </tbody>
                        </table>
                    </div>

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