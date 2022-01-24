<?php
session_start();
$page_title = 'Validez votre adresse E-mail';


require('./inc/pdo.php');
require('./inc/fonctions.php');

if(empty($_SESSION['user']) || empty($_SESSION['user']['email'])){
    if(!empty($_GET['email'])){
        $_SESSION['user'] = array(
            'email'  => trim(strip_tags($_GET['email']))
        );
    }
}

$validemail = 0;
if(!empty($_SESSION['user']['email'])) {
    $sql = "SELECT validemail FROM vac_users WHERE email = :email";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email', $_SESSION['user']['email'], PDO::PARAM_STR);
    $query->execute();
    $validemail = $query->fetchColumn();
}
else{
    redirect404();
}

/* Validation du lien reçu par mail */
$validationMail = false;
if(!empty($_GET['token']) && !empty($_GET['email']) && $validemail != 1){
    $token = trim(strip_tags($_GET['token']));
    $email = trim(strip_tags($_GET['email']));

    $errors = array();
    $errors = textValidation($errors, $token, 'token', 100, 100);
    $errors = emailValidation($errors, $email, 'email');

    if(count($errors) == 0){
        $sql = "SELECT id FROM vac_users WHERE email = :email AND token = :token AND validemail = 0";
        $query = $pdo->prepare($sql);
        $query->bindValue(':email',$email,PDO::PARAM_STR);
        $query->bindValue(':token',$token,PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch();

        if(!empty($user) && !empty($user['id'])){
            $sql = "UPDATE vac_users SET validemail = 1 WHERE id = :id";
            $query = $pdo->prepare($sql);
            $query->bindValue(':id',$user['id'],PDO::PARAM_INT);
            $query->execute();
            $validationMail = true;
            header('Location: connexion.php?redir=first_complete_edit&validemail=1');
        }
        else {
            redirect404();
        }
    }
    else{
        redirect404();
    }
}

$errors = array();

/* Formulaire de modification de l'adresse mail */
if(!empty($_POST['submitted']) && $validemail != 1){
    $email = cleanXss('email');
    $errors = emailValidation($errors, $email, 'email');

    if(count($errors) == 0){
        $sql = "UPDATE vac_users SET email = :newEmail WHERE email = :baseEmail LIMIT 1";
        $query = $pdo->prepare($sql);
        $query->bindValue(':newEmail',$email,PDO::PARAM_STR);
        $query->bindValue(':baseEmail',$_SESSION['user']['email'],PDO::PARAM_STR);
        $query->execute();
        $_SESSION['user'] = array(
            'email'  => $email
        );

        /** Renvoi du mail sur la nouvelle adresse mail */
        $sql = "SELECT token FROM vac_users WHERE email = :email";
        $query = $pdo->prepare($sql);
        $query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
        $query->execute();
        if($query->rowCount() > 0){
            $tmpToken = $query->fetchColumn();
            sendVerificationMail($_SESSION['user']['email'], $tmpToken);
        }
        header('Location: ./valid_mail.php');
    }
}

/* Page: modification de l'adresse mail */
$modif_email = false;
if(!empty($_GET['modif_email']) && is_numeric($_GET['modif_email']) && intval($_GET['modif_email']) == 1 && $validemail != 1){
    $modif_email = true;
}

/* Page: renvoi du mail */
$resendmail = false;
if(!empty($_GET['resendmail']) && is_numeric($_GET['resendmail']) && intval($_GET['resendmail']) == 1 && $validemail != 1){

    $resendmail = true;
    /** Renvoi du mail */
    $sql = "SELECT token FROM vac_users WHERE email = :email";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
    $query->execute();
    if($query->rowCount() > 0){
        $tmpToken = $query->fetchColumn();
        sendVerificationMail($_SESSION['user']['email'], $tmpToken);
    }
    else{
        redirect404();
    }
}

include('./inc/header.php'); ?>

    <section id="valid-mail">
        <div class="wrap">
            <div class="valid-mail-desc">
                <?php
                if($validemail == 1){
                    ?>
                    <h2>Information</h2>
                    <div class="separator"></div>
                    <p>Votre adresse mail a déjà été validée. Vous pouvez <a href="./connexion.php?redir=edit_details.php">vous connecter</a>.</p>
                    <?php
                }
                elseif($validationMail){
                    ?>
                    <h2>Finalisation de votre inscription</h2>
                    <div class="separator"></div>
                    <p>Votre adresse mail a bien été validée. Vous pouvez désormais <a href="./connexion.php?redir=edit_details.php">vous connecter</a>.</p>
                    <?php
                }
                elseif($modif_email){
                    ?>
                    <h2>Modifier mon adresse mail</h2>
                    <div class="separator"></div>
                    <form action="" method="post">
                        <label for="email">Nouvelle adresse mail :</label>
                        <input type="email" name="email" value = "<?php recupInputValue('email'); ?>">
                        <span class="error"><?php echoError($errors, 'email'); ?></span>
                        <input type="submit" name="submitted" value="Modifier">
                    </form>
                    <?php
                }
                elseif($resendmail){
                    ?>
                    <h2>Mail envoyé à nouveau</h2>
                    <div class="separator"></div>
                    <p>Un nouveau mail vient de vous être envoyé à l'adresse <strong><?= $_SESSION['user']['email']; ?></strong>. Cliquez sur le lien envoyé dans ce mail pour valider la création de votre compte. <strong>Pensez à consulter vos spams</strong>.</p>
                    <p><a href="?modif_email=1">Modifier mon adresse mail</a></p>
                    <?php
                }
                else{
                    ?>
                    <h2>Validez votre compte</h2>
                    <div class="separator"></div>
                    <p>Pour confirmer la création de votre compte, cliquez sur le lien dans le mail envoyé à l'adresse <strong><?= $_SESSION['user']['email']; ?></strong>. <strong>Pensez à consulter vos spams</strong>.</p>
                    <p><a href="?resendmail=1">Envoyer le mail à nouveau</a></p>
                    <p><a href="?modif_email=1">Modifier mon adresse mail</a></p>
                    <?php
                }
                ?>
            </div>
            <div class="valid-mail-img"></div>
        </div>
    </section>

<?php include('./inc/footer.php');
