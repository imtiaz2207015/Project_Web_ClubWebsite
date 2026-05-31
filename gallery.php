<?php
session_start();
require 'db.php';

// Search query
$search   = htmlspecialchars(trim($_GET['q'] ?? ''));
$category = $_GET['category'] ?? 'all';

// Build query based on search and filter
if ($search) {
    $stmt = $pdo->prepare(
        "SELECT * FROM photos
         WHERE (title LIKE ? OR photographer LIKE ?)
         ORDER BY created_at DESC"
    );
    $stmt->execute(["%$search%", "%$search%"]);
    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $gallery_items = $pdo->query(
        "SELECT * FROM photos ORDER BY created_at DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

// Apply category filter
if ($category !== 'all') {
    $gallery_items = array_filter(
        $gallery_items,
        fn($item) => $item['category'] === $category
    );
}

$total = count($gallery_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Gallery — KUET Photography Society</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-void:#07050f; --bg-deep:#0d0a1a; --bg-card:#130f24;
            --border:rgba(138,99,255,0.18); --border-bright:rgba(138,99,255,0.45);
            --purple-1:#5b21b6; --purple-2:#7c3aed; --purple-3:#8b5cf6;
            --purple-4:#a78bfa; --accent:#e879f9;
            --text-primary:#f1eeff; --text-secondary:#9d8ec4; --text-muted:#5a4e7a;
            --glow-purple:0 0 40px rgba(139,92,246,0.35);
            --radius-sm:6px; --radius-md:12px; --radius-lg:20px;
            --font-display:'Cormorant Garamond',serif;
            --font-body:'DM Sans',sans-serif;
        }
        *,*::before,*::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body {
            background:var(--bg-void); color:var(--text-primary);
            font-family:var(--font-body); font-weight:300;
            line-height:1.7; overflow-x:hidden;
        }
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:var(--bg-deep); }
        ::-webkit-scrollbar-thumb { background:var(--purple-2); border-radius:3px; }

        /* Navbar */
        nav {
            position:fixed; top:0; left:0; right:0; z-index:100;
            display:flex; align-items:center; justify-content:space-between;
            padding:0 40px; height:70px;
            background:rgba(7,5,15,0.85); backdrop-filter:blur(20px);
            border-bottom:1px solid var(--border);
        }
        .nav-logo {
            display:flex; align-items:center; gap:10px; text-decoration:none;
        }
        .nav-logo-icon {
            width:36px; height:36px; border-radius:50%; overflow:hidden;
            background:linear-gradient(135deg,var(--purple-2),var(--accent));
            display:flex; align-items:center; justify-content:center;
        }
        .nav-logo-text {
            font-family:var(--font-display); font-size:1.1rem; font-weight:600;
            color:var(--text-primary); letter-spacing:0.04em;
        }
        .nav-logo-text span { color:var(--purple-4); }
        .back-btn {
            padding:8px 20px; border:1px solid var(--border-bright);
            border-radius:var(--radius-sm); color:var(--purple-4);
            text-decoration:none; font-size:0.85rem; transition:all 0.25s;
        }
        .back-btn:hover { background:rgba(139,92,246,0.1); }

        /* Page content */
        .page-wrap { max-width:1200px; margin:0 auto; padding:110px 24px 80px; }

        .page-header { margin-bottom:48px; }
        .page-header h1 {
            font-family:var(--font-display); font-size:clamp(2rem,4vw,3.2rem);
            font-weight:700; line-height:1.1;
        }
        .page-header h1 em { font-style:italic; color:var(--purple-3); }
        .page-header p { color:var(--text-secondary); margin-top:8px; }

        /* Search bar */
        .search-bar {
            display:flex; gap:12px; flex-wrap:wrap;
            align-items:center; margin-bottom:32px;
        }
        .search-input-wrap {
            position:relative; flex:1; min-width:240px;
        }
        .search-input-wrap input {
            width:100%; padding:12px 16px 12px 44px;
            background:var(--bg-card); border:1px solid var(--border);
            border-radius:var(--radius-sm); color:var(--text-primary);
            font-family:var(--font-body); font-size:0.95rem; outline:none;
            transition:border-color 0.25s, box-shadow 0.25s;
        }
        .search-input-wrap input:focus {
            border-color:var(--purple-3);
            box-shadow:0 0 0 3px rgba(139,92,246,0.15);
        }
        .search-input-wrap svg {
            position:absolute; left:14px; top:50%;
            transform:translateY(-50%); pointer-events:none;
        }
        .search-btn {
            padding:12px 24px;
            background:linear-gradient(135deg,var(--purple-2),var(--purple-1));
            color:#fff; border:none; border-radius:var(--radius-sm);
            font-family:var(--font-body); font-size:0.9rem; cursor:pointer;
            transition:all 0.2s;
        }
        .search-btn:hover { transform:translateY(-1px); }
        .clear-btn {
            padding:12px 20px; background:transparent;
            border:1px solid var(--border); color:var(--text-secondary);
            border-radius:var(--radius-sm); font-family:var(--font-body);
            font-size:0.9rem; cursor:pointer; text-decoration:none;
            transition:all 0.2s;
        }
        .clear-btn:hover { border-color:var(--border-bright); color:var(--text-primary); }

        /* Filter buttons */
        .filters {
            display:flex; gap:8px; flex-wrap:wrap; margin-bottom:40px;
        }
        .filter-btn {
            padding:8px 20px; border:1px solid var(--border);
            background:transparent; color:var(--text-secondary);
            border-radius:50px; font-family:var(--font-body);
            font-size:0.82rem; letter-spacing:0.06em; text-transform:uppercase;
            cursor:pointer; text-decoration:none; transition:all 0.25s;
        }
        .filter-btn:hover, .filter-btn.active {
            background:rgba(139,92,246,0.15);
            border-color:var(--purple-3); color:var(--purple-4);
        }
        .filter-btn.active {
            background:rgba(124,58,237,0.25);
            box-shadow:0 0 16px rgba(139,92,246,0.2);
        }

        /* Results info */
        .results-info {
            font-size:0.85rem; color:var(--text-muted);
            margin-bottom:28px;
        }
        .results-info strong { color:var(--purple-4); }

        /* Gallery grid */
        .gallery-grid {
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:20px;
        }
        .gallery-card {
            position:relative; aspect-ratio:4/3;
            border-radius:var(--radius-md); overflow:hidden;
            border:1px solid var(--border); cursor:pointer;
            transition:all 0.4s ease; background:var(--bg-card);
        }
        .gallery-card:hover {
            transform:scale(1.02);
            box-shadow:var(--glow-purple);
            border-color:var(--border-bright);
        }
        .gallery-card img {
            width:100%; height:100%; object-fit:cover;
            transition:transform 0.5s ease;
        }
        .gallery-card:hover img { transform:scale(1.08); }
        .gallery-placeholder {
            width:100%; height:100%;
            display:flex; flex-direction:column;
            align-items:center; justify-content:center;
            background:linear-gradient(135deg,var(--bg-card),#1e1540);
            gap:10px;
        }
        .gallery-overlay {
            position:absolute; inset:0;
            background:linear-gradient(0deg,rgba(7,5,15,0.9) 0%,transparent 50%);
            opacity:0; transition:opacity 0.3s ease;
            display:flex; flex-direction:column;
            justify-content:flex-end; padding:20px;
        }
        .gallery-card:hover .gallery-overlay { opacity:1; }
        .gallery-overlay h3 {
            font-family:var(--font-display); font-size:1.1rem;
            color:var(--text-primary);
        }
        .gallery-overlay p {
            font-size:0.78rem; color:var(--purple-4); margin-top:4px;
        }

        /* Empty state */
        .empty-state {
            text-align:center; padding:80px 24px;
            color:var(--text-muted);
        }
        .empty-state h3 {
            font-family:var(--font-display); font-size:1.8rem;
            margin-bottom:8px; color:var(--text-secondary);
        }

        @media (max-width:900px) {
            nav { padding:0 20px; }
            .gallery-grid { grid-template-columns:repeat(2,1fr); }
        }
        @media (max-width:580px) {
            .gallery-grid { grid-template-columns:1fr; }
            .page-wrap { padding:100px 16px 60px; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <a href="index.php" class="nav-logo">
        <div class="nav-logo-icon">
            <?php if (file_exists(__DIR__ . '/Mainlogo.png')): ?>
                <img src="Mainlogo.png" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M23 19C23 20.1 22.1 21 21 21H3C1.9 21 1 20.1 1 19V8C1 6.9 1.9 6 3 6H7L9 3H15L17 6H21C22.1 6 23 6.9 23 8V19Z"
                          stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="13" r="4" stroke="white" stroke-width="1.5"/>
                </svg>
            <?php endif; ?>
        </div>
        <span class="nav-logo-text">KUET <span>Photo</span></span>
    </a>
    <a href="index.php#gallery" class="back-btn">← Back to Home</a>
</nav>

<div class="page-wrap">

    <!-- Header -->
    <div class="page-header">
        <h1>Our <em>Gallery</em></h1>
        <p><?= $total ?> photo<?= $total !== 1 ? 's' : '' ?> in the collection</p>
    </div>

    <!-- Search form -->
    <form method="GET" action="gallery.php">
        <div class="search-bar">
            <div class="search-input-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     style="opacity:0.5;">
                    <circle cx="11" cy="11" r="8" stroke="var(--purple-4)" stroke-width="1.5"/>
                    <path d="M21 21l-4.35-4.35" stroke="var(--purple-4)" stroke-width="1.5"
                          stroke-linecap="round"/>
                </svg>
                <input type="text" name="q"
                       value="<?= $search ?>"
                       placeholder="Search by title or photographer...">
            </div>
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
            <button type="submit" class="search-btn">Search</button>
            <?php if ($search || $category !== 'all'): ?>
                <a href="gallery.php" class="clear-btn">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Category filters -->
    <div class="filters">
        <?php
        $cats = ['all'=>'All','nature'=>'Nature','portrait'=>'Portrait',
                 'architecture'=>'Architecture','event'=>'Events'];
        foreach ($cats as $key => $label):
            $active = ($category === $key) ? 'active' : '';
            $url    = 'gallery.php?category=' . $key . ($search ? '&q='.urlencode($search) : '');
        ?>
            <a href="<?= $url ?>" class="filter-btn <?= $active ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Results info -->
    <?php if ($search || $category !== 'all'): ?>
    <div class="results-info">
        <?php if ($search): ?>
            Showing <strong><?= $total ?></strong> result<?= $total !== 1 ? 's' : '' ?>
            for "<strong><?= $search ?></strong>"
        <?php else: ?>
            Showing <strong><?= $total ?></strong> photo<?= $total !== 1 ? 's' : '' ?>
            in <strong><?= $cats[$category] ?></strong>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Gallery grid -->
    <?php if (empty($gallery_items)): ?>
        <div class="empty-state">
            <h3>No photos found</h3>
            <p>Try a different search term or category.</p>
            <a href="gallery.php"
               style="display:inline-block;margin-top:20px;padding:10px 24px;
                      background:rgba(124,58,237,0.15);border:1px solid var(--border-bright);
                      color:var(--purple-4);text-decoration:none;border-radius:var(--radius-sm);">
                View All Photos
            </a>
        </div>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($gallery_items as $item): ?>
            <div class="gallery-card">
                <?php
                $ip = 'images/' . $item['filename'];
                $if = __DIR__ . '/' . $ip;
                if ($item['filename'] !== 'placeholder' && file_exists($if)):
                ?>
                    <img src="<?= $ip ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                <?php else: ?>
                    <div class="gallery-placeholder">
                        <div style="width:40px;height:40px;border-radius:50%;
                                    background:rgba(124,58,237,0.2);
                                    border:1px solid var(--border-bright);
                                    display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M23 19C23 20.1 22.1 21 21 21H3C1.9 21 1 20.1 1 19V8C1 6.9 1.9 6 3 6H7L9 3H15L17 6H21C22.1 6 23 6.9 23 8V19Z"
                                      stroke="var(--purple-4)" stroke-width="1.5"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="13" r="4" stroke="var(--purple-4)" stroke-width="1.5"/>
                            </svg>
                        </div>
                        <span style="font-size:0.75rem;color:var(--text-muted);margin-top:8px;">
                            No image
                        </span>
                    </div>
                <?php endif; ?>
                <div class="gallery-overlay">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p>by <?= htmlspecialchars($item['photographer']) ?></p>
                    <span style="font-size:0.72rem;color:var(--text-muted);margin-top:2px;text-transform:uppercase;letter-spacing:0.06em;">
                        <?= $item['category'] ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div><!-- end page-wrap -->

</body>
</html>