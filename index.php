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
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-top: 70px;
        }

        #hero h1 {
            font-size: 3rem;
            font-family: 'Cormorant Garamond', serif;
        }

        #hero p {
            color: var(--text-secondary);
            margin-top: 10px;
        }

        /* FOOTER */
        footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
        }
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
    <h1>We Capture Every Moment</h1>
    <p>KUET Photography Society</p>
</section>

<!-- FOOTER -->
<footer>
    <p>KUET Photography Society</p>
</footer>

</body>
</html>