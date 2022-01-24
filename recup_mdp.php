<?php
session_start();
$page_title = 'Récupération de mot de passe';

require ('inc/fonctions.php');
require ('inc/pdo.php');
if(isLoggedIn()){
    header('Location: ./compte.php');
    die();
}
$errors = array();
$success = false;
//debug($_POST);
if(!empty($_GET['success']) && is_numeric($_GET['success'])){
    $success = true;
}

if (!empty($_POST['submitted'])){
    //faille xss
    $post = clearXssAll($_POST);
    $email = $post['email'];

    //validation
    $errors = emailValidation($errors,$email,'email');

    //si pas d'erreur

    $sql = "SELECT count(id) FROM vac_users WHERE email = :email";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email',$email,PDO::PARAM_STR);
    $query->execute();
    $count = $query->fetchColumn();
    if($count == 0){
        $errors['email'] = 'Aucun compte trouvé avec cette adresse mail';
    }
    if (count($errors) == 0){
        $newpassword = generateRandomString(15);
        $sql = "UPDATE vac_users SET mdp = :newpassword WHERE email = :email";
        $query = $pdo->prepare($sql);
        $query->bindValue(':newpassword',password_hash($newpassword, PASSWORD_DEFAULT),PDO::PARAM_STR);
        $query->bindValue(':email',$email,PDO::PARAM_STR);
        $query->execute();
        $success = true;
        sendMdpOubliMail($email,$newpassword);
    }
}

include('inc/header.php');?>
    <section id="mdp_oublie">
        <div class="wrap_recup flex sb">
            <div class="wrap_mdp">
                <div class="titre">
                    <h2>Mot de passe oublié</h2>
                    <div class="separator"></div>
                </div>
                <div class="envoie_email">
                    <form action="" method="post">
                        <div class="form-recup">
                            <label for="email">Votre adresse mail</label>
                            <input type="email" name="email" value="<?php recupInputValue('email'); ?>">
                            <span class="error"><?php echoError($errors, 'email'); ?></span>

                            <input type="submit" name="submitted" value="envoyer">
                        </div>
                    </form>
                </div>
            </div>
            <div class="wrap_img">
                <img src="asset/img/main-img.jpg" alt="">
            </div>
        </div>
    </section>
<?php include('inc/footer.php');
