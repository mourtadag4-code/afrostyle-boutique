<?php
session_start();
require_once 'commun/connexiondb.php';

if (!isset($_SESSION['utilisateur_id']) || empty($_SESSION['panier'])) {
    header("Location: panier.php"); exit();
}

$total_final = 0;
foreach ($_SESSION['panier'] as $id => $item) {
    // SÉCURITÉ MAXIMALE : On check toutes les clés possibles
    if (isset($item['qte'])) {
        $qte_claire = (int)$item['qte'];
    } elseif (isset($item['quantite'])) {
        $qte_claire = (int)$item['quantite'];
    } else {
        $qte_claire = 1;
    }
    
    $total_final += (float)$item['prix'] * $qte_claire;
    
    // On met à jour la session proprement pour la suite du script
    $_SESSION['panier'][$id]['qte_verifiee'] = $qte_claire;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Commande
        $stmt1 = $pdo->prepare("INSERT INTO commande (id_utilisateur, montant_total, statut_commande) VALUES (?, ?, 'en_attente')");
        $stmt1->execute([$_SESSION['utilisateur_id'], $total_final]);
        $id_cmd = $pdo->lastInsertId();

        // 2. Détails (On utilise la clé qte_verifiee créée juste au dessus)
        $stmt2 = $pdo->prepare("INSERT INTO details_commande (id_commande, id_produit, Quantite_commande, prix_unitaire) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['panier'] as $id_p => $d) {
            $stmt2->execute([$id_cmd, $id_p, $d['qte_verifiee'], $d['prix']]);
        }

        // 3. Paiement
        $methode = $_POST['methode_paiement'];
        $stmt3 = $pdo->prepare("INSERT INTO paiement (id_commande, montant, methode_paiement, statut_paiement) VALUES (?, ?, ?, 'en_attente')");
        $stmt3->execute([$id_cmd, $total_final, $methode]);

        $pdo->commit();
        unset($_SESSION['panier']);
        header("Location: succes.php?id=" . $id_cmd); exit();

    } catch (Exception $e) { 
        $pdo->rollBack(); 
        die("Erreur base de données : " . $e->getMessage()); 
    }
}

$page_title = "Paiement AfroStyle";
include_once 'commun/header.php';
?>

<div class="container py-5 text-center">
    <div class="card shadow border-0 mx-auto p-5" style="max-width: 500px; border-radius: 20px;">
        <h2 class="fw-bold mb-4">PAIEMENT</h2>
        <form method="POST">
            <p class="text-muted">Somme à régler :</p>
            <h3 class="fw-bold text-danger mb-4"><?= number_format($total_final, 0, '', ' ') ?> FCFA</h3>
            
            <select name="methode_paiement" class="form-select mb-4 py-3" required>
                <option value="wave">Orange Money / Wave</option>
                <option value="carte">Carte Bancaire</option>
                <option value="livraison">Paiement à la livraison</option>
            </select>

            <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow">CONFIRMER MON ACHAT</button>
        </form>
    </div>
</div>
<?php include_once 'commun/footer.php'; ?>