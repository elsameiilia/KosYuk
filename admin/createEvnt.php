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
        mysqli_stmt_bind_param($stmt, "sssssss",  $title, $description, $lokasi, $fasilitas, $harga, $url_wa, $banner);
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
    <style>
        .form-container {
            margin-top: 31px;
            width: 655px;
            max-width: 100%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        .form-label {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            margin-top: 50px;
        }

        .form-input {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            color: rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
            padding: 9px 18px;
            font: 400 12px/2 'Poppins', sans-serif;
            border: 1px solid rgba(115, 76, 16, 1);
            width: 100%;
            height: 30px;
            margin-bottom: 5px;
        }

        .datetime-container {
            display: flex;
            gap: 30px;
            margin-top: 10px;
        }

        .date-input,
        .time-input {
            flex: 1;
        }

        .form-textarea {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            color: rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
            padding: 9px 17px;
            max-height: 100px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px solid rgba(115, 76, 16, 1);
            width: 100%;
            margin-bottom: 10px;
        }


        .upload-container {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            display: flex;
            margin-top: 0px;
            flex-direction: column;
            align-items: center;
            color: rgba(115, 76, 16, 1);
            letter-spacing: 0.3px;
            justify-content: center;
            padding: 16px 60px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px dashed rgba(115, 76, 16, 1);
            margin-bottom: 10px;
        }

        #thumbnail-preview,
        #gallery-preview {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #000;
        }

        .preview-icon {
            width: 20px;
            height: 20px;
        }

        .visually-hidden {
            display: none;
        }

        .upload-icon {
            width: 32px;
            height: 32px;
        }


        .submit-button {
            border-radius: 5px;
            background-color: rgba(115, 76, 16, 1);
            align-self: end;
            display: flex;
            margin-top: 20px;
            min-height: 42px;
            align-items: center;
            gap: 5px;
            color: var(--white, #fff);
            text-align: center;
            justify-content: center;
            padding: 11px 20px;
            font: 13px 'Poppins', sans-serif;
            border: none;
            cursor: pointer;
            border-style: none;
        }

        .submit-icon {
            aspect-ratio: 1;
            object-fit: contain;
            width: 14px;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        @media (max-width: 991px) {
            .dashboard-container {
                padding: 0 20px;
            }

            .sidebar-container {
                margin-top: 40px;
            }

            .main-content {
                padding: 0 20px 100px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .datetime-container {
                flex-direction: column;
                gap: 20px;
            }

        }
    </style>
</head>

<body>

<div class="container-dashboard">
    <?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>
</div>

<!-- ======= MAIN DASHBOARD ========  -->
<div class="main-dashboard">
    <div class="dashboard">
        <!-- ===== Header =======  -->
        <header class="dashboard-header">
            <h1 class="page-title-dashboard">Buat Postingan</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="/../../assets/images/profile-admin.png" alt="User profile"/>
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
                                <input type="text" name="harga" id="harga" class="form-input" required>
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
                        <div id="thumbnail-preview"><img src="/../../assets/images/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo</span>
                        </div>
                        <input type="file" name="banner" id="thumbnail-upload" class="visually-hidden" accept="image/*" required onchange="updateThumbnailPreview(event)"/>
                    </div> 
                <label for="photos" class="form-label">Galeri:</label>
                <div class="upload-container" role="button" tabindex="0"
                         onclick="document.getElementById('gallery-upload').click()">
                        <div id="gallery-preview"><img src="/../../assets/images/upload.png" alt="" class="upload-icon"/>
                            <span>Click to upload photo</span>
                        </div>
                        <input type="file" name="photos[]" id="gallery-upload" class="visually-hidden" accept="image/*" multiple required onchange="updateGalleryPreview(event)"/>
                    </div> 
                <button type="submit" name="create" class="submit-button"><img src="/../../assets/images/add-post.png" alt="" class="submit-icon"/> Add Post</button>
            </form>
        </div>

                    <script>
                            function updateThumbnailPreview(event) {
                            const previewContainer = document.getElementById('thumbnail-preview');
                            const files = event.target.files;

                            // Clear previous content
                            previewContainer.innerHTML = '';

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
                        
                        function updateGalleryPreview(event) { 
                            const previewContainer = document.getElementById('gallery-preview'); 
                            const files = event.target.files;

                            // Clear previous content 
                            previewContainer.innerHTML = ''; 
                            if (files) { 
                                for (let i = 0; i < files.length; i++) { 
                                    const file = files[i]; 

                                // Create a preview icon 
                                const icon = document.createElement('img'); 
                                icon.src = '../assets/attach-icon.png';

                                // Replace with the attach icon URL 
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