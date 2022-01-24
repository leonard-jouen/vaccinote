<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$page_title = 'Connexion';


if(isLoggedIn()){
    header('Location: ./compte.php');
    die();
}

$redir = '';
if(!empty($_GET['redir'])){
    $redir = trim(strip_tags($_GET['redir']));
}

$validemail = false;
if(!empty($_GET['validemail']) && is_numeric($_GET['validemail']) && intval($_GET['validemail']) == 1){
    $validemail = true;
}

$errors = array();
$rememberMe = false;

if(!empty($_POST['submitted']) || isset($_COOKIE["member_email"])){

    if(!empty($_POST['submitted'])) {
        $connectionType = 'formulaire'; /** Connexion en utilisant le formulaire */
    }
    else {
        $connectionType = 'cookie'; /** Connexion en récupérant l'email stocké dans le cookie */
    }

    if($connectionType === 'formulaire') {
        $post = clearXssAll($_POST);
        $email = $post['email'];
        $password = $post['password'];

        $errors = emailValidation($errors, $email, 'email');
        $errors = textValidation($errors, $password, 'password', 6, 25);

        if(!empty($post['remember'])){
            $rememberMe = true;
        }
    }
    else{
        $rememberMe = true;
        $email = trim(strip_tags($_COOKIE["member_email"]));
        $errors = emailValidation($errors, $email, 'email');
    }

    if(count($errors) == 0){
        $sql = "SELECT * FROM vac_users WHERE email = :email AND role != 'banni'";
        $query = $pdo->prepare($sql);
        $query->bindValue(':email',$email,PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch();

        if(!empty($user) && mb_strlen($user['mdp']) > 0){
            if ($connectionType === 'cookie' || password_verify($password, $user['mdp'])) {

                /** Création du cookie pour: Se souvenir de moi */
                if($rememberMe) {
                    setcookie ('member_email',$user['email'],time() + (60*60*24*7)); // valide 7 jours
                } else {
                    if (isset($_COOKIE['member_email'])) {
                        setcookie('member_email', '');
                    }
                }

                if(intval($user['validemail']) == 1){ /** ajouter validemail bdd */
                    $_SESSION['user'] = array(
                        'id'     => $user['id'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'email'  => $user['email'],
                        'role'   => $user['role'], /** ajouter role bdd */
                        'created_at'   => $user['created_at'],
                        'ip'     => $_SERVER['REMOTE_ADDR']
                    );

                    if(mb_strlen($redir) > 0){

                        if($redir === 'first_complete_edit'){
                            header('Location: edit_details.php?first_edit=1');
                        }
                        else{
                            header('Location: '.$redir);
                        }
                    }
                    else{
                        header('Location: ./compte.php');
                    }
                }
                else{
                    $_SESSION['user'] = array(
                        'email'  => $user['email']
                    );

                    header('Location: ./valid_mail.php');
                }
            } else {
                $errors['password'] = 'Mot de passe invalide';
            }
        }
        else{
            $errors['email'] = 'Aucun compte trouvé avec cette adresse mail';
        }
    }
}

include('inc/header.php'); ?>

    <section id="connexion">
        <div class="wrap flex">
            <div class="connexion_img">
                <h1>Connexion</h1>
                <p>Accédez à votre espace et obtenez le suivi de vos vaccins</p>
            </div>
            <div class="connexion_form">
                <h2>Connexion</h2>
                <div class="separator"></div>

                <?php
                if($validemail){
                    ?>
                    <p style="color: green; padding: 1rem; text-align: center;">Votre adresse mail a bien été validée. Vous pouvez désormais <strong>vous connecter</strong>.</p>
                    <?php
                }
                ?>

                <form action="" method="post">
                    <div class="form-field">
                        <label for="email">Votre adresse mail</label>
                        <input type="email" name="email" value="<?php recupInputValue('email'); ?>">
                        <span class="error"><?php echoError($errors, 'email'); ?></span>
                    </div>
                    <div class="form-field">
                        <label for="password">Votre mot de passe</label>
                        <input type="password" name="password" value="<?php recupInputValue('password'); ?>">
                        <span class="error"><?php echoError($errors, 'password'); ?></span>
                    </div>
                    <div class="form-field checkbox flex">
                        <?php
                        if($rememberMe || isset($_COOKIE["member_email"])){
                            ?>
                            <input type="checkbox" name="remember" checked>
                            <?php
                        }
                        else{
                            ?>
                            <input type="checkbox" name="remember">
                            <?php
                        }
                        ?>
                        <label for="rememberMe">Se souvenir de moi</label>
                        <span class="error"><?php echoError($errors, 'rememberMe'); ?></span>
                    </div>
                    <div class="form-field">
                        <input type="submit" name="submitted" value="Connexion">
                    </div>
                </form>

                <a href="./inscription.php">Je n'ai pas de compte</a>
                <p><a href="recup_mdp.php">Mot de passe oublié</a></p>
            </div>
        </div>
    </section>

<?php include('inc/footer.php');
