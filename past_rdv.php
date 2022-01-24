<?php
session_start();
$page_title = 'Vos RDV passés';

require('./inc/pdo.php');
require('./inc/fonctions.php');

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

$sql = "SELECT * FROM vac_rdv WHERE `user_id` = :userid AND `status` = 'passed'";
$query = $pdo->prepare($sql);
$query->bindValue(':userid',$_SESSION['user']['id'],PDO::PARAM_STR);
$query->execute();
$rdvs = $query->fetchAll();

include('inc/header.php'); ?>

    <section id="carnet_vaccinal">
        <div class="wrap">
            <div class="title">
                <br><h2>Rendez-vous passés</h2>
                <div class="separator"></div>
                <p>Nombre de rendez-vous passés : <?php echo count($rdvs); ?></p>
                <div style="padding-top: 2rem;">
                    <a class="btn_style_blue" href="rdv.php">Revenir a la page rendez-vous</a><br><br>
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

                             ?>
                                <td><?php echo getFormattedDate($rdv['date_rdv']); ?></td>




                            <td><?php echo $rdv['description_rdv']; ?></td>

                            <td>
                                <?php
                                echo ' <a class="supp_past_rdv" href="?delete_rdv='.$rdv['id'].'">Supprimer le rdv</a>';
                                ?>
                            </td>
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
