<?php
session_start();
require('inc/pdo.php');
require('inc/fonctions.php');
$page_title = 'Envoyer un mail';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

$errors = array();
$destinataire = '';
$email = '';
$selectDestinataire = '';
$success = false;

if(!empty($_GET['success']) && is_numeric($_GET['success']) && intval($_GET['success']) == 1){
    $success = true;
}

if(!empty($_GET['destinataire'])){
    $destinataire = trim(strip_tags($_GET['destinataire']));
    $selectDestinataire = $destinataire;
}

if(!empty($_GET['email'])){
    $email = trim(strip_tags($_GET['email']));
}

if(!empty($_GET['submitted_destinataire'])){
    $email = cleanXSSGet('email');
    $destinataire = cleanXSSGet('destinataire');

    if(mb_strlen($destinataire) == 0){
        $errors['email'] = 'Veuillez renseigner un destinataire';
    }

    if(mb_strlen($email) > 0){
        $errors = emailValidation($errors,$email,'email');
    }
    else if($destinataire == 'user' && mb_strlen($email) == 0){
        $errors['email'] = 'Veuillez renseigner une adresse mail pour envoyer un message à un utilisateur spécifique';
    }

    if(count($errors) == 0){
        header('Location: newsletter.php?destinataire='.$destinataire.'&email='.$email);
    }
    else{
        $destinataire = '';
        $email = '';
    }
}

if(!empty($_POST['submitted'])){
    $message = cleanXSS('message');
    $titre = cleanXSS('titre');

    $errors = textValidation($errors,$message,'message',5,2000);
    $errors = textValidation($errors,$titre,'titre',5,150);

    if(count($errors) == 0){

        if($destinataire == 'all'){
            $sql = "SELECT email FROM vac_users";
            $query = $pdo->prepare($sql);
            $query->execute();
            $users = $query->fetchAll();

            $nb = 0;
            foreach ($users as $user){
                if(!empty($user['email'])){
                    sendNewsletterMail($user['email'], $message, $titre);
                    $nb++;
                }
            }
        }
        elseif($destinataire == 'admins'){
            $sql = "SELECT email FROM vac_users WHERE role = 'admin'";
            $query = $pdo->prepare($sql);
            $query->execute();
            $users = $query->fetchAll();

            $nb = 0;
            foreach ($users as $user){
                if(!empty($user['email'])){
                    sendNewsletterMail($user['email'], $message, $titre);
                    $nb++;
                }
            }
        }
        else if(mb_strlen($email) > 0){
            $errors = emailValidation($errors,$email,'user_mail');

            if(count($errors) == 0){
                sendNewsletterMail($email, $message, $titre);
            }
        }
    }
}

$sql = "SELECT * FROM vac_users";
$query = $pdo->prepare($sql);
$query->execute();
$users = $query->fetchAll();

include('inc/header.php'); ?>

<section id="newsletter">
    <div class="wrap">
        <h1>Newsletter</h1>
        <?php
        if($success){
            ?>
            <p class="success">Votre newsletter a bien été envoyée</p>
            <?php
        }
        ?>

        <?php
        if(mb_strlen($destinataire) == 0){
            ?>
            <form action="" method="get">
                <div class="field">
                    <label for="user">Utilisateur(s) :</label>
                    <select name="destinataire" id="destinataire">
                        <?php
                        if($selectDestinataire == 'user'){
                            ?>
                            <option value="all">Tous les utilisateurs</option>
                            <option value="user" selected>Utilisateur sélectionné (renseigner son adresse ci-dessous)</option>
                                <?php
                        }
                        else{
                            ?>
                            <option value="all" selected>Tous les utilisateurs</option>
                            <option value="user">Utilisateur sélectionné (renseigner son adresse ci-dessous)</option>
                                <?php
                        }
                        ?>
                        <option value="admins">Tous les admins</option>
                    </select>
                </div>
                <div id="adresse_mail_user" class="field">
                    <label for="email">Adresse mail (facultatif) :</label>
                    <input type="email" name="email" id="email" value="<?php recupInputValue('email'); ?>">
                    <span class="error"><?php echoError($errors, 'email'); ?></span>
                </div>
                <div class="field">
                    <input class="stylized_blue_input_submit" type="submit" name="submitted_destinataire" value="Continuer">
                </div>
            </form>
                <?php
        }
        else{
            ?>
            <form action="?email=<?php echo $email; ?>&destinataire=<?php echo $destinataire; ?>" method="post">
                <div class="field">
                    <label for="user">Destinataire(s) :</label>
                    <?php
                    if($destinataire == 'all'){
                        echo '<strong>Tous les utilisateurs</strong>';
                    }
                    elseif($destinataire == 'admins'){
                        echo '<strong>Tous les admins</strong>';
                    }
                    else{
                        echo '<strong>'.$email.'</strong>';
                    }
                    ?>
                </div>

                <div class="field">
                    <label for="titre">Titre :</label>
                    <input type="text" name="titre" id="titre" value="<?php recupInputValue('titre'); ?>">
                    <span class="error"><?php echoError($errors, 'titre'); ?></span>
                </div>

                <div class="field">
                    <label for="message">Message :</label>
                    <textarea name="message" id="message" placeholder="Votre message ..."><?php recupInputValue('message'); ?></textarea>
                    <span class="error"><?php echoError($errors, 'message'); ?></span>
                </div>


                <div class="field">
                    <input class="stylized_blue_input_submit" type="submit" name="submitted" value="Envoyer">
                </div>
            </form>
                <?php
        }
        ?>
    </div>
</section>

<?php

include('inc/footer.php');
