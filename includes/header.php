<?php
// includes/header.php
require_once __DIR__ . '/../includes/auth.php';
$user = getCurrentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Support IT' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f0f13;
            --surface:   #18181f;
            --border:    #2a2a35;
            --accent:    #ff6b35;
            --accent2:   #f7c59f;
            --text:      #e8e8f0;
            --muted:     #7a7a8c;
            --success:   #22c55e;
            --warning:   #f59e0b;
            --danger:    #ef4444;
            --info:      #3b82f6;
            --radius:    10px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
        }

        /* NAV */
        nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 62px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--accent);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .nav-brand span { color: var(--text); }

        .nav-links { display: flex; align-items: center; gap: 0.25rem; }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            padding: 0.4rem 0.9rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.15s;
        }

        .nav-links a:hover { background: var(--border); color: var(--text); }
        .nav-links a.active { background: var(--accent); color: #fff; }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .nav-user .badge-admin {
            background: var(--accent);
            color: #fff;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* MAIN */
        main { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem; }

        /* FLASH */
        .flash {
            padding: 0.85rem 1.2rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 4px solid;
        }
        .flash.success { background: #16311f; border-color: var(--success); color: #86efac; }
        .flash.error   { background: #2e1515; border-color: var(--danger);  color: #fca5a5; }
        .flash.info    { background: #152033; border-color: var(--info);    color: #93c5fd; }

        /* CARDS */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--border);
        }

        /* FORMS */
        .form-group { margin-bottom: 1.1rem; }
        label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; color: var(--muted); font-weight: 500; }

        input[type="text"], input[type="email"], input[type="password"],
        textarea, select {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 7px;
            color: var(--text);
            padding: 0.65rem 0.9rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.92rem;
            transition: border-color 0.15s;
            outline: none;
        }

        input:focus, textarea:focus, select:focus { border-color: var(--accent); }
        textarea { resize: vertical; min-height: 110px; }

        /* BUTTONS */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.2rem;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 0.88rem;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary  { background: var(--accent);  color: #fff; }
        .btn-primary:hover  { background: #e55a25; }
        .btn-secondary { background: var(--border); color: var(--text); }
        .btn-secondary:hover { background: #3a3a4a; }
        .btn-danger   { background: var(--danger);  color: #fff; }
        .btn-danger:hover   { background: #dc2626; }
        .btn-sm { padding: 0.35rem 0.8rem; font-size: 0.8rem; }

        /* BADGES STATUT */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-ouvert   { background: #1e3a5f; color: #93c5fd; }
        .badge-en_cours { background: #3b2a00; color: #fcd34d; }
        .badge-fermé    { background: #1a2e1a; color: #86efac; }

        .badge-basse   { background: #1e2832; color: #94a3b8; }
        .badge-normale { background: #1e3a5f; color: #93c5fd; }
        .badge-haute   { background: #3b2a00; color: #fcd34d; }
        .badge-urgente { background: #2e1515; color: #fca5a5; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 0.6rem 1rem; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--muted); border-bottom: 1px solid var(--border); }
        td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        /* PAGE TITLE */
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.7rem; letter-spacing: -0.5px; }
        .page-header p { color: var(--muted); margin-top: 0.3rem; font-size: 0.9rem; }

        /* GRID */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }

        @media (max-width: 640px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            nav { padding: 0 1rem; }
            main { padding: 1.25rem 1rem; }
        }
    </style>
</head>
<body>

<nav>
    <a href="/projet-tickets/index.php" class="nav-brand">⚡ Support<span>IT</span></a>

    <?php if (isLoggedIn()): ?>
    <div class="nav-links">
        <a href="/projet-tickets/index.php">Mes tickets</a>
        <a href="/projet-tickets/create.php">+ Nouveau</a>
        <?php if (isAdmin()): ?>
        <a href="/projet-tickets/admin/index.php">Admin</a>
        <?php endif; ?>
    </div>
    <div class="nav-user">
        <?php if (isAdmin()): ?><span class="badge-admin">Admin</span><?php endif; ?>
        <span><?= htmlspecialchars($user['nom']) ?></span>
        <a href="/projet-tickets/logout.php" class="btn btn-secondary btn-sm">Déconnexion</a>
    </div>
    <?php else: ?>
    <div class="nav-links">
        <a href="/projet-tickets/login.php">Connexion</a>
        <a href="/projet-tickets/register.php" class="btn btn-primary btn-sm">S'inscrire</a>
    </div>
    <?php endif; ?>
</nav>

<main>
<?php if ($flash): ?>
    <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
