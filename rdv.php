<?php
session_start();

require('./inc/pdo.php');
require('./inc/fonctions.php');
$page_title = 'Vos rendez-vous';


if(!isLoggedIn()){
    header('Location: ./connexion.php');
    die();
}

$sql = "SELECT count(id) FROM vac_users WHERE email = :email LIMIT 1";
$query = $pdo->prepare($sql);
$query->bindValue(':email',$_SESSION['user']['email'],PDO::PARAM_STR);
$query->execute();
if($query->fetchColumn() == 0){
    header('Location: logout.php');
}


if(!empty($_GET['delete_rdv']) && is_numeric($_GET['delete_rdv'])){
    $delete_rdv = intval($_GET['delete_rdv']);
    $sql = "DELETE FROM vac_rdv  WHERE id = :rdvid AND user_id = :userid";
    $query = $pdo->prepare($sql);
    $query->bindValue(':rdvid',$delete_rdv,PDO::PARAM_STR);
    $query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
    $query->execute();
    header('Location: rdv.php');
}
if(!empty($_GET['end_rdv']) && is_numeric($_GET['end_rdv'])){
    $end_rdv = intval($_GET['end_rdv']);
    $sql = "UPDATE vac_rdv SET `status` = 'passed' WHERE id = :rdvid AND user_id = :userid";
    $query = $pdo->prepare($sql);
    $query->bindValue(':rdvid',$end_rdv,PDO::PARAM_STR);
    $query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
    $query->execute();
    header('Location: rdv.php');
}

$sql = "SELECT * FROM vac_rdv WHERE `user_id` = :userid AND `status` = 'pending'";
$query = $pdo->prepare($sql);
$query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
$query->execute();
$rdvs = $query->fetchAll();

include('inc/header.php'); ?>

    <section id="carnet_vaccinal">
        <div class="wrap">
            <div class="title">
                <br><h2>Rendez-vous</h2>
                <div class="separator"></div>
                <p>Nombre de rendez-vous : <?php echo count($rdvs); ?></p>
                <div style="padding-top: 2rem;">
                    <a class="btn_style_blue" href="add_rdv.php">Ajouter un rendez-vous</a><br><br>
                </div>
            </div>
            <div class="carnet">
                <table>
                    <thead>
                    <tr>
                        <th>Date de rendez-vous</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($rdvs as $rdv){
                        ?>
                        <tr>

                            <?php
                            $dateRdv = '';

                            $dateDiff = getDateDiffInDays(date('Y-m-d'), $rdv['date_rdv']);
                            if($dateDiff > 0) {
                                if ($dateDiff >= 365) {
                                    if (round($dateDiff / 365) == 1) {
                                        $dateRdv = 'dans <strong>' . round($dateDiff / 365) . '</strong> an (' . getFormattedDate($rdv['date_rdv']) . ')';
                                    } else {
                                        $dateRdv = 'dans <strong>' . round($dateDiff / 365) . '</strong> ans (' . getFormattedDate($rdv['date_rdv']) . ')';
                                    }
                                } elseif ($dateDiff >= 31) {
                                    $dateRdv = 'dans <strong>' . round($dateDiff / 31) . '</strong> mois (' . getFormattedDate($rdv['date_rdv']) . ')';
                                } else {
                                    if ($dateDiff > 1) {
                                        $dateRdv = 'dans <strong>' . $dateDiff . '</strong> jours (' . getFormattedDate($rdv['date_rdv']) . ')';
                                    } elseif ($dateDiff == 1) {
                                        $dateRdv = 'demain';
                                    }
                                }
                            }
                             if($dateDiff > 0) { ?>
                                <td><?php echo getFormattedDate($rdv['date_rdv'], true); ?></td>
                            <?php } else {echo '<td>Rendez-vous passé</td>';};




                            ?>


                            <td><?php echo $rdv['description_rdv']; ?></td>

                            <td>
                                <?php
                                echo ' <a class="btn_supp_rdv" href="?delete_rdv='.$rdv['id'].'">Supprimer le rdv</a><br>';
                                if($dateDiff <= 0) {
                                    echo ' <a class="btn_term_rdv" href="?end_rdv=' . $rdv['id'] . '">Déclarer le rdv terminé</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table><br>
                <a class="btn_style_blue" href="past_rdv.php">Voir mes rendez-vous passés</a><br><br><br>
            </div>
        </div>
    </section>

<?php include('inc/footer.php');
