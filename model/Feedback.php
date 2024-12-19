<?php

namespace model;

class Feedback
{
    public int $fb_id;

    public string $nama;

    public string $email;

    public string $pesan;

    // Method to save feedback to the database
    public function save($dbs)
    {
        $query = "INSERT INTO feedback (nama, email, pesan) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "sss", $this->nama, $this->email, $this->pesan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

?>
