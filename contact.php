<?php
session_start();
require ('inc/fonctions.php');
require ('inc/pdo.php');
$page_title = 'Contactez nous';


$errors = array();
$success = false;
if(!empty($_GET['success']) && is_numeric($_GET['success'])){
    $success = true;
}
//debug($_POST);
if (!empty($_POST['submitted'])){
    //faille xss
    $post = clearXssAll($_POST);
    $email = $post['email'];
    $message = $post['message'];

    //validation
    $errors = emailValidation($errors,$email,'email');
    $errors = textValidation($errors,$message,'message',10,20000);

    //si pas d'erreur
    if (count($errors) == 0){
        sendContactMail($email,$message);
    }
}
include('inc/header.php');?>
<?php if ($success){?>
    <p>Votre message a bien été transmit</p>
    <p><a href="index.php">Retour à l'accueil</a></p>
<?php } else {?>
    <div class="wrap_contact flex sb">
        <div class="formulaire_contact ">
            <form method="post">
                <h2>Contact</h2>
                <div class="separator"></div>
                <label>Votre adresse mail</label>
                <?php if (isLoggedIn()) { ?>
                    <input type="email" name="email" value="<?= $_SESSION['user']['email']; ?>" required><br>
                <?php } else {?>
                    <input type="email" name="email" value="<?php recupInputValue('email'); ?>" required><br>
                <?php }?>
                <span class="error"><?php echoError($errors, 'email'); ?></span>

                <label>Message</label>
                <textarea name="message" rows="7" required></textarea><br>
                <span class="error"><?php echoError($errors, 'message'); ?></span>
            <div class="input_envoyer">
                <input type="submit" name="submitted" value="Envoyer">
            </div>
            </form>
        </div>
        <div class="adresse">
            <h2>Notre adresse :</h2>
            <div class="separator"></div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d324.30898370143075!2d1.0943338634432225!3d49.43779792705673!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e0df50c67e1985%3A0x23d44ae884dc7ce6!2sFury%20Bar!5e0!3m2!1sfr!2sfr!4v1637655286553!5m2!1sfr!2sfr" width="90%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
<?php }?>
<?php include('inc/footer.php');
