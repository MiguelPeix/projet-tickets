<?php
// ticket.php — Détail d'un ticket + commentaires
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$user = getCurrentUser();
$id   = (int)($_GET['id'] ?? 0);

// Récupère le ticket
$stmt = $pdo->prepare('SELECT t.*, u.nom AS auteur FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    setFlash('error', 'Ticket introuvable.');
    header('Location: /projet-tickets/index.php');
    exit;
}

// Vérifier l'accès (propriétaire ou admin)
if ($ticket['user_id'] !== $user['id'] && !isAdmin()) {
    setFlash('error', 'Accès non autorisé.');
    header('Location: /projet-tickets/index.php');
    exit;
}

// Ajout d'un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare('INSERT INTO commentaires (ticket_id, user_id, message) VALUES (?, ?, ?)');
        $stmt->execute([$id, $user['id'], $message]);
        setFlash('success', 'Commentaire ajouté.');
        header('Location: /projet-tickets/ticket.php?id=' . $id . '#comments');
        exit;
    }
}

// Changement de statut (admin ou propriétaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut'])) {
    if (isAdmin()) {
        $statuts = ['ouvert', 'en_cours', 'fermé'];
        $statut  = $_POST['statut'];
        if (in_array($statut, $statuts)) {
            $stmt = $pdo->prepare('UPDATE tickets SET statut = ? WHERE id = ?');
            $stmt->execute([$statut, $id]);
            setFlash('success', 'Statut mis à jour.');
            header('Location: /projet-tickets/ticket.php?id=' . $id);
            exit;
        }
    }
}

// Récupère les commentaires
$stmt = $pdo->prepare('SELECT c.*, u.nom, u.role FROM commentaires c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = ? ORDER BY c.created_at ASC');
$stmt->execute([$id]);
$commentaires = $stmt->fetchAll();

$pageTitle = 'Ticket #' . $id . ' — SupportIT';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom:1rem;">
    <a href="/projet-tickets/index.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← Retour à la liste</a>
</div>

<div style="display:grid; grid-template-columns: 1fr 280px; gap:1.5rem; align-items:start;">

    <!-- Contenu principal -->
    <div>
        <div class="card" style="margin-bottom:1.5rem;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                <div>
                    <div style="color:var(--muted); font-size:0.78rem; margin-bottom:0.4rem;">Ticket #<?= $ticket['id'] ?></div>
                    <h1 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.4rem; line-height:1.3;"><?= htmlspecialchars($ticket['titre']) ?></h1>
                </div>
                <span class="badge badge-<?= $ticket['statut'] ?>" style="font-size:0.8rem;"><?= str_replace('_', ' ', $ticket['statut']) ?></span>
            </div>

            <div style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border); line-height:1.7; color: #c8c8d8; white-space:pre-wrap;">
                <?= htmlspecialchars($ticket['description']) ?>
            </div>
        </div>

        <!-- COMMENTAIRES -->
        <div id="comments">
            <h2 style="font-family:'Syne',sans-serif; font-weight:700; font-size:1rem; margin-bottom:1rem; color:var(--muted);">
                COMMENTAIRES (<?= count($commentaires) ?>)
            </h2>

            <?php if (empty($commentaires)): ?>
                <div class="card" style="text-align:center; color:var(--muted); padding:2rem;">
                    Aucun commentaire pour le moment.
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:1.5rem;">
                    <?php foreach ($commentaires as $c): ?>
                    <div class="card" style="padding:1rem 1.25rem; <?= $c['role'] === 'admin' ? 'border-left:3px solid var(--accent);' : '' ?>">
                        <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.5rem;">
                            <span style="font-weight:600; font-size:0.88rem;"><?= htmlspecialchars($c['nom']) ?></span>
                            <?php if ($c['role'] === 'admin'): ?>
                                <span class="badge" style="background:rgba(255,107,53,0.15); color:var(--accent); font-size:0.68rem;">Support</span>
                            <?php endif; ?>
                            <span style="color:var(--muted); font-size:0.78rem; margin-left:auto;"><?= date('d/m/Y à H:i', strtotime($c['created_at'])) ?></span>
                        </div>
                        <p style="line-height:1.6; color:#c8c8d8; font-size:0.9rem; white-space:pre-wrap;"><?= htmlspecialchars($c['message']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire commentaire -->
            <?php if ($ticket['statut'] !== 'fermé'): ?>
            <div class="card">
                <div class="card-title">Ajouter un commentaire</div>
                <form method="POST">
                    <div class="form-group">
                        <textarea name="message" placeholder="Votre message..." rows="4"></textarea>
                    </div>
                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
            <?php else: ?>
                <div style="text-align:center; padding:1rem; color:var(--muted); font-size:0.85rem; background:var(--surface); border-radius:var(--radius); border:1px solid var(--border);">
                    Ce ticket est fermé. Les commentaires sont désactivés.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar infos -->
    <div>
        <div class="card" style="margin-bottom:1rem;">
            <div class="card-title">Informations</div>
            <div style="display:flex; flex-direction:column; gap:0.85rem;">
                <div>
                    <div style="font-size:0.75rem; color:var(--muted); margin-bottom:0.25rem;">STATUT</div>
                    <span class="badge badge-<?= $ticket['statut'] ?>"><?= str_replace('_', ' ', $ticket['statut']) ?></span>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--muted); margin-bottom:0.25rem;">PRIORITÉ</div>
                    <span class="badge badge-<?= $ticket['priorite'] ?>"><?= $ticket['priorite'] ?></span>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--muted); margin-bottom:0.25rem;">CATÉGORIE</div>
                    <div style="font-size:0.9rem;"><?= htmlspecialchars($ticket['categorie']) ?></div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--muted); margin-bottom:0.25rem;">AUTEUR</div>
                    <div style="font-size:0.9rem;"><?= htmlspecialchars($ticket['auteur']) ?></div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--muted); margin-bottom:0.25rem;">CRÉÉ LE</div>
                    <div style="font-size:0.9rem;"><?= date('d/m/Y à H:i', strtotime($ticket['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Changer le statut (admin seulement) -->
        <?php if (isAdmin()): ?>
        <div class="card">
            <div class="card-title">Changer le statut</div>
            <form method="POST" style="display:flex; flex-direction:column; gap:0.5rem;">
                <select name="statut" style="margin-bottom:0.5rem;">
                    <option value="ouvert"   <?= $ticket['statut'] === 'ouvert'   ? 'selected' : '' ?>>🔵 Ouvert</option>
                    <option value="en_cours" <?= $ticket['statut'] === 'en_cours' ? 'selected' : '' ?>>🟡 En cours</option>
                    <option value="fermé"    <?= $ticket['statut'] === 'fermé'    ? 'selected' : '' ?>>🟢 Fermé</option>
                </select>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Mettre à jour</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
