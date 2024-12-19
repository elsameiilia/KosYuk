<?php
if (!session_id()) session_start();
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

if (isset($_POST["create"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $lokasi = $_POST["lokasi"];
    $fasilitas = $_POST["fasilitas"];
    $harga = $_POST["harga"];
    $url_wa = $_POST["url_wa"];
    $banner = "";

    mysqli_begin_transaction($dbs);

    try {
        if (isset($_FILES["banner"]) && $_FILES["banner"]["error"] === UPLOAD_ERR_OK) {
            $photo_tmp_path = $_FILES['banner']['tmp_name'];
            $photo_extension = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
            $pathdir = __DIR__ . '/../uploads/photos/';
            $photo_filename = uniqid() . '.' . $photo_extension;
            $photo_path = $pathdir . $photo_filename;

            if (move_uploaded_file($photo_tmp_path, $photo_path)) {
                $banner = $photo_filename;
            } else {
                echo '<script>alert("Error uploading banner photo"); location.replace("/admin/createEvnt.php");</script>';
                exit();
            }
        }

        $query = "INSERT INTO kosan (title, description, lokasi, fasilitas, harga, url_wa, banner) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "ssssdss",  $title, $description, $lokasi, $fasilitas, $harga, $url_wa, $banner);
        mysqli_stmt_execute($stmt);

        // Get the last inserted kos_id
        $kos_id = mysqli_insert_id($dbs);

        // Handle multiple photo uploads
        if (isset($_FILES['photos'])) {
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $photo_tmp_path = $_FILES['photos']['tmp_name'][$key];
                    $photo_extension = pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION);
                    $photo_filename = uniqid() . '.' . $photo_extension;
                    $photo_path = $pathdir . $photo_filename;

                    if (move_uploaded_file($photo_tmp_path, $photo_path)) {
                        // Insert into img_kosan table
                        $query_img = "INSERT INTO img_kosan (kos_id, photo) VALUES (?, ?)";
                        $stmt_img = mysqli_prepare($dbs, $query_img);
                        mysqli_stmt_bind_param($stmt_img, "is", $kos_id, $photo_filename);
                        mysqli_stmt_execute($stmt_img);
                        mysqli_stmt_close($stmt_img);
                    } else {
                        throw new Exception("Error uploading photo: " . $_FILES['photos']['name'][$key]);
                    }
                }
            }
        }

        mysqli_commit($dbs);

        echo "<script> alert('Event added successfully');
        location.replace('/admin/manageEvnt.php');
        </script>";

    } catch (Exception $err) {
        mysqli_rollback($dbs);
        echo "<script>alert('Error: " . $err->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../includes/meta.php"; ?>
    <title>Buat Postingan</title>
    <link rel="stylesheet" type="text/css" href="/../../assets/style/styles.css"/>

</head>

<body>

<!-- ======= MAIN DASHBOARD ========  -->
<div class="main-dashboard">
    <div class="dashboard">
        <?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>

        <!-- ===== Header =======  -->
        <header class="dashboard-header">
            <h1 class="page-title-dashboard">Buat Postingan</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="../assets/profile-admin.png" alt="User profile"/>
                <div class="profile-text-dashboard">Admin</div>
            </div>
        </header>

        <!-- ===== Konten Posts =======  -->
        <div class="dashboard-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label for="title" class="form-label">Nama Kos</label>
                        <input type="text" name="title" id="title" class="form-input" required>
                        <label for="fasilitas" class="form-label">Fasilitas</label>
                        <input type="text" name="fasilitas" id="fasilitas" class="form-input" required>
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-input" required>
                        <div class="datetime-container">
                            <div class="date-input">
                                <label for="date" class="form-label">Kontak</label>
                                <input type="text" name="url_wa" id="url_wa" class="form-input" placeholder="Link wa.me yang bisa dihubungi" required>
                            </div>
                            <div class="time-input">
                                <label for="time" class="form-label">Harga</label>
                                <input type="number" name="harga" id="harga" class="form-input" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea" rows="10" required></textarea>
                    </div>
                </div>
                <label for="banner" class="form-label">Banner:</label>
                <div class="upload-container" role="button" tabindex="0"
                         onclick="document.getElementById('thumbnail-upload').click()">
                        <div id="thumbnail-preview"><img src="/images/assets/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo</span>
                        </div>
                        <input type="file" name="banner" id="thumbnail-upload" class="visually-hidden" accept="image/*" required onchange="updateThumbnailPreview(event)"/>
                    </div> 
                <label for="photos" class="form-label">Galeri:</label>
                <div class="upload-container" role="button" tabindex="0"
                         onclick="document.getElementById('gallery-upload').click()">
                        <div id="thumbnail-preview"><img src="/images/assets/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo</span>
                        </div>
                        <input type="file" name="photos[]" id="gallery-upload" class="visually-hidden" accept="image/*" multiple required onchange="updateThumbnailPreview(event)"/>
                    </div> 
                <button type="submit" name="create" class="submit-button"><img src="/images/assets/add-post.png" alt="" class="submit-icon"/> Add Post</button>
            </form>
        </div>

                    <script>
                            function updateThumbnailPreview(event) {
                            const previewContainer = document.getElementById('thumbnail-preview');
                            const files = event.target.files;

                            // Clear previous content
                            previewContainer.innerHTML = '';

                            if (files) {
                                for (let i = 0; i < files.length; i++) {
                                    const file = files[i];

                                    // Create a preview icon
                                    const icon = document.createElement('img');
                                    icon.src = '../assets/attach-icon.png'; // Replace with the attach icon URL
                                    icon.alt = 'Attach Icon';
                                    icon.className = 'preview-icon';

                                    // Add file name
                                    const fileName = document.createElement('span');
                                    fileName.textContent = file.name;

                                    // Add to container
                                    previewContainer.appendChild(icon);
                                    previewContainer.appendChild(fileName);
                                }
                            }
                        }
                    </script>


</body>

</html>