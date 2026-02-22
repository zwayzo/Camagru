<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
$userId = $_SESSION['user_id'];
$pdo = require '../config/database.php'; 

$sql = "
SELECT id, image_path, created_at
FROM images
WHERE user_id = ?
ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php  ?>

    <div id="page-container">
        <div id="sticker-gallery" class="sticker-gallery">
            <img src="../public/assets/stickers/sticker1.png" alt="sticker1" />
            <img src="../public/assets/stickers/sticker2.png" alt="sticker2" />
            <img src="../public/assets/stickers/sticker3.png" alt="sticker3" />
            <img src="../public/assets/stickers/sticker4.png" alt="sticker4" />
            <img src="../public/assets/stickers/sticker5.png" alt="sticker5" />
        </div>

        <div id="webcam-section">
            <video id="webcam" autoplay playsinline width="600" height="450" style="border:1px solid #ccc; display:block;"></video>
            <button id="capture-btn" style="display: none;" class="verify-email-btn" disabled>Capture</button>
        </div>

        <div class="thumbnails">
            <?php foreach ($images as $img): ?>
                <div class="thumbnail">
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Image <?= $img['id'] ?>">
                    <p>Uploaded: <?= htmlspecialchars($img['created_at']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        </div>

    </div> 

    <footer id="mainFooter">
        <div class="header">
            <p>&copy; 2026 Camagru. All rights reserved.</p>
        </div>
    </footer>

<script src="../public/assets/js/script.js" defer></script> 


</body>



</html>

