<?php

session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// ─── PHOTO CRUD ───────────────────────────────────────

// DELETE photo
if (isset($_GET['delete_photo'])) {
    $id = (int)$_GET['delete_photo'];

    $stmt = $pdo->prepare("DELETE FROM photos WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php#photos");
    exit;
}

// ADD photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {

    $title        = trim($_POST['title']);
    $photographer = trim($_POST['photographer']);
    $category     = trim($_POST['category']);
    $filename     = 'placeholder';

    if (!empty($_FILES['photo']['name'])) {

        $upload_dir = 'images/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            $filename = uniqid('photo_') . '.' . $ext;

            move_uploaded_file(
                $_FILES['photo']['tmp_name'],
                $upload_dir . $filename
            );
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO photos (title, photographer, category, filename)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $title,
        $photographer,
        $category,
        $filename
    ]);

    header("Location: admin.php#photos");
    exit;
}

// EDIT photo
$edit_photo = null;

if (isset($_GET['edit_photo'])) {

    $id = (int)$_GET['edit_photo'];

    $stmt = $pdo->prepare("SELECT * FROM photos WHERE id = ?");
    $stmt->execute([$id]);

    $edit_photo = $stmt->fetch(PDO::FETCH_ASSOC);
}

// UPDATE photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_photo'])) {

    $id           = (int)$_POST['photo_id'];
    $title        = trim($_POST['title']);
    $photographer = trim($_POST['photographer']);
    $category     = trim($_POST['category']);

    $stmt = $pdo->prepare("
        UPDATE photos
        SET title=?, photographer=?, category=?
        WHERE id=?
    ");

    $stmt->execute([
        $title,
        $photographer,
        $category,
        $id
    ]);

    header("Location: admin.php#photos");
    exit;
}

// ─── EVENT CRUD ───────────────────────────────────────

// DELETE event
if (isset($_GET['delete_event'])) {

    $id = (int)$_GET['delete_event'];

    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php#events");
    exit;
}

// ADD event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {

    $title    = trim($_POST['ev_title']);
    $location = trim($_POST['ev_location']);
    $date     = $_POST['ev_date'];
    $spots    = (int)$_POST['ev_spots'];

    $stmt = $pdo->prepare("
        INSERT INTO events (title, location, event_date, spots)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $title,
        $location,
        $date,
        $spots
    ]);

    header("Location: admin.php#events");
    exit;
}

// EDIT event
$edit_event = null;

if (isset($_GET['edit_event'])) {

    $id = (int)$_GET['edit_event'];

    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);

    $edit_event = $stmt->fetch(PDO::FETCH_ASSOC);
}

// UPDATE event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {

    $id       = (int)$_POST['event_id'];
    $title    = trim($_POST['ev_title']);
    $location = trim($_POST['ev_location']);
    $date     = $_POST['ev_date'];
    $spots    = (int)$_POST['ev_spots'];

    $stmt = $pdo->prepare("
        UPDATE events
        SET title=?, location=?, event_date=?, spots=?
        WHERE id=?
    ");

    $stmt->execute([
        $title,
        $location,
        $date,
        $spots,
        $id
    ]);

    header("Location: admin.php#events");
    exit;
}

// ─── FETCH DATA ───────────────────────────────────────

$photos = $pdo->query("
    SELECT * FROM photos
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$events = $pdo->query("
    SELECT * FROM events
    ORDER BY event_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Admin Panel — KUET Photo Society</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<style>

:root{
    --bg:#07050f;
    --card:#130f24;
    --deep:#0d0a1a;
    --purple:#7c3aed;
    --purple-light:#a78bfa;
    --accent:#e879f9;
    --border:rgba(138,99,255,0.2);
    --text:#f1eeff;
    --muted:#9d8ec4;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:var(--bg);
    color:var(--text);
    font-family:'DM Sans',sans-serif;
    padding:40px 24px;
}

h1{
    font-size:1.8rem;
    margin-bottom:8px;
}

h1 span{
    color:var(--purple-light);
}

.back-link{
    color:var(--muted);
    text-decoration:none;
    font-size:0.85rem;
    display:inline-block;
    margin-bottom:10px;
}

.admin-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:40px;
    max-width:1200px;
    margin:auto;
}

.panel{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:16px;
    padding:28px;
}

.panel h2{
    margin-bottom:20px;
    color:var(--purple-light);
}

.form-group{
    margin-bottom:14px;
}

label{
    display:block;
    margin-bottom:6px;
    font-size:0.75rem;
    color:var(--muted);
}

input,select{
    width:100%;
    padding:10px 14px;
    background:var(--deep);
    border:1px solid var(--border);
    border-radius:6px;
    color:var(--text);
}

.btn-add,.btn-update{
    width:100%;
    padding:12px;
    background:var(--purple);
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    margin-top:10px;
}

.data-table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

.data-table th,
.data-table td{
    padding:10px;
    border-bottom:1px solid rgba(138,99,255,0.08);
}

.thumb{
    width:40px;
    height:40px;
    object-fit:cover;
    border-radius:6px;
}

.btn-edit,
.btn-delete{
    padding:5px 10px;
    border-radius:5px;
    text-decoration:none;
    font-size:0.78rem;
}

.btn-edit{
    background:rgba(167,139,250,0.15);
    color:var(--purple-light);
}

.btn-delete{
    background:rgba(239,68,68,0.12);
    color:#fca5a5;
}

.category-badge{
    padding:3px 10px;
    border-radius:50px;
    background:rgba(124,58,237,0.15);
    color:var(--purple-light);
    font-size:0.72rem;
}

.edit-box{
    background:rgba(124,58,237,0.08);
    border:1px solid var(--border);
    border-radius:10px;
    padding:16px;
    margin-bottom:20px;
}

@media(max-width:900px){
    .admin-grid{
        grid-template-columns:1fr;
    }
}

</style>

</head>

<body>

<a href="index.php" class="back-link">← Back to Website</a>

<form method="POST" action="logout.php" style="margin:12px 0;">
    <button type="submit" style="
        padding:10px 16px;
        background:rgba(239,68,68,0.1);
        border:1px solid rgba(239,68,68,0.2);
        border-radius:8px;
        color:#fca5a5;
        font-size:0.85rem;
        cursor:pointer;
        font-family:'DM Sans',sans-serif;
    ">
        🚪 Logout
    </button>
</form>

<div style="font-size:0.8rem; color:var(--muted); margin-bottom:25px;">
    Logged in as
    <span style="color:var(--purple-light)">
        <?= htmlspecialchars($_SESSION['admin_username']) ?>
    </span>
</div>

<h1>Admin <span>Panel</span></h1>

<p style="color:var(--muted); margin-bottom:40px;">
    Manage gallery photos and events
</p>


<div class="admin-grid">

    <!-- ═══════════════════════════════
         PHOTOS PANEL
    ═══════════════════════════════ -->
    <div class="panel" id="photos">
        <h2>📷 Photos</h2>

        <!-- Edit form (shows only when edit link clicked) -->
        <?php if ($edit_photo): ?>
        <div class="edit-box">
            <h3>✏️ Editing: <?= htmlspecialchars($edit_photo['title']) ?></h3>
            <form method="POST">
                <input type="hidden" name="photo_id" value="<?= $edit_photo['id'] ?>">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($edit_photo['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Photographer</label>
                    <input type="text" name="photographer" value="<?= htmlspecialchars($edit_photo['photographer']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <?php foreach (['nature','portrait','architecture','event'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= $edit_photo['category']===$cat ? 'selected':'' ?>><?= ucfirst($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="update_photo" class="btn-update">Update Photo</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Add new photo form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="e.g. Golden Hour" required>
            </div>
            <div class="form-group">
                <label>Photographer</label>
                <input type="text" name="photographer" placeholder="e.g. Arif Hossain" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="nature">Nature</option>
                    <option value="portrait">Portrait</option>
                    <option value="architecture">Architecture</option>
                    <option value="event">Event</option>
                </select>
            </div>
            <div class="form-group">
                <label>Upload Photo</label>
                <input type="file" name="photo" accept="image/*">
            </div>
            <button type="submit" name="add_photo" class="btn-add">+ Add Photo</button>
        </form>

        <!-- Photos table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($photos as $photo): ?>
                <tr>
                    <td>
                        <?php if ($photo['filename'] !== 'placeholder' && file_exists('images/' . $photo['filename'])): ?>
                            <img src="images/<?= htmlspecialchars($photo['filename']) ?>" class="thumb" alt="">
                        <?php else: ?>
                            <div class="thumb" style="display:flex;align-items:center;justify-content:center;">🖼️</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($photo['title']) ?></div>
                        <div style="font-size:0.75rem;color:var(--muted)"><?= htmlspecialchars($photo['photographer']) ?></div>
                    </td>
                    <td><span class="category-badge"><?= $photo['category'] ?></span></td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="?edit_photo=<?= $photo['id'] ?>#photos" class="btn-edit">Edit</a>
                        <a href="?delete_photo=<?= $photo['id'] ?>"
                           onclick="return confirm('Delete this photo?')"
                           class="btn-delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($photos)): ?>
                    <tr><td colspan="4" style="color:var(--muted);text-align:center;padding:20px;">No photos yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ═══════════════════════════════
         EVENTS PANEL
    ═══════════════════════════════ -->
    <div class="panel" id="events">
        <h2>📅 Events</h2>

        <!-- Edit event form -->
        <?php if ($edit_event): ?>
        <div class="edit-box">
            <h3>✏️ Editing: <?= htmlspecialchars($edit_event['title']) ?></h3>
            <form method="POST">
                <input type="hidden" name="event_id" value="<?= $edit_event['id'] ?>">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="ev_title" value="<?= htmlspecialchars($edit_event['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="ev_location" value="<?= htmlspecialchars($edit_event['location']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="ev_date" value="<?= $edit_event['event_date'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Available Spots</label>
                    <input type="number" name="ev_spots" value="<?= $edit_event['spots'] ?>" required>
                </div>
                <button type="submit" name="update_event" class="btn-update">Update Event</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Add event form -->
        <form method="POST">
            <div class="form-group">
                <label>Event Title</label>
                <input type="text" name="ev_title" placeholder="e.g. Street Photography Walk" required>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="ev_location" placeholder="e.g. KUET Campus" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="ev_date" required>
            </div>
            <div class="form-group">
                <label>Available Spots</label>
                <input type="number" name="ev_spots" placeholder="e.g. 20" required>
            </div>
            <button type="submit" name="add_event" class="btn-add">+ Add Event</button>
        </form>

        <!-- Events table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                <tr>
                    <td><?= htmlspecialchars($ev['title']) ?></td>
                    <td style="color:var(--purple-light)"><?= date('d M Y', strtotime($ev['event_date'])) ?></td>
                    <td style="color:var(--muted)"><?= htmlspecialchars($ev['location']) ?></td>
                    <td style="display:flex;gap:6px;">
                        <a href="?edit_event=<?= $ev['id'] ?>#events" class="btn-edit">Edit</a>
                        <a href="?delete_event=<?= $ev['id'] ?>"
                           onclick="return confirm('Delete this event?')"
                           class="btn-delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($events)): ?>
                    <tr><td colspan="4" style="color:var(--muted);text-align:center;padding:20px;">No events yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>