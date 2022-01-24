<?php
session_start();
$page_title = 'Ajouter une note';
require('inc/fonctions.php');
require('inc/pdo.php');
$errors = array();

if(!isLoggedIn()){
    header('Location: connexion.php');
    die();
}

if(empty($_GET['id']) || !is_numeric($_GET['id'])){
    header('Location: carnet.php');
}

$sql = "SELECT count(id) FROM vac_users WHERE email = :email LIMIT 1";
$query = $pdo->prepare($sql);
$query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
$query->execute();
if($query->fetchColumn() == 0){
    header('Location: logout.php');
}

if(!empty($_GET['id']) && is_numeric($_GET['id'])){
    $sql = "SELECT user_id FROM vac_usersvaccins WHERE id = :user" ;
    $query = $pdo ->prepare($sql);
    $query->bindValue(':user',$_GET['id'],PDO::PARAM_STR);
    $query ->execute();
    $user_id = $query ->fetchColumn();

    $sql = "SELECT note FROM vac_usersvaccins WHERE id = :user" ;
    $query = $pdo ->prepare($sql);
    $query->bindValue(':user',$_GET['id'],PDO::PARAM_STR);
    $query ->execute();
    $old_note = $query ->fetchColumn();
}

if ($user_id == $_SESSION['user']['id']) {



    if (!empty($_POST['submitted'])) {

        $post = clearXssAll($_POST);

        $errors = textValidation($errors, $post['note'], 'note', 1, 250);


        if (count($errors) == 0) {


            $sql = "UPDATE vac_usersvaccins SET note = :note WHERE id = :user";
            $query = $pdo ->prepare($sql);
            $query->bindValue(':note',$post['note'],PDO::PARAM_STR);
            $query->bindValue (':user',$_GET['id'],PDO::PARAM_STR);

            $query ->execute();
            header('location: carnet.php');

        }
    }


    include('inc/header.php');
    ?>

    <div id="add_note">
        <a class="btn_style_blue" href="carnet.php">Retour sur le carnet</a>
        <div class="separator_invisible"></div>

        <form action="" method="post" class="flex column wrap">
            <label for="note">Note personnelle :</label>
            <textarea name="note" id="note" cols="30" rows="10"><?php if (!empty($old_note)&&empty($_POST['note'])) {echo $old_note;} else { echo recupInputValue('note'); }?></textarea>
            <span class="error"><?php echoError($errors,'note'); ?></span>
            <input class="stylized_blue_input_submit" type="submit" name="submitted" id="submitted" value="Ajouter la note">

        </form>
    </div>

    <?php
    include('inc/footer.php');

} else {
    redirect404();
}
