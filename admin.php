<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit;
}

require 'db.php';

// Active tab
$tab   = in_array($_GET['tab'] ?? '', ['photos','events','team']) ? $_GET['tab'] : 'photos';
$flash = htmlspecialchars($_GET['msg'] ?? '');

// ════════════════════════════════════════
// PHOTOS CRUD
// ════════════════════════════════════════

if (isset($_GET['delete_photo'])) {
    $id = (int)$_GET['delete_photo'];
    $stmt = $pdo->prepare("SELECT filename FROM photos WHERE id=?");
    $stmt->execute([$id]);
    $filename = $stmt->fetchColumn();
    if ($filename && $filename !== 'placeholder' && file_exists("images/$filename")) {
        unlink("images/$filename");
    }
    $pdo->prepare("DELETE FROM photos WHERE id=?")->execute([$id]);
    header("Location: admin.php?tab=photos&msg=Photo+deleted");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {
    $title        = trim($_POST['title']);
    $photographer = trim($_POST['photographer']);
    $category     = trim($_POST['category']);
    $filename     = 'placeholder';
    if (!empty($_FILES['photo']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            if (!is_dir('images')) mkdir('images', 0755, true);
            $filename = uniqid('photo_') . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], "images/$filename");
        }
    }
    $pdo->prepare("INSERT INTO photos (title,photographer,category,filename) VALUES(?,?,?,?)")
        ->execute([$title, $photographer, $category, $filename]);
    header("Location: admin.php?tab=photos&msg=Photo+added");
    exit;
}

$edit_photo = null;
if (isset($_GET['edit_photo'])) {
    $s = $pdo->prepare("SELECT * FROM photos WHERE id=?");
    $s->execute([(int)$_GET['edit_photo']]);
    $edit_photo = $s->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_photo'])) {
    $pdo->prepare("UPDATE photos SET title=?,photographer=?,category=? WHERE id=?")
        ->execute([trim($_POST['title']), trim($_POST['photographer']), $_POST['category'], (int)$_POST['photo_id']]);
    header("Location: admin.php?tab=photos&msg=Photo+updated");
    exit;
}

// ════════════════════════════════════════
// EVENTS CRUD
// ════════════════════════════════════════

if (isset($_GET['delete_event'])) {
    $pdo->prepare("DELETE FROM events WHERE id=?")->execute([(int)$_GET['delete_event']]);
    header("Location: admin.php?tab=events&msg=Event+deleted");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $pdo->prepare("INSERT INTO events (title,location,event_date,spots) VALUES(?,?,?,?)")
        ->execute([trim($_POST['ev_title']), trim($_POST['ev_location']), $_POST['ev_date'], (int)$_POST['ev_spots']]);
    header("Location: admin.php?tab=events&msg=Event+added");
    exit;
}

$edit_event = null;
if (isset($_GET['edit_event'])) {
    $s = $pdo->prepare("SELECT * FROM events WHERE id=?");
    $s->execute([(int)$_GET['edit_event']]);
    $edit_event = $s->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $pdo->prepare("UPDATE events SET title=?,location=?,event_date=?,spots=? WHERE id=?")
        ->execute([trim($_POST['ev_title']), trim($_POST['ev_location']), $_POST['ev_date'], (int)$_POST['ev_spots'], (int)$_POST['event_id']]);
    header("Location: admin.php?tab=events&msg=Event+updated");
    exit;
}

// ════════════════════════════════════════
// TEAM CRUD
// ════════════════════════════════════════

if (isset($_GET['delete_team'])) {
    $pdo->prepare("DELETE FROM team WHERE id=?")->execute([(int)$_GET['delete_team']]);
    header("Location: admin.php?tab=team&msg=Member+deleted");
    exit;
}

// ADD team member

// ADD team member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_team'])) {
    $photo = 'placeholder';

    if (!empty($_FILES['tm_photo']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['tm_photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            if (!is_dir('images/team')) mkdir('images/team', 0755, true);
            $photo = uniqid('team_') . '.' . $ext;
            move_uploaded_file($_FILES['tm_photo']['tmp_name'], "images/team/$photo");
        }
    }

    $pdo->prepare("INSERT INTO team (name,role,icon,photo,sort_order) VALUES(?,?,?,?,?)")
        ->execute([
            trim($_POST['tm_name']),
            trim($_POST['tm_role']),
            '📸',           // default icon kept for fallback
            $photo,
            (int)$_POST['tm_order']
        ]);
    header("Location: admin.php?tab=team&msg=Member+added");
    exit;
}


$edit_team = null;
if (isset($_GET['edit_team'])) {
    $s = $pdo->prepare("SELECT * FROM team WHERE id=?");
    $s->execute([(int)$_GET['edit_team']]);
    $edit_team = $s->fetch(PDO::FETCH_ASSOC);
}

// UPDATE team member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_team'])) {
    $id    = (int)$_POST['team_id'];
    $photo = null; // null means keep existing

    if (!empty($_FILES['tm_photo']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['tm_photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            if (!is_dir('images/team')) mkdir('images/team', 0755, true);

            // Delete old photo file
            $old = $pdo->prepare("SELECT photo FROM team WHERE id=?");
            $old->execute([$id]);
            $oldfile = $old->fetchColumn();
            if ($oldfile && $oldfile !== 'placeholder' && file_exists("images/team/$oldfile")) {
                unlink("images/team/$oldfile");
            }

            $photo = uniqid('team_') . '.' . $ext;
            move_uploaded_file($_FILES['tm_photo']['tmp_name'], "images/team/$photo");
        }
    }

    if ($photo) {
        $pdo->prepare("UPDATE team SET name=?,role=?,sort_order=?,photo=? WHERE id=?")
            ->execute([trim($_POST['tm_name']), trim($_POST['tm_role']), (int)$_POST['tm_order'], $photo, $id]);
    } else {
        $pdo->prepare("UPDATE team SET name=?,role=?,sort_order=? WHERE id=?")
            ->execute([trim($_POST['tm_name']), trim($_POST['tm_role']), (int)$_POST['tm_order'], $id]);
    }
    header("Location: admin.php?tab=team&msg=Member+updated");
    exit;
}

// ════════════════════════════════════════
// FETCH ALL DATA
// ════════════════════════════════════════
$photos = $pdo->query("SELECT * FROM photos ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);
$team   = $pdo->query("SELECT * FROM team ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — KUET Photo Society</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#07050f; --deep:#0d0a1a; --card:#130f24;
            --purple:#7c3aed; --pl:#a78bfa; --accent:#e879f9;
            --border:rgba(138,99,255,0.2); --border-bright:rgba(138,99,255,0.45);
            --text:#f1eeff; --muted:#9d8ec4; --red:#fca5a5; --green:#22c55e;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--deep);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 24px 14px;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
        }

        .sidebar-logo {
            padding: 0 10px 20px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }
        .sidebar-logo h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
        }
        .sidebar-logo h2 span { color: var(--pl); }
        .sidebar-logo p {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 3px;
            letter-spacing: 0.05em;
        }

        /* Nav links stacked vertically */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.88rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .nav-item:hover {
            background: rgba(124,58,237,0.1);
            color: var(--text);
        }
        .nav-item.active {
            background: rgba(124,58,237,0.2);
            color: var(--pl);
            border-color: var(--border);
        }
        .nav-item .nav-icon { font-size: 1rem; width: 20px; text-align: center; }

        /* Divider */
        .sidebar-divider {
            height: 1px;
            background: var(--border);
            margin: 12px 0;
        }

        /* Back to website button */
        .btn-website {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            border-radius: 8px;
            background: rgba(139,92,246,0.1);
            border: 1px solid var(--border-bright);
            color: var(--pl);
            text-decoration: none;
            font-size: 0.88rem;
            transition: all 0.2s ease;
            margin-bottom: 8px;
        }
        .btn-website:hover {
            background: rgba(139,92,246,0.2);
        }

        /* Logout button */
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            border-radius: 8px;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: var(--red);
            text-decoration: none;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            text-align: left;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,0.15);
        }

        /* Logged in user info */
        .sidebar-user {
            padding: 10px 14px;
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 8px;
        }
        .sidebar-user span { color: var(--pl); }

        /* ══════════════════════════════
           MAIN CONTENT
        ══════════════════════════════ */
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 36px 40px;
            min-height: 100vh;
        }

        /* Flash message */
        .flash {
            padding: 12px 18px;
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.25);
            border-radius: 8px;
            color: #86efac;
            font-size: 0.88rem;
            margin-bottom: 28px;
        }

        /* Page header */
        .page-header {
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        .page-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
        }
        .page-header h1 span { color: var(--pl); }
        .page-header p {
            color: var(--muted);
            font-size: 0.88rem;
            margin-top: 4px;
        }

        /* ── Vertical sections — form on top, table below ── */
        .section-block {
            display: flex;
            flex-direction: column;
            gap: 28px;
            max-width: 900px;
        }

        /* Form card */
        .form-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
        }
        .form-card h2 {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--pl);
            margin-bottom: 20px;
        }

        /* Edit box */
        .edit-box {
            background: rgba(232,121,249,0.05);
            border: 1px solid rgba(232,121,249,0.2);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .edit-box h3 {
            font-size: 0.8rem;
            color: var(--accent);
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* Form elements */
        .form-group { margin-bottom: 16px; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        label {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 7px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px 14px;
            background: var(--deep);
            border: 1px solid var(--border);
            border-radius: 7px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus, select:focus {
            border-color: var(--purple);
            box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
        }
        input[type="file"] { padding: 8px; }

        .btn-submit {
            padding: 11px 28px;
            background: var(--purple);
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 4px;
        }
        .btn-submit:hover { background: #6d28d9; }

        .btn-update {
            padding: 10px 24px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 0.88rem;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-update:hover { background: #d946ef; }

        /* Table card */
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
        }
        .table-card h2 {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--pl);
            margin-bottom: 20px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        th {
            text-align: left;
            padding: 9px 14px;
            color: var(--muted);
            font-weight: 400;
            border-bottom: 1px solid var(--border);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        td {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(138,99,255,0.07);
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(124,58,237,0.04); }

        .thumb {
            width: 44px; height: 44px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--deep);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        .badge-cat {
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(124,58,237,0.15);
            color: var(--pl);
            border: 1px solid var(--border);
        }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .btn-e {
            background: rgba(167,139,250,0.12);
            color: var(--pl);
            border: 1px solid var(--border);
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-e:hover { background: rgba(167,139,250,0.22); }
        .btn-d {
            background: rgba(239,68,68,0.1);
            color: var(--red);
            border: 1px solid rgba(239,68,68,0.2);
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-d:hover { background: rgba(239,68,68,0.2); }
        .empty-cell {
            text-align: center;
            padding: 32px;
            color: var(--muted);
            font-size: 0.88rem;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .sidebar { width: 200px; }
            .main { margin-left: 200px; padding: 24px 20px; }
            .form-row { grid-template-columns: 1fr; }
        }
        @media (max-width: 650px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════
     SIDEBAR
══════════════════════════════════════ -->
<aside class="sidebar">

    <div class="sidebar-logo">
        <h2>KUET <span>Admin</span></h2>
        <p>Photography Society</p>
    </div>

    <nav class="sidebar-nav">
        <!-- Gallery -->
        <a href="admin.php?tab=photos"
           class="nav-item <?= $tab === 'photos' ? 'active' : '' ?>">
            <span class="nav-icon">📷</span> Gallery
        </a>

        <!-- Events -->
        <a href="admin.php?tab=events"
           class="nav-item <?= $tab === 'events' ? 'active' : '' ?>">
            <span class="nav-icon">📅</span> Events
        </a>

        <!-- Team -->
        <a href="admin.php?tab=team"
           class="nav-item <?= $tab === 'team' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Team
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    <!-- Back to Website button -->
    <a href="index.php" class="btn-website">
        <span>🌐</span> Back to Website
    </a>

    <!-- Logout button -->
    <a href="adminlogout.php"
       class="btn-logout"
       onclick="return confirm('Are you sure you want to logout?')">
        <span>🚪</span> Logout
    </a>

    <!-- Logged in as -->
    <div class="sidebar-user">
        Logged in as<br>
        <span><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
    </div>

</aside>

<!-- ══════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════ -->
<main class="main">

    <?php if ($flash): ?>
        <div class="flash">✓ <?= $flash ?></div>
    <?php endif; ?>

    <!-- ════════════════════════════════
         PHOTOS / GALLERY TAB
    ════════════════════════════════ -->
    <?php if ($tab === 'photos'): ?>

        <div class="page-header">
            <h1>Manage <span>Gallery</span></h1>
            <p>Add, edit or delete photos. Uploaded images go to the images/ folder.</p>
        </div>

        <div class="section-block">

            <!-- Edit form — shows only when Edit is clicked -->
            <?php if ($edit_photo): ?>
            <div class="form-card">
                <div class="edit-box">
                    <h3>✏️ Editing: <?= htmlspecialchars($edit_photo['title']) ?></h3>
                    <form method="POST">
                        <input type="hidden" name="photo_id" value="<?= $edit_photo['id'] ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title"
                                       value="<?= htmlspecialchars($edit_photo['title']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Photographer</label>
                                <input type="text" name="photographer"
                                       value="<?= htmlspecialchars($edit_photo['photographer']) ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category">
                                <?php foreach (['nature','portrait','architecture','event'] as $c): ?>
                                    <option value="<?= $c ?>"
                                        <?= $edit_photo['category'] === $c ? 'selected' : '' ?>>
                                        <?= ucfirst($c) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="update_photo" class="btn-update">
                            Save Changes
                        </button>
                        <a href="admin.php?tab=photos"
                           style="margin-left:10px;font-size:0.82rem;color:var(--muted);text-decoration:none;">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Add new photo form -->
            <div class="form-card">
                <h2>➕ Add New Photo</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" placeholder="e.g. Golden Hour" required>
                        </div>
                        <div class="form-group">
                            <label>Photographer</label>
                            <input type="text" name="photographer" placeholder="e.g. Arif Hossain" required>
                        </div>
                    </div>
                    <div class="form-row">
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
                            <label>Upload Image (jpg / png / webp)</label>
                            <input type="file" name="photo" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" name="add_photo" class="btn-submit">+ Add Photo</button>
                </form>
            </div>

            <!-- Photos table -->
            <div class="table-card">
                <h2>All Photos (<?= count($photos) ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title / Photographer</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($photos)): ?>
                        <tr><td colspan="4" class="empty-cell">No photos yet — add one above!</td></tr>
                    <?php else: ?>
                        <?php foreach ($photos as $p): ?>
                        <tr>
                            <td>
                                <?php $fp = 'images/' . $p['filename'];
                                if ($p['filename'] !== 'placeholder' && file_exists($fp)): ?>
                                    <img src="<?= $fp ?>" class="thumb" alt="">
                                <?php else: ?>
                                    <div class="thumb">🖼️</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:500"><?= htmlspecialchars($p['title']) ?></div>
                                <div style="font-size:0.75rem;color:var(--muted)">
                                    <?= htmlspecialchars($p['photographer']) ?>
                                </div>
                            </td>
                            <td><span class="badge-cat"><?= $p['category'] ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="?tab=photos&edit_photo=<?= $p['id'] ?>" class="btn-e">Edit</a>
                                    <a href="?tab=photos&delete_photo=<?= $p['id'] ?>"
                                       onclick="return confirm('Delete this photo permanently?')"
                                       class="btn-d">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- end section-block -->

    <!-- ════════════════════════════════
         EVENTS TAB
    ════════════════════════════════ -->
    <?php elseif ($tab === 'events'): ?>

        <div class="page-header">
            <h1>Manage <span>Events</span></h1>
            <p>Add upcoming events — they appear live on the website immediately.</p>
        </div>

        <div class="section-block">

            <!-- Edit event form -->
            <?php if ($edit_event): ?>
            <div class="form-card">
                <div class="edit-box">
                    <h3>✏️ Editing: <?= htmlspecialchars($edit_event['title']) ?></h3>
                    <form method="POST">
                        <input type="hidden" name="event_id" value="<?= $edit_event['id'] ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="ev_title"
                                       value="<?= htmlspecialchars($edit_event['title']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="ev_location"
                                       value="<?= htmlspecialchars($edit_event['location']) ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="ev_date"
                                       value="<?= $edit_event['event_date'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Available Spots</label>
                                <input type="number" name="ev_spots"
                                       value="<?= $edit_event['spots'] ?>" required>
                            </div>
                        </div>
                        <button type="submit" name="update_event" class="btn-update">Save Changes</button>
                        <a href="admin.php?tab=events"
                           style="margin-left:10px;font-size:0.82rem;color:var(--muted);text-decoration:none;">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Add event form -->
            <div class="form-card">
                <h2>➕ Add New Event</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Event Title</label>
                            <input type="text" name="ev_title"
                                   placeholder="e.g. Street Photography Walk" required>
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="ev_location"
                                   placeholder="e.g. KUET Campus" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="ev_date" required>
                        </div>
                        <div class="form-group">
                            <label>Available Spots</label>
                            <input type="number" name="ev_spots" placeholder="20" required>
                        </div>
                    </div>
                    <button type="submit" name="add_event" class="btn-submit">+ Add Event</button>
                </form>
            </div>

            <!-- Events table -->
            <div class="table-card">
                <h2>All Events (<?= count($events) ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Spots</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($events)): ?>
                        <tr><td colspan="5" class="empty-cell">No events yet — add one above!</td></tr>
                    <?php else: ?>
                        <?php foreach ($events as $ev): ?>
                        <tr>
                            <td style="font-weight:500"><?= htmlspecialchars($ev['title']) ?></td>
                            <td style="color:var(--pl)">
                                <?= date('d M Y', strtotime($ev['event_date'])) ?>
                            </td>
                            <td style="color:var(--muted)"><?= htmlspecialchars($ev['location']) ?></td>
                            <td><?= $ev['spots'] ?></td>
                            <td>
                                <div class="actions">
                                    <a href="?tab=events&edit_event=<?= $ev['id'] ?>" class="btn-e">Edit</a>
                                    <a href="?tab=events&delete_event=<?= $ev['id'] ?>"
                                       onclick="return confirm('Delete this event?')"
                                       class="btn-d">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- end section-block -->

    <!-- ════════════════════════════════
         TEAM TAB
    ════════════════════════════════ -->
    <?php elseif ($tab === 'team'): ?>

        <div class="page-header">
            <h1>Manage <span>Team</span></h1>
            <p>Add or update team members shown on the website.</p>
        </div>

        <div class="section-block">

            <!-- Edit team member form -->
            <?php if (isset($edit_team) && $edit_team): ?>
           <div class="edit-box">
    <h3>✏️ Editing: <?= htmlspecialchars($edit_team['name']) ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="team_id" value="<?= $edit_team['id'] ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="tm_name"
                       value="<?= htmlspecialchars($edit_team['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <input type="text" name="tm_role"
                       value="<?= htmlspecialchars($edit_team['role']) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>New Profile Photo (leave empty to keep current)</label>
                <input type="file" name="tm_photo" accept="image/*">
                <!-- Show current photo -->
                <?php
                $tp = 'images/team/' . $edit_team['photo'];
                if ($edit_team['photo'] !== 'placeholder' && file_exists($tp)): ?>
                    <img src="<?= $tp ?>" style="width:50px;height:50px;border-radius:50%;margin-top:8px;object-fit:cover;">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="tm_order" value="<?= $edit_team['sort_order'] ?>">
            </div>
        </div>
        <button type="submit" name="update_team" class="btn-update">Save Changes</button>
        <a href="admin.php?tab=team"
           style="margin-left:10px;font-size:0.82rem;color:var(--muted);text-decoration:none;">
            Cancel
        </a>
    </form>
</div>
            <?php endif; ?>

            <!-- Add team member form -->
<div class="form-card">
    <h2>➕ Add Team Member</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="tm_name" placeholder="e.g. Farhan Kabir" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <input type="text" name="tm_role" placeholder="e.g. President" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="tm_photo" accept="image/*">
            </div>
            <div class="form-group">
                <label>Sort Order (1 = first)</label>
                <input type="number" name="tm_order" value="99">
            </div>
        </div>
        <button type="submit" name="add_team" class="btn-submit">+ Add Member</button>
    </form>
</div>

            <!-- Team table -->
            <div class="table-card">
                <h2>All Members (<?= count($team) ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($team)): ?>
                        <tr><td colspan="5" class="empty-cell">No team members yet — add one above!</td></tr>
                    <?php else: ?>
                        <?php foreach ($team as $m): ?>
                        <tr>
                            <td>
                                <?php
                                $tp = 'images/team/' . $m['photo'];
                                if ($m['photo'] !== 'placeholder' && file_exists($tp)): ?>
                                    <img src="<?= $tp ?>" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                                <?php else: ?>
                                   <div style="font-size:1.6rem;width:44px;height:44px;display:flex;
                                   align-items:center;justify-content:center;background:var(--deep);
                                   border-radius:50%;">
                                   📸
                                  </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight:500"><?= htmlspecialchars($m['name']) ?></td>
                            <td style="color:var(--muted)"><?= htmlspecialchars($m['role']) ?></td>
                            <td style="color:var(--pl)"><?= $m['sort_order'] ?></td>
                            <td>
                                <div class="actions">
                                    <a href="?tab=team&edit_team=<?= $m['id'] ?>" class="btn-e">Edit</a>
                                    <a href="?tab=team&delete_team=<?= $m['id'] ?>"
                                       onclick="return confirm('Remove this member?')"
                                       class="btn-d">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- end section-block -->

    <?php endif; ?>

</main>

</body>
</html>