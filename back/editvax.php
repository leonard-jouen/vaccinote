<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$page_title = 'Editer un vaccin';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

$errors = array();
//$post = array();
// vérifier l'existence et la validité de l'ID (numérique, et créer un $id=$_GET[id]
if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $currentVaccin = getEntityById('vac_vaccins',$id);

    if (!empty($currentVaccin)) {

    //verifier form submitted
    if (!empty($_POST['submitted'])) {
        //faille XSS

        $post = clearXssAll($_POST);



        //validation
        $errors = textValidation($errors, $post['nom_vaccin'], 'nom_vaccin', 3, 200);
        $errors = textValidation($errors, $post['description'], 'description', 10, 2500);
        $errors = textValidation($errors, $post['createur'], 'createur', 3,200);

        if (count($errors) == 0) {

            $sql = "UPDATE vac_vaccins SET nom_vaccin = :nom_vaccin, description = :description, createur = :createur
                    WHERE id =:id";

            $query = $pdo ->prepare($sql);
            $query -> bindValue(':nom_vaccin',$post['nom_vaccin'],PDO::PARAM_STR);
            $query -> bindValue(':description',$post['description'],PDO::PARAM_STR);
            $query -> bindValue(':createur',$post['createur'],PDO::PARAM_STR);
            $query -> bindValue(':id',$id,PDO::PARAM_INT);
            $query -> execute();
//            debug($post);

            header('location: gestion_vaccins.php'  );
        }

    }
    } else {redirect403(); }

} else {
    redirect403();
}

include('inc/header.php'); ?>



<div>
    <form action="" method="post" class="flex column wrap">
        <label for="nom_vaccin">Nom du vaccin :</label>
        <input type="text" name="nom_vaccin" id="nom_vaccin" value="<?php  verifyUpdate($errors,'nom_vaccin',$currentVaccin)  ?>">
        <span class="error"><?php echoError($errors,'nom_vaccin'); ?></span>

        <label for="content">Description du vaccin :</label>
        <textarea rows="7" type="text" name="description" id="description"><?php verifyUpdate($errors,'description',$currentVaccin) ?></textarea>
        <span class="error"><?php echoError($errors,'description'); ?></span>

        <label for="author">Créateur :</label>
        <input type="text" name="createur" id="createur" value="<?php verifyUpdate($errors,'createur',$currentVaccin) ?>">
        <span class="error"><?php echoError($errors,'createur'); ?></span>




        <input class="stylized_blue_input_submit" type="submit" name="submitted" id="submitted" value="Modifier">
    </form>
</div>

<?php
include('inc/footer.php');
