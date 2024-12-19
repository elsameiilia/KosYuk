<?php 
use model\Feedback;

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/model/Feedback.php";

if(isset($_POST['submit'])){
    $feedback = new Feedback();
    $feedback->nama = $_POST['nama'];
    $feedback->email = $_POST['email'];
    $feedback->pesan = $_POST['pesan'];

    $query = "INSERT INTO feedback (nama, email, pesan) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($dbs, $query); 
    mysqli_stmt_bind_param($stmt, "sss", $feedback->nama, $feedback->email, $feedback->pesan); 
    mysqli_stmt_execute($stmt); 
    mysqli_stmt_close($stmt);
}

?>

<html>
    <head>
        <title>Coba feedback</title>
    </head>
    <body>
        <form action="" method="POST">
            <label for="nama">Nama</label>
            <input type="text" class="nama" id="nama" name="nama">
            <label for="email">Email</label>
            <input type="text" class="email" id="email" name="email">
            <label for="pesan">Pesan</label>
            <input type="textarea" class="pesan" id="pesan" name="pesan">
            <input type="submit" name="submit" id="">
        </form>
    </body>
</html>