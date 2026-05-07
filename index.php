<?php
// Gallery data stored in a PHP array
$gallery_items = [
    ['title' => 'Golden Hour',     'category' => 'nature',       'photographer' => 'Araf Hasan',    'icon' => '🌅'],
    ['title' => 'Steel and Glass', 'category' => 'architecture', 'photographer' => 'Nadia Imtiaz', 'icon' => '🏛️'],
    ['title' => 'Silent Portrait', 'category' => 'portrait',     'photographer' => 'Rahim Khan',   'icon' => '👤'],
    ['title' => 'Monsoon Fest',    'category' => 'event',        'photographer' => 'Anwesha Das',  'icon' => '🎉'],
    ['title' => 'Misty Morning',   'category' => 'nature',       'photographer' => 'Karim Sheikh', 'icon' => '🌿'],
    ['title' => 'Campus Walk',     'category' => 'architecture', 'photographer' => 'Fabiha Begum', 'icon' => '🏫'],
    ['title' => 'Joyful Chaos',    'category' => 'event',        'photographer' => 'Sanzida Alam', 'icon' => '🎊'],
    ['title' => 'Ethereal Gaze',   'category' => 'portrait',     'photographer' => 'Sumi Akter',   'icon' => '👁️'],
];

// Events data
$events = [
    ['date' => '15 May 2025', 'title' => 'Street Photography Walk', 'location' => 'Khulna City',     'spots' => 20],
    ['date' => '22 Jun 2025', 'title' => 'Portrait Workshop',       'location' => 'KUET Campus',     'spots' => 15],
    ['date' => '10 Jul 2025', 'title' => 'Monsoon Photo Contest',   'location' => 'Online',          'spots' => 50],
    ['date' => '05 Aug 2025', 'title' => 'Annual Exhibition 2025',  'location' => 'KUET Auditorium', 'spots' => 100],
];

// Gallery filter using GET
$allowed_filters = ['all', 'nature', 'portrait', 'architecture', 'event'];
$active_filter = 'all';

if (isset($_GET['filter']) && in_array($_GET['filter'], $allowed_filters)) {
    $active_filter = $_GET['filter'];
}

// Filter gallery items
$filtered = ($active_filter === 'all')
    ? $gallery_items
    : array_filter($gallery_items, fn($item) => $item['category'] === $active_filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUET Photography Society</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>

        :root {
            --bg-void: #07050f;
            --bg-deep: #0d0a1a;
            --bg-card: #130f24;
            --purple-2: #7c3aed;
            --purple-3: #8b5cf6;
            --purple-4: #a78bfa;
            --accent: #e879f9;
            --text-primary: #f1eeff;
            --text-secondary: #9d8ec4;
            --text-muted: #5a4e7a;
            --border: rgba(138, 99, 255, 0.18);
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        html{
            scroll-behavior:smooth;
        }

        body{
            background:var(--bg-void);
            color:var(--text-primary);
            font-family:'DM Sans',sans-serif;
            line-height:1.7;
        }

        .container{
            max-width:1200px;
            margin:0 auto;
        }

        /* NAVBAR */

        nav{
            position:fixed;
            top:0;
            left:0;
            right:0;
            z-index:100;
            height:70px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 40px;
            background:rgba(7,5,15,0.85);
            backdrop-filter:blur(20px);
            border-bottom:1px solid var(--border);
        }

        nav a{
            color:var(--text-primary);
            text-decoration:none;
            font-weight:600;
        }

        .nav-links{
            display:flex;
            list-style:none;
            gap:8px;
        }

        .nav-links a{
            color:var(--text-secondary);
            font-size:0.85rem;
            text-transform:uppercase;
            letter-spacing:0.06em;
            padding:8px 14px;
            border-radius:6px;
            transition:0.3s;
        }

        .nav-links a:hover{
            background:rgba(139,92,246,0.12);
            color:var(--text-primary);
        }

        #hero {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 120px 24px 80px;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    border: 1px solid rgba(138, 99, 255, 0.45);
    border-radius: 50px;
    font-size: 0.78rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--purple-4);
    margin-bottom: 28px;
}

.hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(3rem, 8vw, 7rem);
    font-weight: 700;
    line-height: 1.05;
    margin-bottom: 20px;
}

.line-accent {
    display: block;
    background: linear-gradient(90deg, var(--purple-3), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-sub {
    font-size: 1.1rem;
    color: var(--text-secondary);
    max-width: 520px;
    margin: 0 auto 40px;
}

.hero-cta { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

.btn-primary {
    padding: 14px 32px;
    background: linear-gradient(135deg, var(--purple-2), #5b21b6);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 24px rgba(124, 58, 237, 0.4);
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(124,58,237,0.6); }

.btn-outline {
    padding: 14px 32px;
    background: transparent;
    color: var(--text-primary);
    border: 1px solid rgba(138, 99, 255, 0.45);
    border-radius: 6px;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
}
.btn-outline:hover { background: rgba(139, 92, 246, 0.1); }

.hero-stats { display: flex; gap: 60px; justify-content: center; margin-top: 80px; flex-wrap: wrap; }
.stat-num { font-family: 'Cormorant Garamond', serif; font-size: 2.8rem; font-weight: 700; color: var(--purple-4); }
.stat-label { font-size: 0.78rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); margin-top: 4px; }

        /* ABOUT */

        #about{
            padding:120px 24px 100px;
            background:var(--bg-deep);
        }

        .section-tag{
            display:block;
            margin-bottom:12px;
            font-size:0.72rem;
            letter-spacing:0.15em;
            text-transform:uppercase;
            color:var(--purple-4);
        }

        .section-title{
            font-family:'Cormorant Garamond',serif;
            font-size:clamp(2rem,4vw,3.2rem);
            margin-bottom:40px;
        }

        .section-title em{
            color:var(--purple-3);
        }

        .about-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:60px;
        }

        .about-text p{
            color:var(--text-secondary);
            margin-bottom:20px;
        }

        .about-features{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
        }

        .feature-card{
            background:var(--bg-card);
            border:1px solid var(--border);
            border-radius:12px;
            padding:20px;
            transition:0.3s;
        }

        .feature-card:hover{
            transform:translateY(-3px);
            border-color:rgba(138,99,255,0.45);
        }

        .feature-icon{
            font-size:1.6rem;
            margin-bottom:10px;
        }

        .feature-title{
            color:var(--purple-4);
            margin-bottom:4px;
        }

        .feature-desc{
            font-size:0.82rem;
            color:var(--text-muted);
        }

        /* GALLERY */

        #gallery{
            padding:100px 24px;
        }

        .gallery-filters{
            display:flex;
            justify-content:center;
            gap:10px;
            flex-wrap:wrap;
            margin-bottom:48px;
        }

        .filter-btn{
            padding:9px 22px;
            border:1px solid var(--border);
            border-radius:50px;
            color:var(--text-secondary);
            text-decoration:none;
            font-size:0.82rem;
            text-transform:uppercase;
            letter-spacing:0.06em;
            transition:0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active{
            background:rgba(124,58,237,0.2);
            border-color:var(--purple-3);
            color:var(--purple-4);
        }

        .gallery-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:20px;
        }

        .gallery-card{
            position:relative;
            aspect-ratio:4/3;
            overflow:hidden;
            border-radius:12px;
            background:var(--bg-card);
            border:1px solid var(--border);
            transition:0.4s;
        }

        .gallery-card:hover{
            transform:scale(1.02);
            border-color:rgba(138,99,255,0.45);
        }

        .gallery-placeholder{
            width:100%;
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:3rem;
            background:linear-gradient(135deg,var(--bg-card),#1e1540);
        }

        .gallery-overlay{
            position:absolute;
            inset:0;
            opacity:0;
            padding:20px;
            display:flex;
            flex-direction:column;
            justify-content:flex-end;
            background:linear-gradient(0deg,rgba(7,5,15,0.9),transparent);
            transition:0.3s;
        }

        .gallery-card:hover .gallery-overlay{
            opacity:1;
        }

        .gallery-overlay h3{
            font-family:'Cormorant Garamond',serif;
        }

        .gallery-overlay p{
            color:var(--purple-4);
            font-size:0.8rem;
        }

        /* EVENTS */

        #events{
            padding:100px 24px;
            background:var(--bg-deep);
        }

        .events-list{
            display:flex;
            flex-direction:column;
            gap:16px;
        }

        .event-card{
            display:grid;
            grid-template-columns:90px 1fr;
            gap:24px;
            align-items:center;
            padding:24px 28px;
            background:var(--bg-card);
            border:1px solid var(--border);
            border-radius:12px;
            transition:0.3s;
        }

        .event-card:hover{
            transform:translateX(4px);
            border-color:rgba(138,99,255,0.45);
        }

        .event-date{
            text-align:center;
            padding:12px;
            border-radius:6px;
            background:rgba(124,58,237,0.15);
        }

        .day{
            font-size:1.8rem;
            font-weight:700;
            color:var(--purple-4);
        }

        .month{
            font-size:0.72rem;
            color:var(--text-muted);
        }

        .event-meta{
            display:flex;
            gap:20px;
            color:var(--text-muted);
            font-size:0.82rem;
        }

        footer{
            padding:30px;
            text-align:center;
            border-top:1px solid var(--border);
            color:var(--text-muted);
        }

    </style>
</head>

<body>

<!-- NAVBAR -->

<nav>
    <a href="#about">
        KUET <span style="color:var(--purple-4)">Photo</span>
    </a>

    <ul class="nav-links">
        <li><a href="#about">About</a></li>
        <li><a href="#gallery">Gallery</a></li>
        <li><a href="#events">Events</a></li>
    </ul>
</nav>

<!-- HERO -->

<section id="hero">
    <div class="hero-badge">KUET Photography Society — Est. 2010</div>

    <h1 class="hero-title">
        We Capture<br>
        <span class="line-accent">Every Moment</span>
    </h1>

    <p class="hero-sub">
        A creative community of photographers at KUET,
        united by the art of visual storytelling.
    </p>

    <div class="hero-cta">
        <a href="#contact" class="btn-primary">Join the Club</a>
        <a href="#gallery" class="btn-outline">View Gallery</a>
    </div>

    <div class="hero-stats">
        <div class="stat-item">
            <div class="stat-num">200+</div>
            <div class="stat-label">Members</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">50+</div>
            <div class="stat-label">Events</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">1200+</div>
            <div class="stat-label">Photos</div>
        </div>
    </div>
</section>

<!-- ABOUT -->

<section id="about">
    <div class="container">
        <span class="section-tag">About Us</span>
        <h2 class="section-title">Passion for <em>Photography</em></h2>

        <div class="about-grid">
            <div class="about-text">
                <p>KUET Photography Society is the official photography club of
                Khulna University of Engineering & Technology.</p>
                <p>From street photography to portraits — our members explore
                every genre and grow together through workshops and contests.</p>
            </div>
            <div class="about-features">
                <div class="feature-card">
                    <div class="feature-icon">🎓</div>
                    <div class="feature-title">Workshops</div>
                    <div class="feature-desc">Regular sessions led by professionals</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏆</div>
                    <div class="feature-title">Contests</div>
                    <div class="feature-desc">Monthly photo contests with prizes</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚶</div>
                    <div class="feature-title">Photo Walks</div>
                    <div class="feature-desc">Explore Khulna with your camera</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🖼️</div>
                    <div class="feature-title">Exhibitions</div>
                    <div class="feature-desc">Annual exhibitions of member work</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GALLERY -->

<section id="gallery">

    <div class="container">

        <span class="section-tag">Portfolio</span>

        <h2 class="section-title">
            Our <em>Gallery</em>
        </h2>

        <div class="gallery-filters">

            <?php
            $labels = [
                'all' => 'All',
                'nature' => 'Nature',
                'portrait' => 'Portrait',
                'architecture' => 'Architecture',
                'event' => 'Events'
            ];

            foreach ($labels as $key => $label):

                $active_class = ($active_filter === $key)
                    ? 'active'
                    : '';
            ?>

                <a href="?filter=<?= $key ?>#gallery"
                   class="filter-btn <?= $active_class ?>">

                    <?= $label ?>

                </a>

            <?php endforeach; ?>

        </div>

        <div class="gallery-grid">

            <?php foreach ($filtered as $item): ?>

                <div class="gallery-card">

                    <div class="gallery-placeholder">
                        <?= htmlspecialchars($item['icon']) ?>
                    </div>

                    <div class="gallery-overlay">

                        <h3>
                            <?= htmlspecialchars($item['title']) ?>
                        </h3>

                        <p>
                            by <?= htmlspecialchars($item['photographer']) ?>
                        </p>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- EVENTS -->

<section id="events">

    <div class="container">

        <span class="section-tag">Upcoming</span>

        <h2 class="section-title">
            Club <em>Events</em>
        </h2>

        <div class="events-list">

            <?php foreach ($events as $ev):

                $parts = explode(' ', $ev['date']);

                $day = $parts[0];
                $month = $parts[1] . ' ' . $parts[2];
            ?>

            <div class="event-card">

                <div class="event-date">

                    <div class="day">
                        <?= $day ?>
                    </div>

                    <div class="month">
                        <?= $month ?>
                    </div>

                </div>

                <div class="event-info">

                    <h3>
                        <?= htmlspecialchars($ev['title']) ?>
                    </h3>

                    <div class="event-meta">

                        <span>
                            📍 <?= htmlspecialchars($ev['location']) ?>
                        </span>

                        <span>
                            👥 <?= $ev['spots'] ?> spots
                        </span>

                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- FOOTER -->

<footer>
    <p>KUET Photography Society</p>
</footer>

</body>
</html>