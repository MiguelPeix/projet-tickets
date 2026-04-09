<?php
// index.php — Liste des tickets de l'utilisateur connecté
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = getCurrentUser();

// Récupère les tickets de l'utilisateur (admin voit tout)
if (isAdmin()) {
    $stmt = $pdo->query('SELECT t.*, u.nom AS auteur FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC');
} else {
    $stmt = $pdo->prepare('SELECT t.*, u.nom AS auteur FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.user_id = ? ORDER BY t.created_at DESC');
    $stmt->execute([$user['id']]);
}
$tickets = $stmt->fetchAll();

// Compter par statut
$stats = ['ouvert' => 0, 'en_cours' => 0, 'fermé' => 0];
foreach ($tickets as $t) { $stats[$t['statut']]++; }

$pageTitle = 'Mes tickets — SupportIT';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1><?= isAdmin() ? 'Tous les tickets' : 'Mes tickets' ?></h1>
        <p><?= isAdmin() ? 'Vue administrateur — tous les tickets' : 'Retrouvez l\'ensemble de vos demandes de support' ?></p>
    </div>
    <a href="/projet-tickets/create.php" class="btn btn-primary">+ Nouveau ticket</a>
</div>

<!-- STATS -->
<div class="grid-3" style="margin-bottom: 1.75rem;">
    <div class="card" style="border-left: 3px solid var(--info);">
        <div style="font-size:1.8rem; font-family:'Syne',sans-serif; font-weight:800;"><?= $stats['ouvert'] ?></div>
        <div style="color:var(--muted); font-size:0.85rem; margin-top:0.2rem;">Ouverts</div>
    </div>
    <div class="card" style="border-left: 3px solid var(--warning);">
        <div style="font-size:1.8rem; font-family:'Syne',sans-serif; font-weight:800;"><?= $stats['en_cours'] ?></div>
        <div style="color:var(--muted); font-size:0.85rem; margin-top:0.2rem;">En cours</div>
    </div>
    <div class="card" style="border-left: 3px solid var(--success);">
        <div style="font-size:1.8rem; font-family:'Syne',sans-serif; font-weight:800;"><?= $stats['fermé'] ?></div>
        <div style="color:var(--muted); font-size:0.85rem; margin-top:0.2rem;">Fermés</div>
    </div>
</div>

<!-- TABLEAU -->
<div class="card" style="padding:0; overflow:hidden;">
    <?php if (empty($tickets)): ?>
        <div style="text-align:center; padding:3rem; color:var(--muted);">
            <div style="font-size:2.5rem; margin-bottom:1rem;">📭</div>
            <p>Aucun ticket pour le moment.</p>
            <a href="/projet-tickets/create.php" class="btn btn-primary" style="margin-top:1rem;">Créer mon premier ticket</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <?php if (isAdmin()): ?><th>Auteur</th><?php endif; ?>
                    <th>Catégorie</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                <tr>
                    <td style="color:var(--muted); font-size:0.8rem;">#<?= $t['id'] ?></td>
                    <td>
                        <a href="/projet-tickets/ticket.php?id=<?= $t['id'] ?>" style="color:var(--text); text-decoration:none; font-weight:500;">
                            <?= htmlspecialchars($t['titre']) ?>
                        </a>
                    </td>
                    <?php if (isAdmin()): ?>
                    <td style="color:var(--muted); font-size:0.85rem;"><?= htmlspecialchars($t['auteur']) ?></td>
                    <?php endif; ?>
                    <td style="color:var(--muted); font-size:0.85rem;"><?= htmlspecialchars($t['categorie']) ?></td>
                    <td><span class="badge badge-<?= $t['priorite'] ?>"><?= $t['priorite'] ?></span></td>
                    <td><span class="badge badge-<?= $t['statut'] ?>"><?= str_replace('_', ' ', $t['statut']) ?></span></td>
                    <td style="color:var(--muted); font-size:0.82rem;"><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                    <td>
                        <a href="/projet-tickets/ticket.php?id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
