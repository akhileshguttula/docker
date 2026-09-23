<?php
// Start the PHP session so we can remember the logged-in user.
session_start();

// Read database connection information from environment variables.
// These values are supplied using docker run or Docker Compose.
$dbHost = getenv('DB_HOST') ?: 'nks-database';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'nkslab';
$dbUser = getenv('DB_USER') ?: 'nksapp';
$dbPassword = getenv('DB_PASSWORD') ?: 'nksdb@123';

$error = '';
$dbConnected = false;

// Create a MySQL connection.
$mysqli = @new mysqli($dbHost, $dbUser, $dbPassword, $dbName, (int)$dbPort);

if (!$mysqli->connect_errno) {
    $dbConnected = true;
}

// Logout request.
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /');
    exit;
}

// Process the login form only when database connectivity is available.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$dbConnected) {
        $error = 'Database is not connected. Check Docker networking.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        // Use a prepared statement to avoid SQL injection.
        $stmt = $mysqli->prepare('SELECT username, password_hash FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Passwords in this lab are stored as SHA-256 hashes.
        if ($user && hash('sha256', $password) === $user['password_hash']) {
            $_SESSION['username'] = $user['username'];
            header('Location: /');
            exit;
        }

        $error = 'Invalid username or password.';
    }
}

$loggedInUser = $_SESSION['username'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextKodeSchool Deployment Lab</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #ffffff;
            background:
                radial-gradient(circle at top right, rgba(255, 204, 0, 0.16), transparent 32%),
                linear-gradient(135deg, #050505 0%, #111111 55%, #080808 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .shell {
            width: 100%;
            max-width: 1080px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            background: #0d0d0d;
            border: 1px solid #2b2b2b;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.55);
        }

        .brand-panel {
            padding: 64px 56px;
            background:
                linear-gradient(160deg, rgba(255, 204, 0, 0.16), transparent 48%),
                #0a0a0a;
            border-right: 1px solid #242424;
        }

        .brand-badge {
            display: inline-block;
            padding: 8px 14px;
            color: #111111;
            background: #ffcc00;
            border-radius: 999px;
            font-weight: 800;
            letter-spacing: .08em;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        h1 {
            margin: 0 0 18px;
            font-size: clamp(42px, 6vw, 72px);
            line-height: .98;
            letter-spacing: -2px;
        }

        h1 span {
            color: #ffcc00;
        }

        .lead {
            margin: 0;
            color: #cfcfcf;
            font-size: 18px;
            line-height: 1.7;
            max-width: 530px;
        }

        .architecture {
            margin-top: 38px;
            padding: 20px;
            border: 1px solid #2c2c2c;
            border-radius: 16px;
            background: #101010;
            color: #dcdcdc;
            font-family: monospace;
            line-height: 1.9;
        }

        .architecture strong {
            color: #ffcc00;
        }

        .form-panel {
            padding: 64px 48px;
            display: flex;
            align-items: center;
        }

        .form-box {
            width: 100%;
        }

        .form-box h2 {
            font-size: 30px;
            margin: 0 0 8px;
        }

        .muted {
            color: #969696;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin: 0 0 8px;
            color: #dedede;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 15px 16px;
            margin-bottom: 18px;
            border-radius: 10px;
            border: 1px solid #383838;
            background: #151515;
            color: #ffffff;
            font-size: 16px;
            outline: none;
        }

        input:focus {
            border-color: #ffcc00;
            box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.12);
        }

        button, .button-link {
            width: 100%;
            display: inline-block;
            border: none;
            border-radius: 10px;
            padding: 15px 18px;
            background: #ffcc00;
            color: #111111;
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        button:hover, .button-link:hover {
            background: #ffd633;
        }

        .status {
            margin-top: 22px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            background: #151515;
            border: 1px solid #303030;
        }

        .status.ok {
            color: #9eff9e;
        }

        .status.fail, .error {
            color: #ff9a9a;
        }

        .error {
            background: rgba(255, 75, 75, 0.08);
            border: 1px solid rgba(255, 75, 75, 0.3);
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
        }

        .success-card {
            padding: 28px;
            border: 1px solid rgba(255, 204, 0, 0.35);
            border-radius: 18px;
            background: linear-gradient(145deg, rgba(255, 204, 0, 0.12), rgba(255,255,255,0.02));
        }

        .success-card h2 {
            color: #ffcc00;
            font-size: 34px;
            margin-bottom: 14px;
        }

        .username {
            font-size: 26px;
            font-weight: 800;
            margin: 18px 0 26px;
        }

        @media (max-width: 820px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                border-right: none;
                border-bottom: 1px solid #242424;
                padding: 42px 30px;
            }

            .form-panel {
                padding: 42px 30px;
            }
        }
    </style>
</head>
<body>

<div class="shell">
    <section class="brand-panel">
        <div class="brand-badge">NextKodeSchool</div>

        <h1>Docker <span>Deployment Lab</span></h1>

        <p class="lead">
            Practice Docker Run, Docker Networking and Docker Compose with a real login application connected to MySQL.
        </p>

        <div class="architecture">
            <strong>Browser</strong> → Frontend Container<br>
            Frontend → <strong>Docker Network</strong><br>
            Docker Network → MySQL Database
        </div>
    </section>

    <section class="form-panel">
        <div class="form-box">

            <?php if ($loggedInUser): ?>
                <div class="success-card">
                    <h2>Awesome! 🎉</h2>
                    <p>You have successfully deployed and connected the application.</p>

                    <div class="username">
                        Logged in with <?= htmlspecialchars($loggedInUser) ?> user
                    </div>

                    <p>Frontend and Database are connected successfully.</p>

                    <div class="status ok">● Database Connected</div>
                    <br>
                    <a class="button-link" href="/?logout=1">Logout</a>
                </div>
            <?php else: ?>
                <h2>Login to Deployment Lab</h2>
                <p class="muted">Authenticate using the user stored in MySQL.</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" placeholder="Enter username" required>

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Enter password" required>

                    <button type="submit">Login</button>
                </form>

                <?php if ($dbConnected): ?>
                    <div class="status ok">● Database Connected</div>
                <?php else: ?>
                    <div class="status fail">● Database Not Connected</div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </section>
</div>

</body>
</html>
