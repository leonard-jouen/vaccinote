<?php
session_start();

require('./inc/pdo.php');
require('./inc/fonctions.php');
$page_title = 'Inscription';


if(isLoggedIn()){
    header('Location: compte.php');
    die();
}

$errors = array();
$conditions = false;
$success = false;
if(!empty($_POST['submitted'])){
    //Faille xss
    $post = clearXssAll($_POST);
    //debug($post);
    //validation
    $errors = textValidation($errors,$post['nom'],'nom',2,50);
    $errors = textValidation($errors,$post['prenom'],'prenom',2,10);
    $errors = emailValidation($errors,$post['email'],'email');
    $errors = textValidation($errors,$post['password'],'password',6,50);
    $errors = textValidation($errors,$post['password2'],'password2',6,50);
    $errors = confPassword($errors,$post['password'],$post['password2'],'password');

    if(!empty($post['conditions'])){
        $conditions = true;
    }else {
        $errors['conditions'] ='Veuillez accepter les conditions';
    }

    $sql = "SELECT count(id) FROM vac_users WHERE email = :email";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email',$post['email'],PDO::PARAM_STR);
    $query->execute();
    $count = $query->fetchColumn();
    if($count > 0){
        $errors['email'] = 'Un compte existe déjà avec cette adresse mail';
    }

    // si pas d'erreur
    if(count($errors) == 0) {
        $token = generateRandomString(100);
        $sql = "INSERT INTO vac_users (nom,prenom,email,created_at,mdp,token) VALUES (:nom, :prenom, :email, NOW(), :password, :token)";
        $query = $pdo->prepare($sql);
        $query->bindValue(':nom',$post['nom'],PDO::PARAM_STR);
        $query->bindValue(':prenom',$post['prenom'],PDO::PARAM_STR);
        $query->bindValue(':email',$post['email'],PDO::PARAM_STR);
        $query->bindValue(':password',password_hash($post['password'], PASSWORD_DEFAULT),PDO::PARAM_STR);
        $query->bindValue(':token',$token,PDO::PARAM_STR);
        $query->execute();
        $success = true;
        $newid = $pdo->lastInsertId();
        /* Envoi du mail de vérification et redirection vers valid_mail.php */
        $_SESSION['user'] = array(
            'email'  => $post['email']
        );
        sendVerificationMail($_SESSION['user']['email'], $token);
    }
}


include ('inc/header.php');?>
<?php if ($success){?>
    <p>Merci pour votre inscription</p>
    <p><a href="valid_mail.php">Retour à l'accueil</a></p>
<?php } else {?>

    <section class="inscription flex">
        <div class="wrap_inscription">
            <div class="text_img">
                <h1>Inscription</h1>
                <p>Créez votre espace et obtenez le suivi de vos vaccins</p>
            </div>
        </div>
        <div class="wrap2_inscription">
            <div>
                <h2>Créer un compte</h2>
                <div class="separator"></div>
            </div>
            <form class="flex column" action="" method="post" class="wrapform" novalidate>
                <div class="flex sb nomprenom">
                    <div>
                        <label for="nom">Votre nom</label>
                        <input type="text" name="nom" id="nom" value="<?php recupInputValue('nom'); ?>">
                        <br><span class="error"><?php echoError($errors,'nom'); ?></span>
                    </div>
                    <div>
                        <label for="prenom">Votre prénom</label>
                        <input type="text" name="prenom" id="prenom" value="<?php recupInputValue('prenom'); ?>">
                        <br><span class="error"><?php echoError($errors,'prenom'); ?></span>
                    </div>
                </div>

                <label for="email">Votre adresse mail</label>
                <input type="email" name="email" id="email" value="<?php recupInputValue('email'); ?>">
                <span class="error"><?php echoError($errors,'email'); ?></span>
                <div class="passwords flex sb">
                    <div>
                        <label for="password">Votre mot de passe</label>
                        <input type="password" name="password" id="password" value="<?php recupInputValue('password'); ?>">
                        <br><span class="error"><?php echoError($errors,'password'); ?></span>
                    </div>
                    <div>
                        <label for="password">Confirmez votre mot de passe</label>
                        <input type="password" name="password2" id="password2" value="<?php recupInputValue('password2'); ?>">
                        <br><span class="error"><?php echoError($errors,'password2'); ?></span>
                    </div>
                </div>

                <?php
                if($conditions){
                    ?>
                    <div class="flex checkbox">
                        <input type="checkbox" name="conditions" checked required> J'ai lu et j'accepte les <br><a href="tapage" >conditions d'utilisation</a>
                        <span class="error"><?php echoError($errors,'conditions'); ?></span>
                    </div>
                    <?php
                }
                else{
                    ?>
                    <div class="flex checkbox">
                        <input type="checkbox" name="conditions" required>
                        <p>J'ai lu et j'accepte les <br><a href="tapage" >conditions d'utilisation</a></p>
                        <span class="error"><?php echoError($errors,'conditions'); ?></span>
                    </div>
                    <?php
                }
                ?>
                <input type="submit" name="submitted" value="Envoyer">
            </form>
            <p><a href="connexion.php">J'ai déjà un compte</a></p>
        </div>
    </section>
<?php }?>
<?php include ('inc/footer.php');
