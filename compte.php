<?php
session_start();
require('inc/pdo.php');
require('inc/fonctions.php');
$page_title = 'Mon compte';


if (!empty($_GET['success'])) {
$_GET['menu']='';}

if(!isLoggedIn()){
    header('Location: connexion.php');
    die();
}

$success = false;
if(!empty($_GET['success']) && is_numeric($_GET['success']) && intval($_GET['success']) == 1){
    $success = true;
}

$sql = "SELECT * FROM vac_users WHERE id = :userid";
$query = $pdo ->prepare($sql);
$query->bindValue(':userid', $_SESSION['user']['id'], PDO::PARAM_INT);
$query->execute();
$user = $query->fetch();

if(empty($user)){
    header('Location: logout.php');
}

$sql = "SELECT count(id) FROM vac_rdv WHERE user_id = :userid AND status = 'pending'";
$query = $pdo ->prepare($sql);
$query->bindValue(':userid', $_SESSION['user']['id'], PDO::PARAM_INT);
$query->execute();
$rdv_Count = $query->fetchColumn();

$sql = "SELECT count(id) FROM vac_usersvaccins WHERE user_id = :userid";
$query = $pdo ->prepare($sql);
$query->bindValue(':userid', $_SESSION['user']['id'], PDO::PARAM_INT);
$query->execute();
$vac_Count = $query->fetchColumn();

include('inc/header.php'); ?>

    <section id="mon-compte-links">
        <div class="wrap">
            <div class="titre">
                <p>Mon compte</p>
                <div class="separator"></div>
            </div>
            <p style = "color: green; text-align: center; padding: 1rem;"><?php
                if($success){
                    echo 'Vos données personnelles ont bien été mises à jour';
                }
                ?></p>
            <div class="nav">
                <nav>
                    <ul>
                        <a href="contact.php">
                            <li>
                                <?php if (empty($_GET) OR $_GET['menu']==0 OR empty($_GET['menu'])) {;
                                    ?>
                                <i class="fas fa-info-circle"></i>

                                <p>Consulter ou modifier mes informations</p>
                                <?php
                                 } ?>
                            </li>
                        </a>
                        <a href="rdv.php">
                            <li>
                                <?php if (empty($_GET) OR $_GET['menu']==0 OR empty($_GET['menu'])) {; ?>
                                <i class="fas fa-calendar-week">
                                    <?php

                                    if($rdv_Count > 0 ){
                                        echo '<span>'.$rdv_Count.'</span>';
                                    }

                                    } ?>
                                </i>

                                <p>Gérer mes rendez-vous</p>
                            </li>
                        </a>
                        <a href="carnet.php">
                            <li>
                                <?php if (empty($_GET) OR $_GET['menu']==0 OR empty($_GET['menu'])) {; ?>
                                <i class="fas fa-book">
                                    <?php
                                    if($vac_Count > 0 && (empty($_GET['menu']) || intval($_GET['menu']) == 0)){
                                        echo '<span>'.$vac_Count.'</span>';
                                    }
                                    } ?>
                                </i>

                                <p>Mon carnet médical</p>
                            </li>
                        </a>
                        <?php
                        if(isLoggedAsAdmin()){
                            ?>
                            <a class="admin" href="./back/index_back.php">
                                <li>
                                <?php if (empty($_GET) OR $_GET['menu']==0 OR empty($_GET['menu'])) {; ?>
                                    <i class="fas fa-hammer"></i>
                                    <p>Espace admin</p>
                                </li>
                                <?php } ?>
                            </a>
                                <?php
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </section>

    <section id="mon-compte-infos">
        <div class="wrap-moncompte">
            <div class="informations">
                <div class="main-title">
                    <h2>Informations personnelles</h2>
                </div>
                <div class="profil">
                    <div class="title">
                        <h2>Profil</h2>
                    </div>
                    <div class="contenu">
                        <div class="little_field">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" disabled value="<?php echo $user['nom']; ?>">
                        </div>
                        <div class="little_field">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" disabled value="<?php echo $user['prenom']; ?>">
                        </div>
                        <div class="little_field">
                            <label for="born">Date de naissance</label>
                            <input type="date" id="born" name="born" disabled value="<?php echo $user['naissance']; ?>">
                        </div>
                        <div class="little_field">
                            <label for="email">Adresse mail</label>
                            <input type="email" id="email" name="email" disabled value="<?php echo $user['email']; ?>">
                        </div>
                    </div>
                </div>
                <div class="donnees-contact">
                    <div class="title">
                        <h2>Données de contact</h2>
                    </div>
                    <div class="contenu">
                        <div class="big_field">
                            <label for="adresse">Adresse</label>
                            <textarea type="text" id="adresse" name="adresse" disabled><?php echo $user['adresse']; ?></textarea>
                        </div>
                        <div class="complement_adresse">
                            <div class="little_field">
                                <label for="postale">Code postal</label>
                                <input type="postale" id="postale" name="postale" disabled value="<?php echo $user['postal']; ?>">
                            </div>

                            <div class="little_field">
                                <label for="number">Numéro de téléphone</label>
                                <input type="text" id="number" name="number" disabled value="<?php echo $user['tel']; ?>">
                            </div>
                        </div>
                        <div>
                            <div class="little_field">
                                <label for="ville">Ville</label>
                                <input type="ville" id="ville" name="ville" disabled value="<?php echo $user['ville']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modifier">
                        <a class="modifier_btn" href="edit_details.php">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include('inc/footer.php');
