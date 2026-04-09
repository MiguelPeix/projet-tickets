<?php
// admin/index.php — Tableau de bord administrateur
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
requireLogin();
requireAdmin();

// Stats globales
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(statut = 'ouvert') AS ouverts,
        SUM(statut = 'en_cours') AS en_cours,
        SUM(statut = 'fermé') AS fermes,
        SUM(priorite = 'urgente') AS urgents
    FROM tickets
")->fetch();

$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

// 10 derniers tickets
$derniers = $pdo->query("
    SELECT t.*, u.nom AS auteur
    FROM tickets t JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC LIMIT 10
")->fetchAll();

$pageTitle = 'Admin — SupportIT';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Tableau de bord admin</h1>
    <p>Vue d'ensemble de la plateforme de support</p>
</div>

<!-- KPIs -->
<div style="display:grid; grid-template-columns: repeat(5, 1fr); gap:1rem; margin-bottom:2rem;">
    <?php
    $kpis = [
        ['label' => 'Total tickets', 'value' => $stats['total'],    'color' => 'var(--text)'],
        ['label' => 'Ouverts',       'value' => $stats['ouverts'],   'color' => 'var(--info)'],
        ['label' => 'En cours',      'value' => $stats['en_cours'],  'color' => 'var(--warning)'],
        ['label' => 'Fermés',        'value' => $stats['fermes'],    'color' => 'var(--success)'],
        ['label' => 'Urgents',       'value' => $stats['urgents'],   'color' => 'var(--danger)'],
    ];
    foreach ($kpis as $k): ?>
    <div class="card" style="text-align:center; border-top:3px solid <?= $k['color'] ?>;">
        <div style="font-family:'Syne',sans-serif; font-weight:800; font-size:2rem; color:<?= $k['color'] ?>;"><?= $k['value'] ?></div>
        <div style="color:var(--muted); font-size:0.8rem; margin-top:0.2rem;"><?= $k['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <h2 style="font-family:'Syne',sans-serif; font-weight:700;">Derniers tickets</h2>
    <a href="/projet-tickets/index.php" class="btn btn-secondary btn-sm">Voir tous</a>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($derniers as $t): ?>
            <tr>
                <td style="color:var(--muted); font-size:0.8rem;">#<?= $t['id'] ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($t['titre']) ?></td>
                <td style="color:var(--muted); font-size:0.85rem;"><?= htmlspecialchars($t['auteur']) ?></td>
                <td style="color:var(--muted); font-size:0.85rem;"><?= htmlspecialchars($t['categorie']) ?></td>
                <td><span class="badge badge-<?= $t['priorite'] ?>"><?= $t['priorite'] ?></span></td>
                <td><span class="badge badge-<?= $t['statut'] ?>"><?= str_replace('_', ' ', $t['statut']) ?></span></td>
                <td style="color:var(--muted); font-size:0.82rem;"><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                <td><a href="/projet-tickets/ticket.php?id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">Voir</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
