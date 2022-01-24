<?php
session_start();
require('./inc/pdo.php');
require('./inc/fonctions.php');
$page_title = 'Modification du compte';


if(!isLoggedIn()){
    header('Location: ./connexion.php?redir=edit_details.php');
    die();
}

$first_edit = false;
if(!empty($_GET['first_edit']) && is_numeric($_GET['first_edit']) && intval($_GET['first_edit']) == 1){
    $first_edit = true;
}

$errors = array();

$sql = "SELECT * FROM vac_users WHERE id = :user_id";
$query = $pdo->prepare($sql);
$query->bindValue(':user_id',$_SESSION['user']['id'],PDO::PARAM_STR);
$query->execute();
$user = $query->fetch();

if(empty($user)){
    header('Location: logout.php');
}

if(!empty($_POST['submitted'])){
    $post = clearXssAll($_POST);
    if(!empty($post['sexe'])) {
        $sexe = intval($post['sexe']);
    }
    else{
        $sexe = 2;
    }

    $naissance = $post['naissance'];
    $adresse = $post['adresse'];
    $codepostal = $post['codepostal'];
    $ville = $post['ville'];
    $numTelephone = $post['numTelephone'];
    $password = $post['password'];
    $password_conf = $post['password_conf'];
    $email = $post['email'];

    if(empty($post['naissance'])){
        $naissance = $user['naissance'];
        $ageData = getAgeDataFromDate($naissance);
        $age = $ageData[1];
    }
    else{
        $ageData = getAgeDataFromDate($naissance);

        if($ageData[0] == 'annees' && ($ageData[1] < 0 || $ageData[1] > 130)){
            $errors['naissance'] = 'Veuillez renseigner une date de naissance correcte';
        }
        else {$age = $ageData[1];}
    }

    if(empty($post['adresse'])){
        $adresse = $user['adresse'];
    }
    else{
        $errors = textValidation($errors, $adresse, 'adresse', 6, 255);
    }

    if(empty($post['codepostal'])){
        $codepostal = $user['postal'];
    }
    else{
        $errors = textValidation($errors, $codepostal, 'codepostal', 5, 5);
    }

    if(empty($post['ville'])){
        $ville = $user['ville'];
    }
    else{
        $errors = textValidation($errors, $ville, 'ville', 2, 60);
    }

    if(empty($post['numTelephone'])){
        $numTelephone = $user['tel'];
    }
    else{
        $errors = textValidation($errors, $numTelephone, 'numTelephone', 10, 10);
    }

    $passwordChanged = false;
    if(!empty($post['password']) && !empty($post['password_conf']) && !empty($post['password_actuel'])){

        if(!password_verify($post['password_actuel'], $user['mdp'])){
            $errors['password_actuel'] = 'Mot de passe incorrect';
        }
        else{
            $errors = textValidation($errors, $password, 'password', 6, 50);
            $errors = textValidation($errors, $password_conf, 'password_conf', 6, 50);
            $errors = confPassword($errors,$post['password'],$post['password_conf'],'password');
            $passwordChanged = true;
        }
    }

    $emailChanged = false;
    if(empty($post['email'])){
        $email = $user['email'];
    }
    else if($email != $user['email']){
        if(empty($post['password_actuel_email'])){
            $errors['password_actuel_email'] = 'Pour changer votre adresse mail, vous devez renseigner votre mot de passe actuel';
        }
        else{
            if(!password_verify($post['password_actuel_email'], $user['mdp'])){
                $errors['password_actuel_email'] = 'Mot de passe incorrect';
            }
            else{
                $emailChanged = true;
                $errors = emailValidation($errors, $email, 'email');
            }
        }
    }

    if(count($errors) == 0){
        $sql = "UPDATE vac_users SET sexe = :sexe, age = :age, naissance = :naissance, adresse = :adresse,
                postal = :postal, ville = :ville, tel = :tel WHERE id = :user_id LIMIT 1";
        $query = $pdo->prepare($sql);
        $query->bindValue(':sexe',$sexe,PDO::PARAM_INT);
        $query->bindValue(':age',$age,PDO::PARAM_INT);
        $query->bindValue(':naissance',$naissance,PDO::PARAM_STR);
        $query->bindValue(':adresse',$adresse,PDO::PARAM_STR);
        $query->bindValue(':postal',$codepostal,PDO::PARAM_INT);
        $query->bindValue(':ville',$ville,PDO::PARAM_STR);
        $query->bindValue(':tel',$numTelephone,PDO::PARAM_STR);
        $query->bindValue(':user_id',$_SESSION['user']['id'],PDO::PARAM_INT);
        $query->execute();

        if($passwordChanged){
            $sql = "UPDATE vac_users SET mdp = :password WHERE id = :user_id LIMIT 1";
            $query = $pdo->prepare($sql);
            $query->bindValue(':password',password_hash($post['password'], PASSWORD_DEFAULT),PDO::PARAM_STR);
            $query->bindValue(':user_id',$_SESSION['user']['id'],PDO::PARAM_INT);
            $query->execute();
        }

        if($emailChanged){
            $token = generateRandomString(100);
            $sql = "UPDATE vac_users SET email = :new_email, token = :token, validemail = 0 WHERE id = :user_id LIMIT 1";
            $query = $pdo->prepare($sql);
            $query->bindValue(':new_email',$email,PDO::PARAM_STR);
            $query->bindValue(':token',$token,PDO::PARAM_STR);
            $query->bindValue(':user_id',$_SESSION['user']['id'],PDO::PARAM_INT);
            $query->execute();

            $_SESSION['user']['email'] = $email;
            sendVerificationMail($email, $token);
            header('Location: valid_mail.php');
        }

        header('location: compte.php?success=1');
    }
}

if(empty($user)){
    redirect404();
}

include('inc/header.php'); ?>

    <section id="edit-details">
        <div class="wrap">
            <div class="edit-detail-form">
                <h2>Complétez votre profil</h2>
                <div class="separator"></div>
                <p>Vous pouvez compléter ces champs pour obtenir des données plus personnalisées selon votre profil.</p>

                <form action="" method="post">
                    <div class="flex sb">
                        <div class="form-radio">
                            <?php
                            if(intval($user['sexe']) == 0){
                                ?>
                                <input type="radio" name="sexe" id="homme" value="0" checked>
                                <?php
                            }
                            else{
                                ?>
                                <input type="radio" name="sexe" id="homme" value="0">
                                <?php
                            }
                            ?>
                            <label for="homme">Homme</label>
                        </div>
                        <div class="form-radio">
                            <?php
                            if(intval($user['sexe']) == 1){
                                ?>
                                <input type="radio" name="sexe" id="femme" value="1" checked>
                                <?php
                            }
                            else{
                                ?>
                                <input type="radio" name="sexe" id="femme" value="1">
                                <?php
                            }
                            ?>
                            <label for="femme">Femme</label>
                        </div>
                    </div>
                    <div class="form-field column">
                        <label for="naissance">Votre date de naissance</label>
                        <input type="date" name="naissance" value="<?= $user['naissance']; ?>">
                        <span class="error"><?php echoError($errors, 'naissance'); ?></span>
                    </div>
                    <div class="flex sb">
                        <div class="form-field column">
                            <label for="adresse">Votre adresse</label>
                            <input type="text" name="adresse" value="<?= $user['adresse']; ?>">
                            <span class="error"><?php echoError($errors, 'adresse'); ?></span>
                        </div>
                        <div class="form-field-small column">
                            <label for="codepostal">Code postal</label>
                            <input type="text" name="codepostal" value="<?= $user['postal']; ?>">
                            <span class="error"><?php echoError($errors, 'codepostal'); ?></span>
                        </div>
                        <div class="form-field-small column">
                            <label for="ville">Ville</label>
                            <input type="text" name="ville" value="<?= $user['ville']; ?>">
                            <span class="error"><?php echoError($errors, 'ville'); ?></span>
                        </div>
                    </div>
                    <div class="form-field column">
                        <label for="numTelephone">Votre numéro de téléphone</label>
                        <input type="text" name="numTelephone" value="<?= $user['tel']; ?>">
                        <span class="error"><?php echoError($errors, 'numTelephone'); ?></span>
                    </div>

                    <?php
                    if(!$first_edit)
                    {
                        ?>
                        <div class="pwmail sb">
                            <div class="flex column">
                                <h2>Changement de mot de passe</h2>
                                <div class="form-field flex column">
                                    <label for="password_actuel">Votre mot de passe actuel</label>
                                    <input type="password" name="password_actuel" value="">
                                    <span class="error"><?php echoError($errors, 'password_actuel'); ?></span>
                                </div>

                                <div class="form-field flex column">
                                    <label for="password">Votre nouveau mot de passe</label>
                                    <input type="password" name="password" value="">
                                    <span class="error"><?php echoError($errors, 'password'); ?></span>
                                </div>

                                <div class="form-field flex column">
                                    <label for="password_conf">Confirmez votre nouveau mot de passe</label>
                                    <input type="password" name="password_conf" value="">
                                    <span class="error"><?php echoError($errors, 'password_conf'); ?></span>
                                </div>
                            </div>
                            <div class="flex column">
                                <h2>Changement d'adresse mail</h2>
                                <div class="form-field flex column">
                                    <label for="password_actuel_email">Votre mot de passe actuel</label>
                                    <input type="password" name="password_actuel_email" value="">
                                    <span class="error"><?php echoError($errors, 'password_actuel_email'); ?></span>
                                </div>
                                <div class="form-field flex column">
                                    <label for="email">Votre nouvelle adresse mail</label>
                                    <input type="email" name="email" value="<?= $user['email']; ?>">
                                    <span class="error"><?php echoError($errors, 'email'); ?></span>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                    ?>

                    <div class="form-field">
                        <input type="submit" name="submitted" value="Mettre à jour">
                    </div>
                </form>
            </div>
            <div class="edit-detail-img"></div>
        </div>
    </section>

<?php include('inc/footer.php');
