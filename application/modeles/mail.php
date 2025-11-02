<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ROOT_PATH . '/lib/phpmailer/src/Exception.php';
require_once ROOT_PATH . '/lib/phpmailer/src/PHPMailer.php';
require_once ROOT_PATH . '/lib/phpmailer/src/SMTP.php';

/**
 * Envoie un mail avec PHPMailer
 * @param string $destinataire
 * @param string $nomDestinataire
 * @return bool
 */
function envoyerMail(string $destinataire, string $nomDestinataire = ''): bool {
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP pour test
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ecoride.mail@gmail.com'; // email 
        $mail->Password   = 'tvrqx zcti pyex ngmk'; // mot de passe ou token SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Expéditeur
        $mail->setFrom('ecoride.mail@gmail.com', 'Ecoride');

        // Destinataire
        $mail->addAddress($destinataire, $nomDestinataire);

        // Contenu du mail
        $mail->isHTML(true);
        $mail->Subject = "Merci de confirmer votre trajet";

        $lienEspace = PUBLIC_URL . "/index.php?page=espace_utilisateur";
        $mail->Body = "
            <p>Bonjour " . htmlspecialchars($nomDestinataire) . ",</p>
            <p>Vous avez récemment effectué un trajet via notre plateforme. Afin de finaliser ce voyage, merci de vous rendre sur votre espace personnel pour le confirmer.</p>
            <p> <a href='{$lienEspace}'>Cliquez ici pour vous connecter</a></p>
            <p>Cela nous permet de valider le bon déroulement du trajet et d’améliorer l’expérience pour tous les utilisateurs.</p>
            <p>Merci pour votre retour,<br>L’équipe Ecoride</p>
        ";
        $mail->AltBody = strip_tags($mail->Body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Affiche l'erreur en local
        echo "Message non envoyé à {$destinataire}. Erreur : {$mail->ErrorInfo}<br>";
        return false;
    }
}
