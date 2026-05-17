<?php
session_start();
require 'db.php';  // connect to MySQL

// Load photos from database
$gallery_items = $pdo->query("SELECT * FROM photos ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Load events from database
$events_raw = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// Format events for display
$events = [];
foreach ($events_raw as $ev) {
    $events[] = [
        'date'     => date('d M Y', strtotime($ev['event_date'])),
        'title'    => $ev['title'],
        'location' => $ev['location'],
        'spots'    => $ev['spots'],
    ];
}

// ADD THIS — was completely missing
$team = $pdo->query("SELECT * FROM team ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

// ADD THIS — contact form handling was also missing
$form_success = false;
$form_errors  = [];
$sticky = ['name'=>'', 'email'=>'', 'subject'=>'', 'message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $sticky['name']    = htmlspecialchars(trim($_POST['name']    ?? ''));
    $sticky['email']   = htmlspecialchars(trim($_POST['email']   ?? ''));
    $sticky['subject'] = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $sticky['message'] = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (empty($sticky['name']))    $form_errors[] = "Name is required.";
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $form_errors[] = "Valid email is required.";
    if (empty($sticky['message'])) $form_errors[] = "Message cannot be empty.";

    if (empty($form_errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO messages (name, email, subject, message) VALUES (?,?,?,?)"
        );
        $stmt->execute([
            $sticky['name'],
            $sticky['email'],
            $sticky['subject'],
            $sticky['message']
        ]);
        $form_success = true;
        $sticky = ['name'=>'','email'=>'','subject'=>'','message'=>''];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUET Photography Society</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           CSS VARIABLES — DARK PURPLE THEME
        ============================================= */
        :root {
            --bg-void:       #07050f;
            --bg-deep:       #0d0a1a;
            --bg-card:       #130f24;
            --bg-card-hover: #1c1635;
            --border:        rgba(138, 99, 255, 0.18);
            --border-bright: rgba(138, 99, 255, 0.45);

            --purple-1:  #5b21b6;
            --purple-2:  #7c3aed;
            --purple-3:  #8b5cf6;
            --purple-4:  #a78bfa;
            --purple-5:  #c4b5fd;
            --accent:    #e879f9;   /* fuchsia pop */
            --gold:      #f59e0b;

            --text-primary:   #f1eeff;
            --text-secondary: #9d8ec4;
            --text-muted:     #5a4e7a;

            --glow-purple: 0 0 40px rgba(139, 92, 246, 0.35);
            --glow-accent:  0 0 30px rgba(232, 121, 249, 0.3);

            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 20px;

            --font-display: 'Cormorant Garamond', serif;
            --font-body:    'DM Sans', sans-serif;
        }

        /* =============================================
           RESET & BASE
        ============================================= */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; font-size: 16px; }

        body {
            background-color: var(--bg-void);
            color: var(--text-primary);
            font-family: var(--font-body);
            font-weight: 300;
            line-height: 1.7;
            overflow-x: hidden;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-deep); }
        ::-webkit-scrollbar-thumb { background: var(--purple-2); border-radius: 3px; }

        /* =============================================
           NOISE TEXTURE OVERLAY
        ============================================= */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 999;
            opacity: 0.4;
        }

        /* =============================================
           NAVIGATION
        ============================================= */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
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

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--purple-2), var(--accent));
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .nav-logo-text {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: 0.04em;
        }
        .nav-logo-text span { color: var(--purple-4); }

        .nav-links {
            display: flex;
            gap: 8px;
            list-style: none;
        }
        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 400;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            transition: all 0.25s ease;
        }
        .nav-links a:hover {
            color: var(--text-primary);
            background: rgba(139, 92, 246, 0.12);
        }

        /* Hamburger */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 8px;
            background: none;
            border: none;
        }
        .hamburger span {
            display: block;
            width: 24px; height: 2px;
            background: var(--text-primary);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* =============================================
           HERO SECTION
        ============================================= */
        #hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 24px 80px;
            position: relative;
            overflow: hidden;
        }

        /* Radial glow backgrounds */
        #hero::before {
            content: '';
            position: absolute;
            top: 20%; left: 50%;
            transform: translate(-50%, -50%);
            width: 700px; height: 700px;
            background: radial-gradient(ellipse, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        #hero::after {
            content: '';
            position: absolute;
            bottom: 10%; right: 10%;
            width: 400px; height: 400px;
            background: radial-gradient(ellipse, rgba(232, 121, 249, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border: 1px solid var(--border-bright);
            border-radius: 50px;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--purple-4);
            background: rgba(91, 33, 182, 0.12);
            margin-bottom: 28px;
            animation: fadeInDown 0.8s ease both;
        }
        .hero-badge::before { content: '●'; font-size: 0.5rem; color: var(--accent); }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(3rem, 8vw, 7rem);
            font-weight: 700;
            line-height: 1.05;
            letter-spacing: -0.01em;
            margin-bottom: 20px;
            animation: fadeInUp 0.9s ease 0.1s both;
        }
        .hero-title .line-accent {
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
            animation: fadeInUp 0.9s ease 0.2s both;
        }

        .hero-cta {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.9s ease 0.3s both;
        }

        .btn-primary {
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--purple-2), var(--purple-1));
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 24px rgba(124, 58, 237, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(124, 58, 237, 0.6);
        }

        .btn-outline {
            padding: 14px 32px;
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-bright);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 400;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: var(--purple-3);
        }

        .hero-stats {
            display: flex;
            gap: 60px;
            justify-content: center;
            margin-top: 80px;
            flex-wrap: wrap;
            animation: fadeInUp 0.9s ease 0.4s both;
        }
        .stat-item { text-align: center; }
        .stat-num {
            font-family: var(--font-display);
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--purple-4);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.78rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* =============================================
           SECTION COMMON STYLES
        ============================================= */
        section { padding: 100px 24px; }

        .section-header {
            text-align: center;
            margin-bottom: 64px;
        }
        .section-tag {
            display: inline-block;
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--purple-4);
            margin-bottom: 12px;
        }
        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 700;
            line-height: 1.1;
        }
        .section-title em {
            font-style: italic;
            color: var(--purple-3);
        }
        .section-divider {
            width: 60px; height: 2px;
            background: linear-gradient(90deg, var(--purple-2), var(--accent));
            margin: 20px auto 0;
            border-radius: 2px;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* =============================================
           ABOUT SECTION
        ============================================= */
        #about { background: var(--bg-deep); }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-text p {
            color: var(--text-secondary);
            margin-bottom: 20px;
            font-size: 1.05rem;
        }

        .about-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 32px;
        }
        .feature-card {
            padding: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            border-color: var(--border-bright);
            transform: translateY(-3px);
            box-shadow: var(--glow-purple);
        }
        .feature-icon { font-size: 1.6rem; margin-bottom: 10px; }
        .feature-title {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--purple-4);
            margin-bottom: 4px;
        }
        .feature-desc { font-size: 0.82rem; color: var(--text-muted); }

        .about-visual {
            position: relative;
        }
        .about-img-frame {
            width: 100%;
            aspect-ratio: 4/5;
            background: linear-gradient(135deg, var(--bg-card) 0%, #1e1540 100%);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            position: relative;
            overflow: hidden;
        }
        .about-img-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 30%, rgba(124, 58, 237, 0.2), transparent 60%);
        }
        .about-img-frame img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: var(--radius-lg);
        }

        /* =============================================
           GALLERY SECTION
        ============================================= */
        #gallery { background: var(--bg-void); }

        .gallery-filters {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 48px;
        }
        .filter-btn {
            padding: 9px 22px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-secondary);
            border-radius: 50px;
            font-family: var(--font-body);
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(139, 92, 246, 0.15);
            border-color: var(--purple-3);
            color: var(--purple-4);
        }
        .filter-btn.active {
            background: rgba(124, 58, 237, 0.25);
            box-shadow: 0 0 16px rgba(139, 92, 246, 0.2);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .gallery-card {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.4s ease;
            background: var(--bg-card);
        }
        .gallery-card:hover {
            transform: scale(1.02);
            box-shadow: var(--glow-purple);
            border-color: var(--border-bright);
        }
        .gallery-card img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-card:hover img { transform: scale(1.08); }

        /* Placeholder when no image loaded */
        .gallery-placeholder {
            width: 100%; height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bg-card), #1e1540);
            gap: 10px;
            font-size: 2.5rem;
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(7,5,15,0.9) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
        }
        .gallery-card:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay h3 {
            font-family: var(--font-display);
            font-size: 1.1rem;
            color: var(--text-primary);
        }
        .gallery-overlay p {
            font-size: 0.78rem;
            color: var(--purple-4);
            margin-top: 4px;
        }

        .gallery-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px;
            color: var(--text-muted);
        }

        /* =============================================
           EVENTS SECTION
        ============================================= */
        #events { background: var(--bg-deep); }

        .events-list { display: flex; flex-direction: column; gap: 16px; }

        .event-card {
            display: grid;
            grid-template-columns: 90px 1fr auto;
            gap: 24px;
            align-items: center;
            padding: 24px 28px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }
        .event-card:hover {
            border-color: var(--border-bright);
            box-shadow: var(--glow-purple);
            transform: translateX(4px);
        }

        .event-date {
            text-align: center;
            padding: 12px;
            background: rgba(124, 58, 237, 0.15);
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-bright);
        }
        .event-date .day {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--purple-4);
            line-height: 1;
        }
        .event-date .month {
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .event-info h3 {
            font-family: var(--font-display);
            font-size: 1.2rem;
            margin-bottom: 6px;
        }
        .event-meta {
            display: flex;
            gap: 20px;
            font-size: 0.82rem;
            color: var(--text-muted);
        }
        .event-meta span::before { margin-right: 4px; }

        .event-spots {
            font-size: 0.8rem;
            padding: 6px 16px;
            background: rgba(232, 121, 249, 0.1);
            border: 1px solid rgba(232, 121, 249, 0.25);
            border-radius: 50px;
            color: var(--accent);
            white-space: nowrap;
        }

        /* =============================================
           TEAM SECTION
        ============================================= */
        #team { background: var(--bg-void); }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .team-card {
            padding: 32px 24px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            text-align: center;
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
        }
        .team-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--purple-2), var(--accent));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .team-card:hover { border-color: var(--border-bright); transform: translateY(-6px); box-shadow: var(--glow-purple); }
        .team-card:hover::before { transform: scaleX(1); }

        .team-icon {
            font-size: 2.8rem;
            width: 72px; height: 72px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.12);
            border: 1px solid var(--border-bright);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .team-name {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .team-role {
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--purple-4);
            margin-top: 4px;
        }

        /* =============================================
           CONTACT SECTION
        ============================================= */
        #contact { background: var(--bg-deep); }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 60px;
            align-items: start;
        }

        .contact-info h3 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            margin-bottom: 16px;
        }
        .contact-info p { color: var(--text-secondary); margin-bottom: 32px; }

        .contact-detail {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }
        .contact-detail:last-child { border-bottom: none; }
        .contact-detail-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-sm);
            background: rgba(124, 58, 237, 0.15);
            border: 1px solid var(--border-bright);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .contact-detail-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; }
        .contact-detail-value { font-size: 0.95rem; color: var(--text-primary); }

        /* Form */
        .contact-form {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px;
        }

        .form-group { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        label {
            display: block;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-deep);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--purple-3);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        }
        textarea { height: 120px; resize: vertical; }
        select option { background: var(--bg-deep); }

        .form-success {
            padding: 16px;
            background: rgba(139, 92, 246, 0.12);
            border: 1px solid var(--border-bright);
            border-radius: var(--radius-sm);
            color: var(--purple-4);
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .form-errors {
            padding: 16px;
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            border-radius: var(--radius-sm);
            color: #fca5a5;
            margin-bottom: 20px;
            font-size: 0.88rem;
        }
        .form-errors ul { list-style: none; }
        .form-errors li::before { content: '• '; }

        /* =============================================
           FOOTER
        ============================================= */
        footer {
            background: var(--bg-void);
            border-top: 1px solid var(--border);
            padding: 48px 24px 32px;
            text-align: center;
        }
        .footer-logo {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
        }
        .footer-logo span { color: var(--purple-4); }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: var(--purple-4); }
        .footer-copy {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 24px;
        }

        /* =============================================
           ANIMATIONS
        ============================================= */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 900px) {
            nav { padding: 0 20px; }
            .nav-links { display: none; flex-direction: column; position: absolute; top: 70px; left: 0; right: 0; background: var(--bg-deep); padding: 20px; border-bottom: 1px solid var(--border); }
            .nav-links.open { display: flex; }
            .hamburger { display: flex; }

            .about-grid,
            .contact-grid { grid-template-columns: 1fr; gap: 40px; }
            .about-features { grid-template-columns: 1fr 1fr; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .team-grid { grid-template-columns: repeat(2, 1fr); }
            .event-card { grid-template-columns: 70px 1fr; }
            .event-spots { display: none; }
        }

        @media (max-width: 580px) {
            section { padding: 70px 16px; }
            .gallery-grid { grid-template-columns: 1fr; }
            .team-grid { grid-template-columns: 1fr 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .hero-stats { gap: 30px; }
            .contact-form { padding: 24px; }
        }

        /* =============================================
   3-DOT INFO MENU
============================================= */
.info-dots { position: relative; margin-left: 8px; }

.dots-btn {
    background: none;
    border: 1px solid var(--border);
    color: var(--text-secondary);
    font-size: 1.4rem;
    width: 38px; height: 38px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    padding-bottom: 4px;
    line-height: 1;
}
.dots-btn:hover {
    background: rgba(139,92,246,0.12);
    border-color: var(--border-bright);
    color: var(--text-primary);
}
.dots-menu {
    display: none;
    position: absolute;
    top: 48px; right: 0;
    background: var(--bg-card);
    border: 1px solid var(--border-bright);
    border-radius: 12px;
    padding: 8px;
    min-width: 200px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.5);
    z-index: 500;
}
.dots-menu.open { display: block; }
.dots-section-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--text-muted);
    padding: 6px 12px 4px;
}
.dots-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.88rem;
    border-radius: 7px;
    transition: all 0.2s;
}
.dots-item:hover {
    background: rgba(139,92,246,0.12);
    color: var(--text-primary);
}
.dots-admin { color: var(--purple-4) !important; }
.dots-admin:hover { background: rgba(124,58,237,0.15) !important; }
.dots-divider {
    height: 1px;
    background: var(--border);
    margin: 6px 8px;
}

/* Floating side button */
.floating-dots {
    position: fixed;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 400;
}
.floating-btn {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: rgba(124,58,237,0.3);
    border: 1px solid var(--border-bright);
    color: var(--text-primary);
    font-size: 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-bottom: 4px;
    box-shadow: 0 4px 20px rgba(124,58,237,0.3);
    transition: all 0.2s;
}
.floating-btn:hover {
    background: rgba(124,58,237,0.5);
    box-shadow: 0 6px 28px rgba(124,58,237,0.5);
}
    </style>
</head>
<body>

<!-- =============================================
     NAVIGATION
============================================= -->
<nav id="navbar">
    <a href="#hero" class="nav-logo">
        <div class="nav-logo-icon">📷</div>
        <span class="nav-logo-text">KUET <span>Photo</span></span>
    </a>

    <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="navLinks">
        <li><a href="#about">About</a></li>
        <li><a href="#gallery">Gallery</a></li>
        <li><a href="#events">Events</a></li>
        <li><a href="#team">Team</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>

    <!-- 3-dot info menu -->
    <div class="info-dots" id="infoDots">
        <button class="dots-btn" onclick="toggleInfoMenu()">⋮</button>
        <div class="dots-menu" id="dotsMenu">
            <div class="dots-section-label">Navigate</div>
            <a href="#gallery"  class="dots-item" onclick="closeInfoMenu()">🖼️ Gallery</a>
            <a href="#events"   class="dots-item" onclick="closeInfoMenu()">📅 Events</a>
            <a href="#team"     class="dots-item" onclick="closeInfoMenu()">👥 Team</a>
            <a href="#contact"  class="dots-item" onclick="closeInfoMenu()">✉️ Contact</a>
            <div class="dots-divider"></div>
            <div class="dots-section-label">Info</div>
            <a href="#" class="dots-item" onclick="openModal('termsModal');return false;">📄 Terms & Conditions</a>
            <a href="#" class="dots-item" onclick="openModal('privacyModal');return false;">🔒 Privacy Policy</a>
            <div class="dots-divider"></div>
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <a href="admin.php" class="dots-item dots-admin">⚙️ Admin Panel</a>
            <?php else: ?>
                <a href="adminlogin.php" class="dots-item dots-admin">🔐 Admin Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- =============================================
     HERO
============================================= -->
<section id="hero">
    <div class="hero-badge">KUET Photography Society — Est. 2010</div>

    <h1 class="hero-title">
        We Capture<br>
        <span class="line-accent">Every Moment</span>
    </h1>

    <p class="hero-sub">
        A creative community of photographers at Khulna University of
        Engineering &amp; Technology, united by the art of visual storytelling.
    </p>

    <div class="hero-cta">
        <a href="#contact" class="btn-primary">Join the Club</a>
        <a href="#gallery" class="btn-outline">View Gallery</a>
    </div>

    <div class="hero-stats">
        <div class="stat-item">
            <div class="stat-num">200+</div>
            <div class="stat-label">Active Members</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">50+</div>
            <div class="stat-label">Events Held</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">1200+</div>
            <div class="stat-label">Photos Captured</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">15</div>
            <div class="stat-label">Years Strong</div>
        </div>
    </div>
</section>

<!-- =============================================
     ABOUT
============================================= -->
<section id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <div class="section-header" style="text-align:left; margin-bottom:32px;">
                    <span class="section-tag">About Us</span>
                    <h2 class="section-title">Passion for <em>Photography</em></h2>
                    <div class="section-divider" style="margin-left:0;"></div>
                </div>
                <p>
                    KUET Photography Society is the official photography club of Khulna University
                    of Engineering And Technology. We welcome students of all skill levels who
                    share a passion for capturing the world through a lens.
                </p>
                <p>
                    From street photography to astrophotography, portrait sessions to architectural
                    studies — our members explore every genre and grow together through workshops,
                    contests, and collaborative projects.
                </p>

                <div class="about-features">
                    <div class="feature-card">
                        <div class="feature-icon">🎓</div>
                        <div class="feature-title">Workshops</div>
                        <div class="feature-desc">Regular sessions led by senior members and professionals</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🏆</div>
                        <div class="feature-title">Contests</div>
                        <div class="feature-desc">Monthly photo contests with exciting prizes</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🚶</div>
                        <div class="feature-title">Photo Walks</div>
                        <div class="feature-desc">Explore Khulna and beyond with your camera</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🖼️</div>
                        <div class="feature-title">Exhibitions</div>
                        <div class="feature-desc">Annual exhibitions to showcase member work</div>
                    </div>
                </div>
            </div>

            <div class="about-visual">
                <div class="about-img-frame">
                    <!-- Replace src with actual image -->
                    <span style="position:relative;z-index:1;">📷</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =============================================
     GALLERY — PHP filter via GET
============================================= -->
<section id="gallery">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Portfolio</span>
            <h2 class="section-title">Our <em>Gallery</em></h2>
            <div class="section-divider"></div>
        </div>



<div class="gallery-filters">
    <button class="filter-btn active" onclick="filterGallery('all', this)">All</button>
    <button class="filter-btn" onclick="filterGallery('nature', this)">Nature</button>
    <button class="filter-btn" onclick="filterGallery('portrait', this)">Portrait</button>
    <button class="filter-btn" onclick="filterGallery('architecture', this)">Architecture</button>
    <button class="filter-btn" onclick="filterGallery('event', this)">Events</button>
</div>

<div class="gallery-grid" id="galleryGrid">
<?php if (empty($gallery_items)): ?>
    <div class="gallery-empty">
        <p>No photos yet. Add some from the admin panel.</p>
    </div>
<?php else: ?>
    <?php foreach ($gallery_items as $item): ?>
        <div class="gallery-card" data-category="<?= htmlspecialchars($item['category']) ?>">
            <?php if ($item['filename'] !== 'placeholder' && file_exists('images/' . $item['filename'])): ?>
                <img src="images/<?= htmlspecialchars($item['filename']) ?>"
                     alt="<?= htmlspecialchars($item['title']) ?>"
                     style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <div class="gallery-placeholder"><span>🖼️</span></div>
            <?php endif; ?>
            <div class="gallery-overlay">
                <h3><?= htmlspecialchars($item['title']) ?></h3>
                <p>by <?= htmlspecialchars($item['photographer']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div><!-- end gallery-grid -->
</section>

<!-- =============================================
     EVENTS
============================================= -->
<section id="events">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Upcoming</span>
            <h2 class="section-title">Club <em>Events</em></h2>
            <div class="section-divider"></div>
        </div>

        <div class="events-list">
            <?php foreach ($events as $ev):
                // Parse date parts with PHP
                $parts = explode(' ', $ev['date']);
                $day   = $parts[0];
                $month = $parts[1] . ' ' . $parts[2];
            ?>
            <div class="event-card">
                <div class="event-date">
                    <div class="day"><?= $day ?></div>
                    <div class="month"><?= $month ?></div>
                </div>
                <div class="event-info">
                    <h3><?= htmlspecialchars($ev['title']) ?></h3>
                    <div class="event-meta">
                        <span>📍 <?= htmlspecialchars($ev['location']) ?></span>
                        <span>👥 <?= $ev['spots'] ?> spots</span>
                    </div>
                </div>
                <div class="event-spots"><?= $ev['spots'] ?> Seats Left</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =============================================
     TEAM
============================================= -->
<section id="team">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">People</span>
            <h2 class="section-title">Meet the <em>Team</em></h2>
            <div class="section-divider"></div>
        </div>

        <div class="team-grid">
          <?php foreach ($team as $member): ?>
<div class="team-card">
    <?php
    $tp = 'images/team/' . $member['photo'];
    if (isset($member['photo']) && $member['photo'] !== 'placeholder' && file_exists($tp)): ?>
        <img src="<?= $tp ?>"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;
                    margin:0 auto 16px;display:block;
                    border:2px solid var(--border-bright);">
    <?php else: ?>
        <div class="team-icon"><?= $member['icon'] ?></div>
    <?php endif; ?>
    <div class="team-name"><?= htmlspecialchars($member['name']) ?></div>
    <div class="team-role"><?= htmlspecialchars($member['role']) ?></div>
</div>
<?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =============================================
     CONTACT — PHP form (POST + sticky values + validation)
============================================= -->
<section id="contact">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Get In Touch</span>
            <h2 class="section-title">Join or <em>Contact</em> Us</h2>
            <div class="section-divider"></div>
        </div>

        <div class="contact-grid">
            <!-- Info Column -->
            <div class="contact-info">
                <h3>We'd love to hear from you</h3>
                <p>Whether you want to join the club, collaborate on a project, or simply have a question — reach out.</p>

                <div class="contact-detail">
                    <div class="contact-detail-icon">📧</div>
                    <div>
                        <div class="contact-detail-label">Email</div>
                        <div class="contact-detail-value">photo@kuet.ac.bd</div>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">📍</div>
                    <div>
                        <div class="contact-detail-label">Location</div>
                        <div class="contact-detail-value">KUET Campus, Khulna, Bangladesh</div>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">🕐</div>
                    <div>
                        <div class="contact-detail-label">Club Hours</div>
                        <div class="contact-detail-value">Sat – Thu, 4:00 PM – 7:00 PM</div>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">📱</div>
                    <div>
                        <div class="contact-detail-label">Facebook</div>
                        <div class="contact-detail-value">fb.com/KUETPhotoSociety</div>
                    </div>
                </div>
            </div>

            <!-- Form Column -->
            <div class="contact-form">
                <!-- PHP: success message -->
                <?php if ($form_success): ?>
                    <div class="form-success">
                        ✓ Your message was sent successfully! We'll get back to you soon.
                    </div>
                <?php endif; ?>

                <!-- PHP: error messages -->
                <?php if (!empty($form_errors)): ?>
                    <div class="form-errors">
                        <ul>
                        <?php foreach ($form_errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Sticky form — PHP repopulates values on error -->
                <form method="POST" action="#contact">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name"
                                   value="<?= htmlspecialchars($sticky['name']) ?>"
                                   placeholder="Your name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($sticky['email']) ?>"
                                   placeholder="your@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject">
                            <option value="" <?= $sticky['subject'] === '' ? 'selected' : '' ?>>Select a topic</option>
                            <option value="join"      <?= $sticky['subject'] === 'join'      ? 'selected' : '' ?>>Join the Club</option>
                            <option value="workshop"  <?= $sticky['subject'] === 'workshop'  ? 'selected' : '' ?>>Workshop Inquiry</option>
                            <option value="collab"    <?= $sticky['subject'] === 'collab'    ? 'selected' : '' ?>>Collaboration</option>
                            <option value="general"   <?= $sticky['subject'] === 'general'   ? 'selected' : '' ?>>General Question</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message"
                                  placeholder="Tell us about yourself or your query..."><?= htmlspecialchars($sticky['message']) ?></textarea>
                    </div>

                    <button type="submit" name="contact_submit" class="btn-primary" style="width:100%; justify-content:center;">
                        Send Message →
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- =============================================
     FOOTER
============================================= -->
<footer>
    <div class="footer-logo">KUET <span>Photography</span> Society</div>
    <p style="color:var(--text-muted); font-size:0.88rem; max-width:400px; margin:8px auto;">
        Capturing moments that last forever at KUET Campus, Khulna.
    </p>
    <div class="footer-links">
        <a href="#hero">Home</a>
        <a href="#about">About</a>
        <a href="#gallery">Gallery</a>
        <a href="#events">Events</a>
        <a href="#team">Team</a>
        <a href="#contact">Contact</a>
    </div>
    <div class="footer-copy">
        © <?= date('Y') ?> KUET Photography Society. Built with PHP &amp; HTML.
    </div>
</footer>


<!-- Floating 3-dot button -->
<div class="floating-dots">
    <button class="floating-btn" onclick="toggleInfoMenu()">⋮</button>
</div>

<!-- Terms Modal -->
<div id="termsModal" style="display:none;position:fixed;inset:0;
     background:rgba(0,0,0,0.7);z-index:1000;align-items:center;
     justify-content:center;padding:24px;">
    <div style="background:var(--bg-card);border:1px solid var(--border-bright);
                border-radius:16px;padding:36px;max-width:560px;width:100%;
                max-height:80vh;overflow-y:auto;">
        <h2 style="font-family:var(--font-display);font-size:1.8rem;margin-bottom:16px;">
            Terms and Conditions
        </h2>
        <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.8;">
            By using this website, you agree that all photos displayed are the property of
            KUET Photography Society and its members. Reproduction of any content without
            written permission is prohibited. Membership is open to all KUET students.
            The club reserves the right to use submitted photos for promotional purposes.
        </p>
        <button onclick="closeModal('termsModal')"
                style="margin-top:24px;padding:10px 24px;background:var(--purple-2);
                       color:#fff;border:none;border-radius:7px;cursor:pointer;font-size:0.9rem;">
            Close
        </button>
    </div>
</div>

<!-- Privacy Modal -->
<div id="privacyModal" style="display:none;position:fixed;inset:0;
     background:rgba(0,0,0,0.7);z-index:1000;align-items:center;
     justify-content:center;padding:24px;">
    <div style="background:var(--bg-card);border:1px solid var(--border-bright);
                border-radius:16px;padding:36px;max-width:560px;width:100%;
                max-height:80vh;overflow-y:auto;">
        <h2 style="font-family:var(--font-display);font-size:1.8rem;margin-bottom:16px;">
            Privacy Policy
        </h2>
        <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.8;">
            KUET Photography Society collects only the information you provide through
            the contact form (name, email, message). This information is used solely
            to respond to your inquiry and is never shared with third parties.
            We do not use cookies for tracking purposes.
        </p>
        <button onclick="closeModal('privacyModal')"
                style="margin-top:24px;padding:10px 24px;background:var(--purple-2);
                       color:#fff;border:none;border-radius:7px;cursor:pointer;font-size:0.9rem;">
            Close
        </button>
    </div>
</div>

<!-- =================
     JAVASCRIPT — Hamburger + Client-side gallery filter
============================================= -->
<script>
    // Hamburger menu
    const hamburger = document.getElementById('hamburger');
    const navLinks  = document.getElementById('navLinks');
    hamburger.addEventListener('click', () => navLinks.classList.toggle('open'));

    // Client-side gallery filter (no reload — works alongside PHP filter)
// Gallery filter — pure JS, no page reload, no scroll jump
function filterGallery(category, clickedBtn) {
    // Update active button style
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    clickedBtn.classList.add('active');

    // Show/hide cards instantly — no scroll, no reload
    document.querySelectorAll('.gallery-card').forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

    // Smooth navbar background on scroll
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('navbar');
        nav.style.boxShadow = window.scrollY > 20 ? '0 4px 30px rgba(0,0,0,0.5)' : 'none';
    });

    // Scroll reveal — simple fade-in as elements enter view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.feature-card, .gallery-card, .event-card, .team-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

    // 3-dot info menu toggle
function toggleInfoMenu() {
    document.getElementById('dotsMenu').classList.toggle('open');
}
function closeInfoMenu() {
    document.getElementById('dotsMenu').classList.remove('open');
}
// Close when clicking outside
document.addEventListener('click', function(e) {
    const dots = document.getElementById('infoDots');
    if (dots && !dots.contains(e.target)) {
        const floatingBtn = document.querySelector('.floating-dots');
        if (floatingBtn && !floatingBtn.contains(e.target)) {
            document.getElementById('dotsMenu').classList.remove('open');
        }
    }
});

// Modal open/close
function openModal(id) {
    const m = document.getElementById(id);
    m.style.display = 'flex';
    closeInfoMenu();
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
// Close modal on backdrop click
document.querySelectorAll('#termsModal, #privacyModal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>

</body>
</html>