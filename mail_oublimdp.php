<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

if(!empty($_GET['redir']))
{
    $redir = trim(strip_tags($_GET['redir']));

    if(!empty($_GET['mdp']) && !empty($_GET['email'])){

        $mdp = trim(strip_tags($_GET['mdp']));
        $email = trim(strip_tags($_GET['email']));

        //debug($token);
        //debug($email);

        $sujet = 'Récupération de mot de passe';
        $contenu = ' <img src="https://zupimages.net/up/21/46/l5ml.png" alt="logo vaccinote" width="150">
                        <h2>Mot de passe oublié</h2>
                        <p style="padding-bottom: 1rem;">Un nouveau mot de passe a été créé pour votre compte <strong>'.$email.'</strong>.</p>
                        <p style="padding-bottom: 1rem;">Votre nouveau mot de passe temporaire : <strong>'.$mdp.'</strong></p>
                        <p style="padding-bottom: 1rem;">Il est fortement conseillé de modifier ce mot de passe depuis votre espace personnel.</p>
                        <a href="'.$redir.'">Aller sur Vaccinote</a>';

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.ionos.fr';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPKeepAlive = true;
        $mail->Username = 'vaccinote@jouen.eu';
        $mail->Password = 'vaccinote123.';
        $mail->setFrom('vaccinote@jouen.eu', 'Vaccinote');
        $mail->AddAddress($email);
        $mail->Subject = $sujet;
        $mail->Body = $contenu;
        $mail->isHTML(true);
        $mail->CharSet = "utf-8";

        if (!$mail->send()) {
            echo 'erreur mailer : ' . $mail->ErrorInfo;
        } else {
            header('Location: '.$redir.'connexion.php');
            die();
        }

        header('Location: '.$redir);
        die();
    }

    header('Location: '.$redir);
}
else{
    header('Location: http://localhost');
}
?>