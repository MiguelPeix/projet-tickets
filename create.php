<?php
// create.php — Créer un nouveau ticket
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre      = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priorite   = $_POST['priorite'] ?? 'normale';
    $categorie  = $_POST['categorie'] ?? 'autre';

    $priorites_valides  = ['basse', 'normale', 'haute', 'urgente'];
    $categories_valides = ['matériel', 'logiciel', 'réseau', 'autre'];

    if (empty($titre)) $errors[] = 'Le titre est obligatoire.';
    if (empty($description)) $errors[] = 'La description est obligatoire.';
    if (!in_array($priorite, $priorites_valides)) $errors[] = 'Priorité invalide.';
    if (!in_array($categorie, $categories_valides)) $errors[] = 'Catégorie invalide.';

    if (empty($errors)) {
        $user = getCurrentUser();
        $stmt = $pdo->prepare('INSERT INTO tickets (user_id, titre, description, priorite, categorie) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$user['id'], $titre, $description, $priorite, $categorie]);
        $id = $pdo->lastInsertId();

        setFlash('success', 'Ticket #' . $id . ' créé avec succès !');
        header('Location: /projet-tickets/ticket.php?id=' . $id);
        exit;
    }
}

$pageTitle = 'Nouveau ticket — SupportIT';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>Nouveau ticket</h1>
    <p>Décrivez votre problème, nous vous répondrons rapidement.</p>
</div>

<div style="max-width:680px;">
    <div class="card">
        <?php if (!empty($errors)): ?>
            <div style="background:#2e1515; border-left:3px solid var(--danger); color:#fca5a5; padding:0.75rem 1rem; border-radius:7px; margin-bottom:1.25rem; font-size:0.88rem;">
                <?= htmlspecialchars($errors[0]) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Titre du problème *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" placeholder="Ex : Mon PC ne démarre plus" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie">
                        <option value="matériel"  <?= ($_POST['categorie'] ?? '') === 'matériel'  ? 'selected' : '' ?>>🖥 Matériel</option>
                        <option value="logiciel"  <?= ($_POST['categorie'] ?? '') === 'logiciel'  ? 'selected' : '' ?>>💿 Logiciel</option>
                        <option value="réseau"    <?= ($_POST['categorie'] ?? '') === 'réseau'    ? 'selected' : '' ?>>🌐 Réseau</option>
                        <option value="autre"     <?= ($_POST['categorie'] ?? 'autre') === 'autre' ? 'selected' : '' ?>>❓ Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Priorité</label>
                    <select name="priorite">
                        <option value="basse"   <?= ($_POST['priorite'] ?? '') === 'basse'   ? 'selected' : '' ?>>🟢 Basse</option>
                        <option value="normale" <?= ($_POST['priorite'] ?? 'normale') === 'normale' ? 'selected' : '' ?>>🔵 Normale</option>
                        <option value="haute"   <?= ($_POST['priorite'] ?? '') === 'haute'   ? 'selected' : '' ?>>🟡 Haute</option>
                        <option value="urgente" <?= ($_POST['priorite'] ?? '') === 'urgente' ? 'selected' : '' ?>>🔴 Urgente</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Description détaillée *</label>
                <textarea name="description" placeholder="Décrivez le problème en détail : depuis quand, sur quel appareil, ce que vous avez déjà essayé..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div style="display:flex; gap:0.75rem; justify-content:flex-end; margin-top:0.5rem;">
                <a href="/projet-tickets/index.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Soumettre le ticket</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
