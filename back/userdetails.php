<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$page_title = 'Details utilisateur';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

if(empty($_GET['id']) || !is_numeric($_GET['id'])){
    header('Location: gestion_users.php');
    die();
}

$errors = array();

$userid = $_GET['id'];

if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $userid = $_GET['id'];
    $currentUser = getEntityById('vac_users',$userid);

    if (empty($currentUser)) {
        redirect404();
    }

} else {
    redirect404();
}


if(!empty($_GET['delete_file']) && !empty($_GET['info_id']) && is_numeric($_GET['info_id'])){
    $delete_file = trim(strip_tags($_GET['delete_file']));
    $info_id = intval($_GET['info_id']);

    $sql = "SELECT documents FROM vac_usersvaccins WHERE id = :info_id AND user_id = :user_id";
    $query = $pdo->prepare($sql);
    $query->bindValue(':info_id',$info_id,PDO::PARAM_INT);
    $query->bindValue(':user_id',$userid,PDO::PARAM_INT);
    $query->execute();
    $documents = $query->fetchColumn();

    if(!empty($documents) && mb_strlen($documents) > 0){
        if(str_contains($documents, ',')) {
            $docsArray = explode(',', $documents);
        }
        else{
            $docsArray = array();
            $docsArray[] = $documents;
        }

        for($i = 0; $i < count($docsArray); $i++){
            if($docsArray[$i] === $delete_file){
                if(file_exists('../upload/'.'user_'.$_SESSION['user']['id'].'/info_'.$info_id.'/'.$delete_file)){
                    unlink('../upload/'.'user_'.$_SESSION['user']['id'].'/info_'.$info_id.'/'.$delete_file);
                }
                $docsArray[$i] = '';
            }
        }

        $newDocumentsString = '';
        for($i = 0; $i < count($docsArray); $i++) {
            if(mb_strlen($docsArray[$i]) > 0){
                if(mb_strlen($newDocumentsString) > 0){
                    $newDocumentsString .= ',';
                }
                $newDocumentsString .= $docsArray[$i];
            }
        }

        $sql = "UPDATE vac_usersvaccins SET documents = :documents WHERE id = :info_id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':documents',$newDocumentsString,PDO::PARAM_STR);
        $query->bindValue(':info_id',$info_id,PDO::PARAM_INT);
        $query->execute();
    }

    header('Location: userdetails.php?id='.$userid);
}

include('inc/header.php');
//debug($errors);
//debug($currentUser);
echo '<div class="user_details flex sb">';
    echo '<div class="info_compte">';
        echo '<h2 class="name">'.$currentUser['nom'].' '.$currentUser['prenom'].'</h2>';
        if ($currentUser['role']=='admin' OR $currentUser['role']=='banni') {
            echo '<p style="color: var(--rouge);" class="role">' . $currentUser['role'] . '</p>';
        }
        echo '<p class="email">Adresse mail : '.$currentUser['email'].'</p>';
        echo '<p class="tel">Téléphone : '.$currentUser['tel'].'</p>';
        echo '<p class="email">Adresse email validée : ';
            if ($currentUser['validemail']) {echo 'oui';}
            else {echo 'non';}
        echo '</p>';
        echo '<p class="date">Date d\'inscription : '; dateFormat($currentUser['created_at']);
        echo '</p>';
    echo '</div>';
    echo '<div class="info_perso">';
        echo '<h3> Informations personnelles : </h3>';
        if ($currentUser['age']) {
            echo 'Né(e) le ';
            echo dateFormat($currentUser['naissance'],'d/m/Y').', '.$currentUser['age'].' ans';
        }
        if ($currentUser['sexe'] == 0) {
            echo '<p class="sexe">Sexe : H';}
        elseif ($currentUser['sexe'] == 2) {}
        else {
            echo '<p class="sexe">Sexe : F';}
        echo '</p>';
        if ($currentUser['adresse']) {
            echo '<p class="adresse">Adresse : ' . $currentUser['adresse'] . ', ' . $currentUser['postal'] . ', ' . $currentUser['ville'];
        }
    echo '</div>';
echo '</div>';



$sql = "SELECT COUNT(vaccin_id) FROM vac_usersvaccins WHERE user_id = :userid";
$query = $pdo ->prepare($sql);
$query->bindValue('userid',$currentUser['id'],PDO::PARAM_INT);
$query ->execute();
$vaccins_nb = $query->fetchColumn();


echo '<h3> Vaccins enregistrés par l\'utilisateur (';
echo $vaccins_nb;
echo ') </h3>';

$sql = "SELECT * FROM vac_usersvaccins WHERE user_id = :userid";
$query = $pdo ->prepare($sql);
$query->bindValue(':userid',$currentUser['id'],PDO::PARAM_INT);
$query ->execute();
$vaccins = $query->fetchAll();

foreach ($vaccins as $vaccin) {
    $sql = "SELECT * FROM vac_vaccins WHERE id = :vaccinid";
    $query = $pdo ->prepare($sql);
    $query->bindValue(':vaccinid',$vaccin['vaccin_id'],PDO::PARAM_INT);
    $query ->execute();
    $current_vaccin = $query->fetch();
    echo '<p class="nom_vaccin"><strong>'.$current_vaccin['nom_vaccin'].'</strong></p>';
    $dateDiff = getDateDiffInDays(date('Y-m-d'), $vaccin['date_rappel']);
    if($dateDiff > 0) {
        echo '<p class="rappel_vaccin">Date de rappel : '.$vaccin['date_rappel'].'</p>';
    }
    if ($vaccin['note']) {
        echo '<p class="note">Note de l\'utilisateur : '.$vaccin['note'].'</p>';
    }


    $documents = $vaccin['documents'];
    if(mb_strlen($documents) > 0){
        if(str_contains($documents, ',')) {
            $docsArray = explode(',', $documents);
        }
        else{
            $docsArray = array();
            $docsArray[] = $documents;
        }

        $documentInfo = '';
        foreach ($docsArray as $document) {
            if(file_exists('../upload/user_'.$_SESSION['user']['id'].'/info_'.$vaccin['id'].'/'.$document)){
                if(mb_strlen($documentInfo) > 0){
                    $documentInfo .= '<br>';
                }
                $documentInfo .= '<a target="_blank" href="../upload/user_'.$_SESSION['user']['id'].'/info_'.$vaccin['id'].'/'.$document.'"><i class="fas fa-file"></i> '.str_replace('.pdf', '', $document).'</a> <a href="?delete_file='.$document.'&info_id='.$vaccin['id'].'&id='.$userid.'">Supprimer</a>';
            }
        }
        echo $documentInfo;
    }

}



$sql = "SELECT documents FROM vac_usersvaccins WHERE user_id = :userid";
$query = $pdo ->prepare($sql);
$query->bindValue('userid',$currentUser['id'],PDO::PARAM_INT);
$query ->execute();
$fichiers_nb = $query->fetchAll();
$nbDocuments = 0;
foreach ($fichiers_nb as $fichier_vaccin) {
    $nbDocumentsSingleVax = explode(',', $fichier_vaccin['documents']);
    $nbDocuments += count($nbDocumentsSingleVax);
}

echo '<h3> Documents mis en ligne par l\'utilisateur (';
echo $nbDocuments;
echo ') </h3>';
echo '<br>';


?>




<?php
include('inc/footer.php');

