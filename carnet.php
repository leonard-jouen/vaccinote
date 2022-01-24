<?php
session_start();

require('./inc/pdo.php');
require('./inc/fonctions.php');
$page_title = 'Carnet vaccinal';


if(!isLoggedIn()){
    header('Location: ./connexion.php');
    die();
}

$sql = "SELECT count(id) FROM vac_users WHERE email = :email";
$query = $pdo->prepare($sql);
$query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
$query->execute();
if($query->fetchColumn() == 0){
    header('Location: logout.php');
}

$errors = array();
if(!empty($_POST['submitted']) && !empty($_POST['info_id']) && is_numeric($_POST['info_id'])){

    $info_id = trim(strip_tags($_POST['info_id']));

    $sql = "SELECT documents FROM vac_usersvaccins WHERE user_id = :user_id AND id = :info_id";
    $query = $pdo->prepare($sql);
    $query->bindValue(':user_id',$_SESSION['user']['id'],PDO::PARAM_INT);
    $query->bindValue(':info_id',$info_id,PDO::PARAM_INT);
    $query->execute();
    if($query->rowCount() == 0){
        redirect404();
        die();
    }
    $documents = $query->fetchColumn();

    $nbDocuments = explode(',', $documents);
    if(count($nbDocuments) >= 5){
        $errors[$info_id]['document'] = 'Vous avez atteint la limite de 5 documents par vaccin';
    }
    else {

        if ($_FILES['document']['error'] > 0) {
            if ($_FILES['document']['error'] != 4) {
                $errors[$info_id]['document'] = 'Une erreur inconnue s\'est produite';
            } else {
                $errors[$info_id]['document'] = 'Veuillez joindre un fichier';
            }
        } else {
            // tout est OK
            $fileName = $_FILES['document']['name'];
            $fileSize = $_FILES['document']['size'];
            $fileTmpName = $_FILES['document']['tmp_name'];
            $fileType = $_FILES['document']['type'];

            // Taille du fichier
            $sizeMax = 2000000; // 2Mo
            if ($fileSize > $sizeMax || filesize($fileTmpName) > $sizeMax) {
                $errors[$info_id]['document'] = 'Le fichier est trop volumineux (2Mo max)';
            } else {
                // Type du fichier
                $goodMimeType = array('application/pdf');
                $fInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($fInfo, $fileTmpName);
                finfo_close($fInfo);
                if (!in_array($mime, $goodMimeType)) {
                    $errors[$info_id]['document'] = 'Veuillez charger un document de type PDF';
                } else {
                    // Upload

                    // extension
                    $i_point = strpos($fileName, '.');
                    $extension = substr($fileName, $i_point, strlen($fileName) - $i_point);
                    $newFileName = trim(strip_tags(str_replace($extension, '', $fileName))) . $extension;

                    if (!is_dir('upload')) {
                        mkdir('upload');
                    }

                    if (!is_dir('upload/user_' . $_SESSION['user']['id'])) {
                        mkdir('upload/user_' . $_SESSION['user']['id']);
                    }

                    if (!is_dir('upload/user_' . $_SESSION['user']['id'] . '/info_' . $info_id)) {
                        mkdir('upload/user_' . $_SESSION['user']['id'] . '/info_' . $info_id);
                    }

                    if (file_exists('upload/' . 'user_' . $_SESSION['user']['id'] . '/info_' . $info_id . '/' . $newFileName)) {
                        $errors[$info_id]['document'] = 'Vous avez déjà ajouté un document avec un nom identique';
                        unlink($fileTmpName);
                    } else {
                        if (move_uploaded_file($fileTmpName, 'upload/' . 'user_' . $_SESSION['user']['id'] . '/info_' . $info_id . '/' . $newFileName)) {

                            if (mb_strlen($documents) > 0) {
                                $documents .= ',';
                            }
                            $documents .= $newFileName;
                            $sql = "UPDATE vac_usersvaccins SET documents = :documents WHERE id = :info_id";
                            $query = $pdo->prepare($sql);
                            $query->bindValue(':documents', $documents, PDO::PARAM_STR);
                            $query->bindValue(':info_id', $info_id, PDO::PARAM_INT);
                            $query->execute();
                            header('Location: carnet.php#info_' . $info_id);
                        } else {
                            $errors[$info_id]['document'] = 'Une erreur s\'est produite lors de l\'envoi';
                        }
                    }
                }
            }
        }
    }
}

if(!empty($_GET['delete_file']) && !empty($_GET['info_id']) && is_numeric($_GET['info_id'])){
    $delete_file = trim(strip_tags($_GET['delete_file']));
    $info_id = intval($_GET['info_id']);

    $sql = "SELECT documents FROM vac_usersvaccins WHERE id = :info_id AND user_id = :user_id";
    $query = $pdo->prepare($sql);
    $query->bindValue(':info_id',$info_id,PDO::PARAM_INT);
    $query->bindValue(':user_id',$_SESSION['user']['id'],PDO::PARAM_INT);
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
                if(file_exists('upload/'.'user_'.$_SESSION['user']['id'].'/info_'.$info_id.'/'.$delete_file)){
                    unlink('upload/'.'user_'.$_SESSION['user']['id'].'/info_'.$info_id.'/'.$delete_file);
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

    header('Location: carnet.php#info_'.$info_id);
}

if(!empty($_GET['delete_rappel']) && is_numeric($_GET['delete_rappel'])){
    $delete_rappel = intval($_GET['delete_rappel']);
    $sql = "UPDATE vac_usersvaccins SET date_rappel = '' WHERE id = :rappelid AND user_id = :userid";
    $query = $pdo->prepare($sql);
    $query->bindValue(':rappelid',$delete_rappel,PDO::PARAM_STR);
    $query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
    $query->execute();
    header('Location: carnet.php#info_'.$delete_rappel);
}

if(!empty($_GET['delete_note']) && is_numeric($_GET['delete_note'])){
    $delete_note = intval($_GET['delete_note']);
    $sql = "UPDATE vac_usersvaccins SET note = '' WHERE id = :rappelid AND user_id = :userid";
    $query = $pdo->prepare($sql);
    $query->bindValue(':rappelid',$delete_note,PDO::PARAM_STR);
    $query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
    $query->execute();
    header('Location: carnet.php#info_'.$delete_note);
}

if(!empty($_GET['delete_vax']) && is_numeric($_GET['delete_vax'])){
    $delete_vax = intval($_GET['delete_vax']);
    $sql = "DELETE FROM vac_usersvaccins WHERE id = :vaccinid AND user_id = :userid";
    $query = $pdo->prepare($sql);
    $query->bindValue(':vaccinid',$delete_vax,PDO::PARAM_STR);
    $query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
    $query->execute();
    header('Location: carnet.php#info_'.$delete_vax);
}

$sql = "SELECT v.id AS vaccin_id, uv.id AS uservaccin_id, v.nom_vaccin, uv.date_rappel, uv.date_vaccination, uv.note, uv.documents FROM vac_usersvaccins AS uv
                        LEFT JOIN vac_vaccins AS v ON v.id = uv.vaccin_id WHERE uv.user_id = :userid";
$query = $pdo->prepare($sql);
$query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
$query->execute();
$vaccins = $query->fetchAll();

include('inc/header.php'); ?>

    <section id="carnet_vaccinal">
        <div class="wrap">
            <div class="title">
                <br><h2>Vos vaccins</h2>
                <div class="separator"></div>
                <p>Nombre de vaccin(s): <?php echo count($vaccins); ?></p><br>
            </div>
            <div class="carnet">
                <div class="bouton_carnet">
                    <a href="add_vaccin.php">Ajouter un vaccin</a>
                </div>

                <table>
                    <thead>
                    <tr>
                        <th>Nom du vaccin</th>
                        <th>Date de vaccination</th>
                        <th>Prochain rappel</th>
                        <th>Note</th>
                        <th>Documents annexes</th>
                        <th>Actions</th>
                    </tr>
                    <div class="table_separator"></div>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($vaccins as $vaccin){
                        ?>
                        <tr id="info_<?= $vaccin['uservaccin_id']; ?>">
                            <td><?php echo $vaccin['nom_vaccin']; ?></td>
                            <td><?php echo getFormattedDate($vaccin['date_vaccination']); ?></td>

                            <?php

                            $aJour = false;
                            $rdvPrevu = false;
                            $aucunRappel = false;
                            $dateRdv = '';

                            if(empty($vaccin['date_rappel'])){
                                $rappelAPrevoir = true;
                            }
                            else{
                                $dateDiff = getDateDiffInDays(date('Y-m-d'), $vaccin['date_rappel']);
                                if($dateDiff > 0){
                                    $rdvPrevu = true;
                                    if($dateDiff >= 365){
                                        if(round($dateDiff / 365) == 1){
                                            $dateRdv = 'dans <strong>'.round($dateDiff / 365).'</strong> an ('.getFormattedDate($vaccin['date_rappel']).')';
                                        }
                                        else{
                                            $dateRdv = 'dans <strong>'.round($dateDiff / 365).'</strong> ans ('.getFormattedDate($vaccin['date_rappel']).')';
                                        }
                                    }
                                    elseif($dateDiff >= 31){
                                        $dateRdv = 'dans <strong>'.round($dateDiff / 31).'</strong> mois ('.getFormattedDate($vaccin['date_rappel']).')';
                                    }
                                    else{
                                        if($dateDiff > 1){
                                            $dateRdv = 'dans <strong>'.$dateDiff.'</strong> jours ('.getFormattedDate($vaccin['date_rappel']).')';
                                        }
                                        elseif($dateDiff == 1){
                                            $dateRdv = 'demain';
                                        }
                                    }
                                }
                                else{
                                    $aJour = true;
                                }
                            }

                            if($rdvPrevu){
                                $status = $dateRdv;
                            }
                            else if($aJour){
                                $status = 'à jour <i style="color: green;" class="fas fa-check"></i>';
                            }
                            else{
                                $status = 'aucun rappel <i style="color: green;" class="fas fa-check"></i>';
                                $aucunRappel = true;
                            }
                            ?>

                            <td><?php echo $status; ?></td>

                            <td><?php echo $vaccin['note']; ?></td>
                            
                            <td>
                                <?php
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
                                        if(file_exists('./upload/user_'.$_SESSION['user']['id'].'/info_'.$vaccin['uservaccin_id'].'/'.$document)){
                                            if(mb_strlen($documentInfo) > 0){
                                                $documentInfo .= '<br>';
                                            }
                                            $documentInfo .= '<a target="_blank" href="./upload/user_'.$_SESSION['user']['id'].'/info_'.$vaccin['uservaccin_id'].'/'.$document.'"><i class="fas fa-file"></i> '.str_replace('.pdf', '', $document).'</a> <a class="bouton_sup"  href="?delete_file='.$document.'&info_id='.$vaccin['uservaccin_id'].'"><br>Supprimer</a>';
                                        }
                                    }

                                    echo $documentInfo;
                                }
                                ?>
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="file" name="document" id="document">
                                    <span class="error"><?php if(!empty($errors[$vaccin['uservaccin_id']]['document'])){ echo $errors[$vaccin['uservaccin_id']]['document']; } ?></span>
                                    <input type="hidden" name="info_id" id="info_id" value="<?= $vaccin['uservaccin_id']; ?>">
                                    <input class="ajouter" type="submit" name="submitted" value="Ajouter (.pdf)">
                                </form>
                            </td>

                            <td>
                                <?php
                                if($aucunRappel || $aJour){
                                    echo '<a class="first_child" href="add_rappel.php?id='.$vaccin['uservaccin_id'].'">Ajouter un rappel</a>';
                                }
                                if($rdvPrevu){
                                    echo '<a class="first_child2" href="?delete_rappel='.$vaccin['uservaccin_id'].'">Annuler le rappel</a>';
                                }
                                if(!empty($vaccin['note'])){
                                    echo ' <a class="first_child2" href="?delete_note='.$vaccin['uservaccin_id'].'">Supprimer la note</a>';
                                }
                                else{
                                    echo ' <a class="first_child" href="add_note.php?id='.$vaccin['uservaccin_id'].'">Ajouter une note</a>';
                                }

                                ?>
                                <a onclick="return confirm('Voulez vous vraiment supprimer ce vaccin?')" class="first_child2" href="?delete_vax=<?= $vaccin['uservaccin_id']?>" >Supprimer le vaccin</a>
                            </td>
                            <div class="table_separator"></div>
                        </tr>

                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

<?php include('inc/footer.php');
