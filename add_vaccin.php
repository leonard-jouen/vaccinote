<?php
$page_title = 'Ajouter un vaccin';
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$errors = array();

if(!isLoggedIn()){
    header('Location: connexion.php');
    die();
}

$sql = "SELECT count(id) FROM vac_users WHERE email = :email LIMIT 1";
$query = $pdo->prepare($sql);
$query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
$query->execute();
if($query->fetchColumn() == 0){
    header('Location: logout.php');
}

$sql = "SELECT * FROM vac_vaccins";
$query = $pdo ->prepare($sql);
$query ->execute();
$vaccins = $query->fetchAll();

if (!empty($_POST['submitted'])) {

//    $title = trim(strip_tags($_POST['title']));
//    $content = trim(strip_tags($_POST['content']));
//    $author = trim(strip_tags($_POST['author']));
//    $status = trim(strip_tags($_POST['status']));

    $post = clearXssAll($_POST);

//    $errors = textValidation($errors, $post['date_vaccin'], 'title', 3, 250);
    $errors = selectValidation($errors, $post['date_vaccin'], 'date_vaccin');
    $errors = selectValidation($errors, $post['vaccin'], 'vaccin');




    if (count($errors) == 0) {
        $sql = "SELECT id FROM vac_vaccins WHERE nom_vaccin = :vaccin" ;
        $query = $pdo ->prepare($sql);
        $query->bindValue(':vaccin',$post['vaccin'],PDO::PARAM_STR);
        $query ->execute();
        $vaccin_id = $query ->fetchColumn();
//        debug($vaccin_id);

        $sql = "INSERT INTO vac_usersvaccins (`user_id`, `vaccin_id`,`date_vaccination`,`note`,`date_rappel`,`created_at`) 
                VALUES (:user,:vaccin,:date_vaccin,:note,:date_rappel,NOW())";
        $query = $pdo ->prepare($sql);
        $query->bindValue(':date_vaccin',$post['date_vaccin'],PDO::PARAM_STR);
        $query->bindValue(':date_rappel',$post['date_rappel'],PDO::PARAM_STR);
        $query->bindValue(':note',$post['note'],PDO::PARAM_STR);
        $query->bindValue(':vaccin',$vaccin_id,PDO::PARAM_STR);
        $query->bindValue (':user',$_SESSION['user']['id'],PDO::PARAM_STR);

        $query ->execute();
        header('location: carnet.php');

    }
}


include('inc/header.php');
?>
   <section id="add_vaccin">
    <div>
        <form action="" method="post" class="flex column wrap">

            <label class="padding" for="vaccin">Vaccin :</label>
            <select class="add" name="vaccin" id="vaccin">
                <option value="">__sélectionnez__</option>
                <?php foreach ($vaccins as $vaccin) { ?>
                    <?php if(!empty($_POST['vaccin']) && $_POST['vaccin'] == $vaccin['nom_vaccin'] ) { ?>
                        <option value="<?php echo $vaccin['nom_vaccin']; ?>" selected><?php echo $vaccin['nom_vaccin']; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $vaccin['nom_vaccin']; ?>"><?php echo $vaccin['nom_vaccin']; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <span class="error"><?php echoError($errors,'vaccin'); ?></span>

            <label class="padding" for="date_vaccin">Date de vaccination :</label>
            <input class="add" type="date" name="date_vaccin" id="date_vaccin" value="<?= recupInputValue('date_vaccin'); ?>">
            <span class="error"><?php echoError($errors,'date_vaccin'); ?></span>

            <label class="padding" for="date_rappel">Date de rappel (optionnel) :</label>
            <input class="add" type="date" name="date_rappel" id="date_rappel" value="<?= recupInputValue('date_rappel'); ?>">
            <span class="error"><?php echoError($errors,'date_rappel'); ?></span>

            <label class="padding" for="note">Note personnelle (optionnel) :</label>
            <textarea name="note" id="note" cols="30" rows="10"><?= recupInputValue('note'); ?></textarea>

            <div class="add_input_envoyer">
             <input class="input_envoyer" type="submit" name="submitted" id="submitted" value="Ajouter mon vaccin">
            </div>
        </form>
    </div>
   </section>

<?php
include('inc/footer.php');

