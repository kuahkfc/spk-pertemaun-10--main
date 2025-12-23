<?php
// alt_ubah.php
include 'koneksi.php';

if (!isset($_GET['nama'])) {
    header('Location: alternatif.php');
    exit;
}

$old_name = $_GET['nama'];
// ambil data lama
$stmt = $conn->prepare("SELECT nama, pengembang, kategori FROM saw_aplikasi WHERE nama = ? LIMIT 1");
$stmt->bind_param("s", $old_name);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo "<script>alert('Data tidak ditemukan.');window.location='alternatif.php';</script>";
    exit;
}
$row = $res->fetch_assoc();

// proses update jika submit
if (isset($_POST['submit'])) {
    $nama_baru = trim($_POST['nama']);
    $pengembang = trim($_POST['pengembang']);
    $kategori = $_POST['kategori'];

    if ($nama_baru == '' || $pengembang == '') {
        echo "<script>alert('Tolong lengkapi data!');</script>";
    } else {
        // jika nama diubah dan nama baru sudah ada di DB -> tolak
        if ($nama_baru !== $old_name) {
            $chk = $conn->prepare("SELECT nama FROM saw_aplikasi WHERE nama = ? LIMIT 1");
            $chk->bind_param("s", $nama_baru);
            $chk->execute();
            $chkres = $chk->get_result();
            if ($chkres->num_rows > 0) {
                echo "<script>alert('Nama laptop sudah ada, gunakan nama lain.');</script>";
            } else {
                // update
                $u = $conn->prepare("UPDATE saw_aplikasi SET nama=?, pengembang=?, kategori=? WHERE nama=?");
                $u->bind_param("ssss", $nama_baru, $pengembang, $kategori, $old_name);
                if ($u->execute()) {
                    echo "<script>alert('Data berhasil diupdate'); window.location='alternatif.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal update: " . addslashes($conn->error) . "');</script>";
                }
            }
        } else {
            // nama tidak berubah, langsung update field lain
            $u = $conn->prepare("UPDATE saw_aplikasi SET pengembang=?, kategori=? WHERE nama=?");
            $u->bind_param("sss", $pengembang, $kategori, $old_name);
            if ($u->execute()) {
                echo "<script>alert('Data berhasil diupdate'); window.location='alternatif.php';</script>";
                exit;
            } else {
                echo "<script>alert('Gagal update: " . addslashes($conn->error) . "');</script>";
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<?php include 'components/head.php'; ?>

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
                                <li><i class="fa fa-user"></i><a href="alternatif.php"> Alternatif Laptop</a></li>
                                <li><i class="fa fa-edit"></i> Ubah</li>
                            </ol>
                        </div>
                    </div>

                    <h4>Ubah Alternatif Laptop</h4>
                    <form method="POST" action="">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Laptop</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama"
                                    value="<?= htmlspecialchars($row['nama']) ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Vendor / Merek</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="pengembang"
                                    value="<?= htmlspecialchars($row['pengembang']) ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kategori</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="kategori">
                                    <?php
                    $cats = ['Laptop','Gaming','Ultrabook','Business','2-in-1','Lainnya'];
                    foreach($cats as $c) {
                      $sel = ($c == $row['kategori']) ? 'selected' : '';
                      echo "<option value=\"" . htmlspecialchars($c) . "\" $sel>" . htmlspecialchars($c) . "</option>";
                    }
                  ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                                Simpan Perubahan</button>
                            <a href="alternatif.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

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