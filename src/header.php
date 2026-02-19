<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use PDO
$pdo = require '../config/database.php';

session_start();

$successMessage = $_SESSION['succes'] ?? null;
unset($_SESSION['succes']);

// Determine if user is logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // fetch full user row
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch images for gallery (everyone can see)
$sql = "
SELECT 
    images.id,
    images.user_id,
    images.image_path,
    images.created_at,
    users.username,
    COUNT(DISTINCT likes.id) AS like_count,
    COUNT(DISTINCT comments.id) AS comment_count
FROM images
JOIN users ON images.user_id = users.id
LEFT JOIN likes ON images.id = likes.image_id
LEFT JOIN comments ON images.id = comments.image_id
GROUP BY images.id
ORDER BY images.created_at DESC
";


$stmt = $pdo->query($sql);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="../public/assets/css/user.css">
</head>
<body>
    <div id="mainContent">

        <div class="header">
            <h1>Camagru</h1>

            <div class="header-actions">
                <!-- Always visible -->
                <?php if ($user == null): ?>
                    <a href="../public/index.php" class="verify-email-btn">Return to login</a>
                <?php else: ?>
                    <a href="gallerie.php" class="verify-email-btn">View Gallerie</a>
                <?php endif; ?>

                <?php if ($user != null): ?>
                    <!-- Visible only to logged-in users -->
                    <a href="user_page.php" class="verify-email-btn">Take a pic</a>

                    <div id="upload-section">
                        <input type="file" id="file-input" accept="image/*" style="display: none;">
                        <button class="verify-email-btn" id="upload-btn">Upload image</button>
                    </div>

                    <div id="canvas-container" style="display: none;">
                        <canvas id="canvas"></canvas>
                    </div>

                    <div id="stickers-panel" style="display:none;">
                        <h3>Add a sticker:</h3>
                        <button class="sticker-btn" data-src="../public/assets/stickers/sticker1.webp">👑</button>
                        <button class="sticker-btn" data-src="../public/assets/stickers/sticker2.avif">😎</button>
                        <button class="sticker-btn" data-src="../public/assets/stickers/sticker3.png">🔥</button>
                    </div>

                    <form action="save.php" method="POST" id="save-form" style="display: none;">
                        <input type="hidden" name="imageData" id="imageDataInput">
                        <button type="submit" id="save-btn" class="verify-email-btn">Save Image</button>
                    </form>

                    <form action="enable.php" method="post">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="verify-email-btn">
                            <?= ($user['enable'] == 1) ? 'Disable notification' : 'Enable notification' ?>
                        </button>
                    </form>

                    <button type="button" class="verify-email-btn" onclick="openModal()">Edit profile</button>

                    <form action="logout.php" method="post">
                        <button type="submit" class="verify-email-btn">Logout</button>
                    </form>
                    <!-- <div id="overlay" class="overlay"></div>
                    <div id="editModal" class="modal">
                        <div class="modal-content">
                            <h2>Edit Profile</h2>
                            <form action="edit_profile.php" method="post">
                                <input type="text" name="username" placeholder="New username">
                                <input type="email" name="email" placeholder="New email">
                                <input type="text" name="password" placeholder="New password">
                                <button type="submit">Save</button>
                                <button type="button" onclick="closeModal()">Cancel</button>
                            </form>
                        </div>
                    </div> -->
                <?php endif; ?>
            </div>
        </div>

        <?php if($successMessage): ?>
            <p id="flash-message" class="success-message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>
    </div>

    <div id="overlay" class="overlay"></div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Profile</h2>
            <form action="edit_profile.php" method="post">
                <input type="text" name="username" placeholder="New username">
                <input type="email" name="email" placeholder="New email">
                <input type="password" name="password" placeholder="New password">
                <button type="submit">Save</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
</body>

    <!-- mainContent remains open; page templates should render content here, then close the mainContent div and include the footer -->




