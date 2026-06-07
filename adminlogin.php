<?php
session_start();

// Already logged in — go straight to admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

require 'db.php';

$error  = '';
$sticky_username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $sticky_username = htmlspecialchars($username);

    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        // Look up admin by username
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // password_verify() checks plain password against stored hash
        if ($admin && password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username']  = $admin['username'];
            $_SESSION['admin_id']        = $admin['id'];

            header("Location: admin.php");
            exit;
        } else {
            $error = "Invalid username or password.";
            // Small delay to slow brute-force attempts
            sleep(1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — KUET Photo Society</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:     #07050f;
            --card:   #130f24;
            --deep:   #0d0a1a;
            --purple: #7c3aed;
            --pl:     #a78bfa;
            --accent: #e879f9;
            --border: rgba(138,99,255,0.2);
            --text:   #f1eeff;
            --muted:  #9d8ec4;
            --red:    #fca5a5;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Background glow */
        body::before {
            content: '';
            position: fixed;
            top: 30%; left: 50%;
            transform: translate(-50%, -50%);
            width: 600px; height: 600px;
            background: radial-gradient(ellipse, rgba(124,58,237,0.15), transparent 70%);
            pointer-events: none;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px 40px;
            position: relative;
            z-index: 1;
        }

        /* Top accent line */
        .login-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--purple), var(--accent));
            border-radius: 20px 20px 0 0;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo-icon {
            font-size: 2.2rem;
            width: 64px; height: 64px;
            border-radius: 50%;
            background: rgba(124,58,237,0.15);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .login-logo h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 700;
        }
        .login-logo h1 span { color: var(--pl); }
        .login-logo p {
            font-size: 0.82rem;
            color: var(--muted);
            margin-top: 4px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .error-box {
            padding: 12px 16px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 8px;
            color: var(--red);
            font-size: 0.87rem;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: var(--muted);
            margin-bottom: 7px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            background: var(--deep);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        input:focus {
            border-color: var(--purple);
            box-shadow: 0 0 0 3px rgba(124,58,237,0.15);
        }

        /* Show/hide password toggle */
        .input-wrap { position: relative; }
        .toggle-pass {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            line-height: 1;
        }
        .toggle-pass:hover { color: var(--pl); }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--purple), #5b21b6);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(124,58,237,0.35);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(124,58,237,0.5);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 22px;
            font-size: 0.82rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--pl); }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-logo">
        <div class="login-logo-icon">📷</div>
        <h1>KUET <span>Admin</span></h1>
        <p>Photography Society</p>
    </div>

    <?php if ($error): ?>
        <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text"
                   id="username"
                   name="username"
                   value="<?= $sticky_username ?>"
                   placeholder="Enter username"
                   autocomplete="username"
                   required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Enter password"
                       autocomplete="current-password"
                       required>
                <button type="button" class="toggle-pass" onclick="togglePassword()">👁</button>
            </div>
        </div>

        <button type="submit" class="btn-login">Sign In →</button>
    </form>

    <a href="index.php" class="back-link">← Back to website</a>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
</html>