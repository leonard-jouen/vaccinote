<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';
/*
function debug($tab) {
    echo '<pre style="height: 200px;overflow-y: scroll;font-size: 0.7rem; padding: 0.5rem; font-family: Consolas, monospace; background-color: black; color: palegreen;  text-shadow:0 0 15px forestgreen,
    0 0 20px forestgreen,
    0 0 31px forestgreen,
    0 0 42px forestgreen,
    0 0 92px forestgreen,
    0 0 102px forestgreen,
    0 0 112px forestgreen,
    0 0 161px forestgreen;
    
}">';
    print_r($tab);
    echo '</pre>';
};

debug($_GET);*/

if(!empty($_GET['redir']))
{
    $redir = trim(strip_tags($_GET['redir']));

    if(!empty($_GET['token']) && !empty($_GET['email'])){

        $token = trim(strip_tags($_GET['token']));
        $email = trim(strip_tags($_GET['email']));

        //debug($token);
        //debug($email);

        $sujet = 'Confirmez votre adresse mail sur Vaccinote';
        $contenu = '    <img src="https://zupimages.net/up/21/46/l5ml.png" alt="logo vaccinote" width="150">
                        <h2>Confirmez votre adresse mail pour accéder à Vaccinote</h2>
                        <p style="padding-bottom: 1rem;">Quand vous aurez confirmé que <strong>'.$email.'</strong> est votre adresse mail, vous pourrez accéder à votre espace personnel.</p>
                        <a style="
                            padding: 1rem;
                            font-weight: bold;
                            border-radius: 5px;
                            background-color: #3c5499;
                            color: white;" href="'.$redir.'valid_mail.php?email='.$email.'&token='.$token.'">Confirmer mon adresse mail</a>
                        <p>Si le lien ne fonctionne pas, vous pouvez vous rendre ici : '.$redir.'valid_mail.php?email='.$email.'&token='.$token.'</p>
                        <p>Si vous n’avez pas demandé à recevoir cet e-mail, vous pouvez simplement l’ignorer.</p>
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
            header('Location: '.$redir.'valid_mail.php?email='.$email);
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