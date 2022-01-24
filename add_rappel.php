<?php
$page_title = 'Ajouter un rappel';
session_start();
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

$sql = "SELECT user_id FROM vac_usersvaccins WHERE id = :user" ;
$query = $pdo ->prepare($sql);
$query->bindValue(':user',$_GET['id'],PDO::PARAM_STR);
$query ->execute();
$user_id = $query ->fetchColumn();



if ($user_id == $_SESSION['user']['id']) {


    if (!empty($_POST['submitted'])) {



        $post = clearXssAll($_POST);

        $errors = selectValidation($errors, $post['date_rappel'], 'date_rappel');


        if (count($errors) == 0) {


            $sql = "UPDATE vac_usersvaccins SET date_rappel = :date_rappel WHERE id = :user";
            $query = $pdo ->prepare($sql);
            $query->bindValue(':date_rappel',$post['date_rappel'],PDO::PARAM_STR);
            $query->bindValue (':user',$_GET['id'],PDO::PARAM_STR);

            $query ->execute();
            header('location: carnet.php');

        }
    }


    include('inc/header.php');
    ?>
    <div id="add_rappel">
        <a class="btn_style_blue" href="carnet.php">Retour sur le carnet</a>
        <div class="separator_invisible"></div>
        <form action="" method="post" class="flex column wrap">

            <label for="date_rappel">Date de rappel :</label>
            <input type="date" name="date_rappel" id="date_rappel" value="<?= recupInputValue('date_rappel'); ?>">
            <span class="error"><?php echoError($errors,'date_rappel'); ?></span>


            <input class="stylized_blue_input_submit" type="submit" name="submitted" id="submitted" value="Ajouter le rappel">
        </form>
    </div>

    <?php
    include('inc/footer.php');

} else {
    redirect404();
}
