<?php

if (!session_id())
session_start();
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../model/Feedback.php";

use model\Feedback;

if (isset($_POST['submit'])) { 
  $feedback = new Feedback(); 
  $feedback->nama = $_POST['nama']; 
  $feedback->email = $_POST['email']; 
  $feedback->pesan = $_POST['pesan']; 
  
  try { $feedback->save($dbs); 
    echo "<script>alert('Feedback submitted successfully!');</script>"; 
  } catch (\Exception $e) { 
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>"; 
  } 
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8"/>
        <title>About KosYuk</title>
        <link rel="stylesheet" type="text/css" href="/../../assets/style/styles.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <style>
              /* Overlay styling */
              .hero-section-about {
                  position: relative;
                  display: inline-block; /* Sesuaikan berdasarkan konteks */
              }

              .hero-section-about img {
                  display: block;
                  width: 100%;
                  height: auto; /* Sesuaikan ukuran sesuai kebutuhan */
              }

              .hero-section-about::before {
                  content: '';
                  position: absolute;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  background-color: rgba(0, 0, 0, 0.5); /* Warna overlay dengan transparansi */
                  z-index: 1; /* Pastikan overlay berada di atas gambar */
              }

              .hero-title-about, .hero-description-about {
                  position: relative;
                  z-index: 2; /* Pastikan konten berada di atas overlay */
                  color: #fff; /* Warna teks putih agar terlihat di atas overlay gelap */
              }
          </style>
    </head>

<body>
  <div class="about-container">
   <!-- ======== HEADER ======== -->

   <?php include_once __DIR__. "/../includes/navbar.php"; ?>

    <!-- ======== BANNER HERO ABOUT ======== -->
    <section class="hero-section-about">
        <img src="/../../assets/images/hero-about.jpeg" alt="About section hero image" class="hero-image-about" />
        <h1 class="hero-title-about">Tentang KosYuk</h1>
        <p class="hero-description-about">
          <b>KosYuk</b> adalah sebuah sistem berbasis web yang dikembangkan untuk membantu mahasiswa, pekerja, atau masyarakat umum dalam menemukan kost-kostan di sekitar Universitas Jenderal Soedirman (UNSOED). Permasalahan yang sering dihadapi oleh pencari kost, seperti kesulitan mendapatkan informasi lengkap dan terpercaya mengenai kost, menjadi latar belakang pengembangan sistem ini. Website ini dirancang sebagai solusi komprehensif yang menyediakan informasi detail mengenai kost, sehingga pengguna dapat dengan mudah menemukan tempat tinggal yang sesuai dengan kebutuhan dan preferensi mereka. Dengan pendekatan yang modern dan berbasis teknologi, sistem ini diharapkan dapat memberikan pengalaman pencarian yang lebih efektif, efisien, dan nyaman.

        </p>
      </section>
    
  <!-- ======== CONTACT OASIS SEEK ======== -->
      <section class="contact-section-about">
        <div class="contact-container-about">
          <div class="contact-info-about">
            <h2 class="contact-title-about">Kontak Kami</h2>
            <p class="contact-subtitle-about">
              Silakan hubungi kami bila ada pertanyaan, saran, maupun kritik terkait website kami!
            </p>
            <div class="contact-details-about">
              <div class="contact-item-about">
                <img src="/assets/images/loc-icon-about.png" alt="Location icon" class="contact-icon-about" />
                <span>Blater, Purbalingga</span>
              </div>
              <div class="contact-item-about">
                <img src="/assets/images/call-icon-about.png" alt="Phone icon" class="contact-icon-about" />
                <span>+62 8123 4567 890</span>
              </div>
              <div class="contact-item-about">
                <img src="/assets/images/mail-icon-about.png" alt="Email icon" class="contact-icon-about" />
                <span>kosyuk@gmail.com</span>
              </div>
            </div>
            
            <div class="social-section-about">
              <h3 class="social-title-about">Follow kami di social media:</h3>
              <div class="social-links-about">
                <div class="social-item-about">
                  <img src="/assets/images/insta-icon-about.png" alt="Twitter icon" class="social-icon-about" />
                  <span>@kosyuk1</span>
                </div>
                <div class="social-item-about">
                  <img src="/assets/images/facebook-icon-about.png" alt="Facebook icon" class="social-icon-about" />
                  <span>KosYuk Official</span>
                </div>
                <div class="social-item-about">
                  <img src="/assets/images/tiktok-icon-about.png" alt="Instagram icon" class="social-icon-about" />
                  <span>@kosyuk1</span>
                </div>
              </div>
            </div>
          </div>
          <div class="feedback">
            <div class="feedback-container">
              <div class="fb-title">Hubungi Kami</div>
              <div class="feedback-form">
                <form action="" method="POST">
                  <div class="sender">
                    <label for="nama">Nama</label><br>
                    <input type="text" class="nama" id="nama" name="nama">
                  </div>
                  <div class="sender-email">
                    <label for="email">Email</label><br>
                    <input type="text" class="email" id="email" name="email">
                  </div>
                  <div class="fb-message">
                    <label for="pesan">Pesan</label><br>
                    <input type="textarea" class="pesan" id="pesan" name="pesan">
                  </div><br><br>
                    <input type="submit" name="submit" id="submit" value="Kirim">
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    
    
      <!--============== DROPDOWN FAQ=================== -->

      <div class="faq-container">
        <div class="header-wrapper-faq">
          <h1 class="main-title">Ada pertanyaan?</h1>
          <h2 class="subtitle">Frequently Asked Questions (FAQ)</h2>
        </div>
      
        <div class="questions-wrapper">
          <div class="question-item">
            <button class="question-toggle">
              Apa itu KosYuk?
            </button>
            <div class="answer">
              <p>
              KosYuk adalah sebuah sistem berbasis web yang dikembangkan untuk membantu mahasiswa, pekerja, atau masyarakat umum dalam menemukan kos-kosan di sekitar Universitas Jenderal Soedirman (UNSOED)
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
      
          <div class="question-item">
            <button class="question-toggle">
              Apa cuma mahasiswa Unsoed saja yang diperbolehkan cari kos di sini?
            </button>
            <div class="answer">
              <p>
                Tidak, website ini tidak mengkhususkan mahasiswa Unsoed saja. Tapi mengkhususkan untuk kos area Unsoed.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
      
          <div class="question-item">
            <button class="question-toggle">
              Apakah bisa menghubungi pemilik kos lewat website ini?
            </button>
            <div class="answer">
              <p>
                Ya, menghubungi pemilik kos bisa dilakukan melalui tombol icon Whatsapp yang terletak di sebelah icon Bookmark.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
      
          <div class="question-item">
            <button class="question-toggle">
              Informasi apa saja yang ada di detail kos?
            </button>
            <div class="answer">
              <p>
                Banyak, bisa lihat lokasi, fasilitas, biaya, dan menghubungi pemilik kos secara langsung.
              </p>
            </div>
          </div>
          <hr class="faq-divider" />
        </div>
      </div>
      <form action="" method="POST"></form>
      
      
    <!-- ========= JS untuk dropdown =========-->
    <script>
      document.querySelectorAll('.question-toggle').forEach((button) => {
      button.addEventListener('click', () => {
        const answer = button.nextElementSibling;

        // Toggle menampilkan jawaban
        button.classList.toggle('active');
        if (answer.style.display === 'block') {
          answer.style.display = 'none';
        } else {
          answer.style.display = 'block';
        }
      });
    });
    </script>
     
    <!-- =========== FOOTER =========== -->
  
    <?php include_once __DIR__ . "/../includes/footer.php"; ?>

  </div>
</body>
</html>
