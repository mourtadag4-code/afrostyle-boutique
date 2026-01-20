<?php
session_start();
$page_title = "Contact - AfroStyle";
require_once "commun/header.php";

// --- LOGIQUE D'ENVOI RÉEL ---
$success_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Destination (Ton email d'admin)
    $destinataire = "attoumanenisrine1@gmail.com"; 
    
    // 2. Récupération des données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $email_client = htmlspecialchars($_POST['email']);
    $sujet_client = htmlspecialchars($_POST['sujet']);
    $message_client = htmlspecialchars($_POST['message']);

    // 3. Préparation de l'email
    $sujet_mail = "SITE AFROSTYLE - Nouveau message : " . $sujet_client;
    
    $corps_mail = "Vous avez reçu un nouveau message de votre site web :\n\n";
    $corps_mail .= "Nom : " . $nom . "\n";
    $corps_mail .= "Email : " . $email_client . "\n";
    $corps_mail .= "Sujet : " . $sujet_client . "\n\n";
    $corps_mail .= "Message :\n" . $message_client . "\n";
    
    // Pour pouvoir répondre directement au client en cliquant sur "Répondre"
    $headers = "From: " . $email_client . "\r\n" .
               "Reply-To: " . $email_client . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // 4. Envoi (Fonctionnera une fois le site en ligne)
    @mail($destinataire, $sujet_mail, $corps_mail, $headers);

    // 5. Message de confirmation pour le client
    $success_msg = "Merci $nom ! Votre message a bien été envoyé. Notre équipe vous répondra sous 24h sur $email_client.";
}
?>

<style>
    :root { --or-afro: #D4AF37; --or-fonce: #B8860B; --creme: #f8f4eb; }
    
    .page-header { 
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; 
        color: white; padding: 60px 0; margin-bottom: 40px;
    }

    .contact-card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    
    .info-box {
        background: var(--or-afro);
        color: white;
        border-radius: 15px;
        padding: 30px;
        height: 100%;
    }

    .form-control:focus {
        border-color: var(--or-afro);
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .btn-primary-afro { 
        background: var(--or-afro); border: none; color: white; 
        font-weight: 600; padding: 12px; border-radius: 10px;
    }
    .btn-primary-afro:hover { background: var(--or-fonce); color: white; }

    .icon-circle {
        width: 50px; height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 15px; font-size: 1.5rem;
    }
</style>

<div class="page-header text-center">
    <div class="container">
        <h1 class="display-5 fw-bold">CONTACTEZ-NOUS</h1>
        <p class="lead">Une question ? Un besoin particulier ? Nous sommes à votre écoute.</p>
    </div>
</div>

<div class="container mb-5">
    <?php if ($success_msg): ?>
        <div class="alert alert-success shadow-sm border-0 text-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="info-box shadow">
                <h3 class="fw-bold mb-4">Nos Coordonnées</h3>
                
                <div class="mb-4 d-flex align-items-start">
                    <div class="icon-circle me-3"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Adresse</h6>
                        <p class="mb-0">123 Rue du Marché, Dakar, Sénégal</p>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-start">
                    <div class="icon-circle me-3"><i class="bi bi-telephone"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Téléphone</h6>
                        <p class="mb-0">+221 33 000 00 00</p>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-start">
                    <div class="icon-circle me-3"><i class="bi bi-envelope"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Email</h6>
                        <p class="mb-0">contact@afrostyle.com</p>
                    </div>
                </div>

                <hr class="my-4" style="background-color: rgba(255,255,255,0.3);">
                
                <h6 class="fw-bold mb-3">Suivez-nous</h6>
                <div class="d-flex gap-3 fs-4">
                    <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card contact-card shadow p-4 p-md-5">
                <h3 class="fw-bold mb-4">Envoyez-nous un message</h3>
                <form action="contact.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nom complet</label>
                            <input type="text" name="nom" class="form-control form-control-lg bg-light border-0" placeholder="Ex: Moussa Diop" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="exemple@mail.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Sujet</label>
                            <input type="text" name="sujet" class="form-control form-control-lg bg-light border-0" placeholder="De quoi souhaitez-vous discuter ?" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Message</label>
                            <textarea name="message" class="form-control form-control-lg bg-light border-0" rows="5" placeholder="Votre message ici..." required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary-afro w-100 shadow-sm">
                                <i class="bi bi-send-fill me-2"></i> Envoyer le message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "commun/footer.php"; ?>