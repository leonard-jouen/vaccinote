<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

if(!empty($_GET['redir']))
{
    $redir = trim(strip_tags($_GET['redir']));

    if(!empty($_GET['message']) && !empty($_GET['email'])){

        $message = trim(strip_tags($_GET['message']));
        $email = trim(strip_tags($_GET['email']));

        $sujet = 'Message envoyé depuis la page Contact';
        $contenu = ' <img src="https://zupimages.net/up/21/46/l5ml.png" alt="logo vaccinote" width="150">
                        <p style="padding-bottom: 1rem;">From: '.$email.'</p>
                        <p style="padding-bottom: 1rem;">Message: '.$message.'</p>
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
        $mail->AddAddress('vaccinote@gmail.com');
        $mail->Subject = $sujet;
        $mail->Body = $contenu;
        $mail->isHTML(true);
        $mail->CharSet = "utf-8";

        if (!$mail->send()) {
            echo 'erreur mailer : ' . $mail->ErrorInfo;
        } else {
            header('Location: '.$redir.'contact.php?success=1');
            die();
        }

        header('Location: '.$redir.'contact.php');
        die();
    }

    header('Location: '.$redir);
}
else{
    header('Location: http://localhost');
}
?>