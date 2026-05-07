<?php
// Gallery data stored in a PHP array
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

#about { background: var(--bg-deep); padding: 100px 24px; }
.container { max-width: 1200px; margin: 0 auto; }
.section-tag { font-size: 0.72rem; letter-spacing: 0.15em; text-transform: uppercase; color: var(--purple-4); display: block; margin-bottom: 12px; }
.section-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 700; margin-bottom: 40px; }
.section-title em { font-style: italic; color: var(--purple-3); }

.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
.about-text p { color: var(--text-secondary); margin-bottom: 20px; font-size: 1.05rem; }
.about-features { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.feature-card {
    padding: 20px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    transition: all 0.3s ease;
}
.feature-card:hover { border-color: rgba(138,99,255,0.45); transform: translateY(-3px); }
.feature-icon { font-size: 1.6rem; margin-bottom: 10px; }
.feature-title { font-weight: 500; font-size: 0.9rem; color: var(--purple-4); margin-bottom: 4px; }
.feature-desc { font-size: 0.82rem; color: var(--text-muted); }

#gallery { padding: 100px 24px; }
.gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

.gallery-card {
    position: relative;
    aspect-ratio: 4/3;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border);
    cursor: pointer;
    background: var(--bg-card);
    transition: all 0.4s ease;
}
.gallery-card:hover { transform: scale(1.02); border-color: rgba(138,99,255,0.45); }

.gallery-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem;
    background: linear-gradient(135deg, var(--bg-card), #1e1540);
}

.gallery-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(0deg, rgba(7,5,15,0.9) 0%, transparent 50%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex; flex-direction: column; justify-content: flex-end;
    padding: 20px;
}
.gallery-card:hover .gallery-overlay { opacity: 1; }
.gallery-overlay h3 { font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; }
.gallery-overlay p  { font-size: 0.78rem; color: var(--purple-4); margin-top: 4px; }

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

<section id="gallery">
    <div class="container">
        <span class="section-tag">Portfolio</span>
        <h2 class="section-title">Our <em>Gallery</em></h2>

        <div class="gallery-grid">
            <?php foreach ($gallery_items as $item): ?>
                <div class="gallery-card" data-category="<?= htmlspecialchars($item['category']) ?>">
                    <div class="gallery-placeholder">
                        <span><?= $item['icon'] ?></span>
                    </div>
                    <div class="gallery-overlay">
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p>by <?= htmlspecialchars($item['photographer']) ?></p>
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