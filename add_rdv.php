<?php
$page_title = 'Ajouter un RDV';
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$errors = array();

if(!isLoggedIn()){
    header('Location: connexion.php');
    die();
}

if (isLoggedIn()) {

    $sql = "SELECT count(id) FROM vac_users WHERE email = :email LIMIT 1";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
    $query->execute();
    if($query->fetchColumn() == 0){
        header('Location: logout.php');
    }


if (!empty($_POST['submitted'])) {

//    $title = trim(strip_tags($_POST['title']));
//    $content = trim(strip_tags($_POST['content']));
//    $author = trim(strip_tags($_POST['author']));
//    $status = trim(strip_tags($_POST['status']));

    $post = clearXssAll($_POST);

    $errors = selectValidation($errors, $post['date_rdv'], 'date_rdv');
    $errors = textValidation($errors, $post['desc'], 'desc', 3, 300);


//debug($_SESSION);
//debug($post);

    if (count($errors) == 0) {


        $sql = "INSERT INTO vac_rdv (`user_id`, `date_rdv`, `description_rdv`) 
                VALUES (:user, :date_rdv, :desc)";
        $query = $pdo ->prepare($sql);
        $query->bindValue(':date_rdv',$post['date_rdv'],PDO::PARAM_STR);
        $query->bindValue(':desc',$post['desc'],PDO::PARAM_STR);
        $query->bindValue (':user',$_SESSION['user']['id'],PDO::PARAM_INT);

        $query ->execute();
        header('location: rdv.php');
    }
}


include('inc/header.php');
?>
    <div>
        <a class="btn_style_blue" href="rdv.php">Retour sur la liste des rendez-vous</a>
        <div class="separator_invisible"></div>
        <form action="" method="post" class="flex column wrap">



            <label for="date_rdv">Date et heure du rendez-vous :</label>
            <input class="date_rdv" type="datetime-local" name="date_rdv" id="date_rdv" value="<?= recupInputValue('date_rdv'); ?>">
            <span class="error"><?php echoError($errors,'date_rdv'); ?></span>

            <label for="desc">Description du rendez-vous :</label>
            <textarea name="desc" id="desc" cols="30" rows="10"><?= recupInputValue('desc'); ?></textarea>
            <span class="error"><?php echoError($errors,'desc'); ?></span>


            <input class="stylized_blue_input_submit" type="submit" name="submitted" id="submitted" value="Ajouter">
        </form>
    </div>

<?php
include('inc/footer.php');

} else {header('location: connexion.php');};