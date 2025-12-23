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
      ?>

            <section id="main-content">
                <section class="wrapper">

                    <!-- Breadcrumb -->
                    <div class="row">
                        <div class="col-lg-12">
                            <ol class="breadcrumb">
                                <li><i class="fa fa-laptop"></i><a href="index.php"> Alternatif Laptop</a></li>
                            </ol>
                        </div>
                    </div>

                    <!--START SCRIPT INSERT-->
                    <?php
          include 'koneksi.php';

          if (isset($_POST['submit'])) {
            $nama = $_POST['nama'];
            $pengembang = $_POST['pengembang']; // akan dianggap sebagai "Merek Laptop"
            $kategori = $_POST['kategori'];     // kategori laptop, misal: Gaming, Ultrabook

            if (($nama == "") || ($pengembang == "")) {
              echo "<script>alert('Tolong lengkapi data laptop!');</script>";
            } else {
              $sql = "SELECT * FROM saw_aplikasi WHERE nama='$nama'";
              $hasil = $conn->query($sql);
              $rows = $hasil->num_rows;

              if ($rows > 0) {
                echo "<script>alert('Laptop $nama sudah ada!');</script>";
              } else {
                $sql = "INSERT INTO saw_aplikasi(nama,pengembang,kategori)
                        VALUES ('$nama', '$pengembang', '$kategori')";
                $hasil = $conn->query($sql);
                echo "<script>alert('Data laptop berhasil ditambahkan!');</script>";
              }
            }
          }
          ?>
                    <!-- END SCRIPT INSERT -->

                    <!-- FORM INPUT -->
                    <form method="POST" action="">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Laptop</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="nama" placeholder="Contoh: Acer Aspire 5">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Merek / Brand</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="pengembang"
                                    placeholder="Contoh: Acer, ASUS, Lenovo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kategori Laptop</label>
                            <div class="col-sm-5">
                                <select class="form-control" name="kategori">
                                    <option>Ultrabook</option>
                                    <option>Gaming</option>
                                    <option>Workstation</option>
                                    <option>2-in-1 Convertible</option>
                                    <option>Standard</option>
                                    <option>Business</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="submit" name="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i> Submit
                            </button>
                        </div>
                    </form>

                    <!-- TABLE LIST LAPTOP -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-arrow-down"></i> No</th>
                                <th><i class="fa fa-arrow-down"></i> Nama Laptop</th>
                                <th><i class="fa fa-arrow-down"></i> Brand</th>
                                <th><i class="fa fa-arrow-down"></i> Kategori</th>
                                <th><i class="fa fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
              $b = 0;
              $sql = "SELECT * FROM saw_aplikasi ORDER BY nama ASC";
              $hasil = $conn->query($sql);
              $rows = $hasil->num_rows;

              if ($rows > 0) {
                while ($row = $hasil->fetch_row()) {
              ?>
                            <tr>
                                <td><?php echo ++$b; ?></td>
                                <td><?= $row[0] ?></td>
                                <td><?= $row[1] ?></td>
                                <td><?= $row[2] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-success" href="alt_ubah.php?nama=<?= $row[0] ?>">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger" href="alt_hapus.php?nama=<?= $row[0] ?>">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                }
              } else {
                echo "<tr><td colspan='5'>Belum ada data laptop</td></tr>";
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