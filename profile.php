<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi database Anda

// Ambil data pengguna berdasarkan sesi login
$username = $_SESSION['username'];
$query = "SELECT * FROM user WHERE username = '$username'";
$result = mysqli_query($conn, $query);
//$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses ubah password
    if (!empty($_POST['password'])) {
        $new_password = md5($_POST['password']); // Mengamankan input
        //$hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hash password

        // Update password di database
        $update_query = "UPDATE user SET password = '$new_password' WHERE username = ?";
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Password berhasil diubah!');</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan, coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Password baru tidak boleh kosong!');</script>";}
}


// Proses penyimpanan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = 'img/'; // Folder penyimpanan gambar
    $defaultImage = 'default.jpg'; // Gambar default jika tidak ada yang diunggah

    // Cek apakah folder "uploads" sudah ada, jika tidak maka dibuat
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Cek apakah ada gambar yang diunggah
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $filePath = $uploadDir . $fileName;

        // Pindahkan file yang diunggah ke folder "uploads"
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $uploadedImage = $filePath;
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    } else {
        // Jika tidak ada gambar yang diunggah, gunakan gambar yang sebelumnya atau default
        $uploadedImage = isset($_POST['current_image']) ? $_POST['current_image'] : $uploadDir . $defaultImage;
    }

    // Simpan path gambar ke sesi untuk keperluan tampilan ulang
    //session_start();
    $_SESSION['profile_image'] = $uploadedImage;
}

// Ambil gambar dari sesi jika tersedia
//session_start();
$currentImage = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'uploads/default.jpg';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="icon" href="img/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
 <!-- nav begin -->
 <nav class="navbar navbar-expand-sm bg-danger-subtle sticky-top">
    <div class="container">
        <a class="navbar-brand" href="">My Daily Journal</a>
        <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
        >
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-dark">
            <li class="nav-item">
                <a class="nav-link" href="admin.php?page=dashboard">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin.php?page=article">Article</a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" href="admin.php?page=gallery">Gallery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=gallery#gallery"><b>Homepage</b></a>
            </li>
            <!--admin-->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-danger fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> 
                <?= $_SESSION['username']?>
                </a>
                <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="profile.php">Profile  <?= $_SESSION['username']?></a></li> 
                    <li><a class="dropdown-item" href="login.php">Logout</a></li> 
                </ul>
            </li> 
        </ul>
        </div>
    </div>
    </nav>
    <br><br>
    <!-- nav end -->

    <!-- content begin -->

        <div class="container"> 
            <?php
            if(isset($_GET['page'])){
            ?>
                <h4 class="lead display-6 pb-2 border-bottom border-primary-subtle"><?= ucfirst($_GET['page'])?></h4>
                <?php
            }else{
            ?>
                <h4 class="lead display-6 pb-2 border-bottom border-primary-subtle">Profile</h4>
                <?php
            }
            ?>
        </div> 
    <!-- content end -->
<div class="container mt-5">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="password" class="form-label">Ganti Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Tuliskan Password Baru Jika Ingin Mengganti Password Saja">
        </div>
        <div class="mb-3">
            <label for="profile_picture" class="form-label">Ganti Foto Profil</label>
            <input class="form-control" type="file" id="profile_picture" name="profile_picture">
        </div>
        <div class="mb-3">
            <p>Foto Profil Saat Ini:</p>
            <img src="<?php echo htmlspecialchars($currentImage); ?>" alt="Foto Profil" class="img-thumbnail" style="max-width: 200px;">
        </div>
        <!-- Input tersembunyi untuk menyimpan gambar saat ini -->
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($currentImage); ?>">
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
<br><br>



<!-- footer begin -->
<footer class="text-center p-5 bg-danger-subtle">
    <div>
        <a href="https://www.instagram.com/habibiramadhani02/profilecard/?igsh=MXV6b2RlanF5dGFtYg=="
        ><i class="bi bi-instagram h2 p-2 text-dark"></i
        ></a>
        <a href="https://wa.me/+6285292301037"
        ><i class="bi bi-whatsapp h2 p-2 text-dark"></i
        ></a>
    </div>
    <div>Yusu Farhan A &copy; 2023</div>
    </footer>
    <!-- footer end -->
    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"
    ></script>


</body>
</html>