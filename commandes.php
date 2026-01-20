<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'commun/connexiondb.php';

// 1. SECURITÉ : Obliger la connexion
if(!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['utilisateur_id'];

// 2. TRAITEMENT DE LA COMMANDE (Si le panier n'est pas vide)
if (!empty($_SESSION['panier'])) {
    $total_commande = 0;
    foreach ($_SESSION['panier'] as $item) {
        // Sécurité : s'assurer que 'quantite' existe dans la session
        $qte = isset($item['quantite']) ? (int)$item['quantite'] : 1;
        $total_commande += $item['prix'] * $qte;
    }

    try {
        $pdo->beginTransaction();

        // Insertion dans la table 'commande' (Table n°5)
        $stmt1 = $pdo->prepare("INSERT INTO commande (id_utilisateur, montant_total, statut_commande) VALUES (?, ?, 'en_attente')");
        $stmt1->execute([$user_id, $total_commande]);
        $id_commande = $pdo->lastInsertId();

        // Insertion dans 'details_commande' (Table n°6)
        // Note : On respecte bien la majuscule 'Quantite_commande' de ton SQL
        $stmt2 = $pdo->prepare("INSERT INTO details_commande (id_commande, id_produit, Quantite_commande, prix_unitaire) VALUES (?, ?, ?, ?)");
        
        foreach ($_SESSION['panier'] as $id_produit => $details) {
            $qte_finale = isset($details['quantite']) ? (int)$details['quantite'] : 1;
            
            $stmt2->execute([
                $id_commande, 
                $id_produit, 
                $qte_finale, 
                $details['prix']
            ]);
        }

        $pdo->commit();
        unset($_SESSION['panier']); // Vider le panier après succès

        // Redirection vers succes.php
        header("Location: succes.php?id=" . $id_commande);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}

// 3. AFFICHAGE DE L'HISTORIQUE (Si on accède à la page normalement)
$page_title = "Mes Commandes - AfroStyle";
require_once 'commun/header.php';

$sql = "SELECT * FROM commande WHERE id_utilisateur = ? ORDER BY date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$mes_commandes = $stmt->fetchAll();
?>

<div class="container py-5" style="min-height: 70vh;">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Historique de mes achats</h2>

    <?php if(empty($mes_commandes)): ?>
        <div class="alert alert-light border shadow-sm p-4 text-center">
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="catalogue.php" class="btn btn-warning rounded-pill">Boutique</a>
        </div>
    <?php else: ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle bg-white mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">N°</th>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mes_commandes as $cmd): ?>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">#<?= $cmd['id_commande'] ?></td>
                        <td><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></td>
                        <td class="fw-bold"><?= number_format($cmd['montant_total'], 0, ',', ' ') ?> FCFA</td>
                        <td>
                            <span class="badge rounded-pill bg-warning text-dark">
                                <?= strtoupper(str_replace('_', ' ', $cmd['statut_commande'])) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'commun/footer.php'; ?>