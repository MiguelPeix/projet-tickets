<?php
// login.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (isLoggedIn()) {
    header('Location: /projet-tickets/index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mdp)) {
        $errors[] = 'Tous les champs sont obligatoires.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['role']    = $user['role'];

            setFlash('success', 'Bienvenue, ' . $user['nom'] . ' !');
            header('Location: /projet-tickets/index.php');
            exit;
        } else {
            $errors[] = 'Email ou mot de passe incorrect.';
        }
    }
}

$pageTitle = 'Connexion — SupportIT';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0f0f13; --surface: #18181f; --border: #2a2a35;
            --accent: #ff6b35; --text: #e8e8f0; --muted: #7a7a8c;
            --danger: #ef4444; --radius: 10px;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        body::before {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255,107,53,0.08) 0%, transparent 70%);
            top: -100px; right: -100px;
            pointer-events: none;
        }
        .auth-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }
        .auth-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--accent);
            text-align: center;
            margin-bottom: 0.35rem;
        }
        .auth-logo span { color: var(--text); }
        .auth-sub {
            text-align: center;
            color: var(--muted);
            font-size: 0.85rem;
            margin-bottom: 2rem;
        }
        h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
        }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.4rem; font-size: 0.83rem; color: var(--muted); font-weight: 500; }
        input {
            width: 100%; background: var(--bg); border: 1px solid var(--border);
            border-radius: 7px; color: var(--text); padding: 0.7rem 0.9rem;
            font-family: 'DM Sans', sans-serif; font-size: 0.92rem;
            transition: border-color 0.15s; outline: none;
        }
        input:focus { border-color: var(--accent); }
        .btn {
            width: 100%; background: var(--accent); color: #fff;
            border: none; border-radius: 7px; padding: 0.75rem;
            font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 0.95rem; cursor: pointer; transition: background 0.15s;
            margin-top: 0.5rem;
        }
        .btn:hover { background: #e55a25; }
        .error-box {
            background: #2e1515; border-left: 3px solid var(--danger);
            color: #fca5a5; padding: 0.75rem 1rem;
            border-radius: 7px; margin-bottom: 1.25rem; font-size: 0.88rem;
        }
        .auth-footer {
            text-align: center; margin-top: 1.5rem;
            font-size: 0.85rem; color: var(--muted);
        }
        .auth-footer a { color: var(--accent); text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="auth-box">
    <div class="auth-logo">⚡ Support<span>IT</span></div>
    <p class="auth-sub">Plateforme de support informatique</p>

    <h2>Connexion</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box"><?= htmlspecialchars($errors[0]) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="vous@exemple.fr" required>
        </div>
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>

    <div class="auth-footer">
        Pas encore de compte ? <a href="/projet-tickets/register.php">Créer un compte</a>
    </div>
</div>
</body>
</html>
