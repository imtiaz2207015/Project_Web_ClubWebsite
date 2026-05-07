<?php

$gallery_items = [
    ['title' => 'Golden Hour',     'category' => 'nature',       'photographer' => 'Arif Hossain',  'icon' => '🌅'],
    ['title' => 'Steel and Glass', 'category' => 'architecture', 'photographer' => 'Nadia Islam',   'icon' => '🏛️'],
    ['title' => 'Silent Portrait', 'category' => 'portrait',     'photographer' => 'Rahim Uddin',   'icon' => '👤'],
    ['title' => 'Monsoon Fest',    'category' => 'event',        'photographer' => 'Priya Das',     'icon' => '🎉'],
    ['title' => 'Misty Morning',   'category' => 'nature',       'photographer' => 'Karim Sheikh',  'icon' => '🌿'],
    ['title' => 'Campus Walk',     'category' => 'architecture', 'photographer' => 'Tania Begum',   'icon' => '🏫'],
];
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
            --bg-void:     #07050f;
            --bg-deep:     #0d0a1a;
            --bg-card:     #130f24;
            --purple-2:    #7c3aed;
            --purple-3:    #8b5cf6;
            --purple-4:    #a78bfa;
            --accent:      #e879f9;
            --text-primary:   #f1eeff;
            --text-secondary: #9d8ec4;
            --text-muted:     #5a4e7a;
            --border:      rgba(138, 99, 255, 0.18);
        }

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-void);
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            line-height: 1.7;
        }

        /* NAVBAR */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 70px;
            background: rgba(7, 5, 15, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }

        nav a {
            color: var(--text-primary);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all 0.25s ease;
        }

        .nav-links a:hover {
            color: var(--text-primary);
            background: rgba(139, 92, 246, 0.12);
        }

        /* HERO */
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
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav id="navbar">
    <a href="#hero">KUET <span style="color:var(--purple-4)">Photo</span></a>

    <ul class="nav-links">
        <li><a href="#about">About</a></li>
        <li><a href="#gallery">Gallery</a></li>
        <li><a href="#events">Events</a></li>
        <li><a href="#team">Team</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>


<section id="hero">
    <div class="hero-badge">KUET Photography Society — Est. 2010</div>

    <h1 class="hero-title">
        We Capture<br>
        <span class="line-accent">Every Moment!</span>
    </h1><br>

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

<!-- FOOTER -->
<footer>
    <p>KUET Photography Society</p>
</footer>

</body>
</html>