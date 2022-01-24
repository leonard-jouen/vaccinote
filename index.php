
<?php
$page_title = 'Accueil de Vaccinote';

require('./inc/pdo.php');
require('./inc/fonctions.php');
session_start();
//debug($_SESSION['user']);
if (isLoggedIn()) {
    $sql = "SELECT COUNT(*) FROM vac_usersvaccins WHERE `user_id` = :userid";
    $query = $pdo->prepare($sql);
    $query->bindValue(':userid', $_SESSION['user']['id'], PDO::PARAM_INT);
    $query->execute();
    $vaccin_user_nb = $query->fetchColumn();

//debug($vaccin_user_nb);
    if (!$vaccin_user_nb == 0) {
        $sql = "SELECT date_rappel FROM vac_usersvaccins WHERE `user_id` = :userid AND date_rappel > NOW() ORDER BY date_rappel ASC LIMIT 1";
        $query = $pdo->prepare($sql);
        $query->bindValue(':userid', $_SESSION['user']['id'], PDO::PARAM_INT);
        $query->execute();
        $prochain_rappel = $query->fetchColumn();
    }
}
include('inc/header.php');


        if (isLoggedIn() && (empty($_GET) OR $_GET['menu']==0)) {

            if ($vaccin_user_nb==0) {
                echo '<section id="loggedin">';
                    echo '<p class="big_loggedin">Aucun vaccin sur votre profil</p>';
                    echo '<p class="small_loggedin">N\'oubliez pas d\'ajouter vos vaccins effectués en <a href="add_vaccin.php">cliquant ici</a></p>';
                echo '</section>';
            } elseif (!empty($prochain_rappel)) {
                $dateDiff = getDateDiffInDays(date('Y-m-d'), $prochain_rappel);

                echo '<section id="loggedinRappel" class="relative">';
                    echo '<p class="big_loggedin">Prochain rappel :</p>';
                    echo '<p class="small_loggedin">';
                    if($dateDiff >= 365){
                    if(round($dateDiff / 365) == 1){
                        $dateRdv = 'dans <strong>'.round($dateDiff / 365).'</strong> an ('.getFormattedDate($prochain_rappel).')';
                    }
                    else{
                        $dateRdv = 'dans <strong>'.round($dateDiff / 365).'</strong> ans ('.getFormattedDate($prochain_rappel).')';
                    }
                }
                elseif($dateDiff >= 31){
                    $dateRdv = 'dans <strong>'.round($dateDiff / 31).'</strong> mois ('.getFormattedDate($prochain_rappel).')';
                }
                else{
                    if($dateDiff > 1){
                        $dateRdv = 'dans <strong>'.$dateDiff.'</strong> jours ('.getFormattedDate($prochain_rappel).')';
                    }
                    elseif($dateDiff == 1){
                        $dateRdv = 'demain';
                    }
                }
                echo $dateRdv.'</p>';
                echo '<div class="index_calendar absolute">';
                echo '<i class="far fa-calendar-alt "></i>';
                echo '</div>';

                echo '</section>';
            }
        } ?>
    <section id="presentation">
        <div class="presentation_background">
            <div class="presentation_text">
                <h2>Carnet de vaccination</h2>
                <p>Renseignez votre carnet de vaccination et obtenez un suivi complet</p>
            </div>
            <div class="presentation_lien">
                <a href="carnet.php">Accéder à mon carnet</a>
                <a href="rdv.php">Gérer mes rendez-vous</a>
                <a href="infos.php">Centre d'information vaccinal</a>
            </div>
        </div>
    </section>
    <section id="services">
        <div class="titre">
            <h3>Tous nos services</h3>
            <div class="separator"></div>
        </div>
        <div class="services_bloc flex sb">
            <div class="service_bloc1">
                <div class="flex service_titre">
                    <i class="fas fa-phone"></i>
                    <h3>Prise de rendez-vous</h3>
                </div>
                <p>Notez et gardez un suivi de vos rendez vous sans prise de tête en remplissant un formulaire.</p>
            </div>
            <div class="service_bloc2">
                <div class="flex service_titre">
                    <i class="fas fa-bell"></i>
                    <h3>Rappels</h3>
                </div>
                <p>Un outil simple pour garder en mémoire vos dates de rappels pour tous vos vaccins!</p>
            </div>
            <div class="service_bloc3">
                <div class="flex service_titre">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Historique</h3>
                </div>
                <p>Remplissez votre historique vaccinal et gardez en facilement une trace !</p>
            </div>
            <div class="service_bloc4">
                <div class="flex service_titre">
                    <i class="fas fa-map"></i>
                    <h3>Centre d'informations</h3>
                </div>
                <p>Besoin d’informations à propos d’un vaccin? consultez notre page d’aide catégorisant les caractéristiques principales de chaque vaccin!</p>
            </div>
        </div>
    </section>
    <section id="compteur">
        <div class="compteur_background">
            <div class="compeur_maj flex">
                <div>
                    <?php $sql = "SELECT COUNT(*) FROM vac_vaccins WHERE status = 'disponible'";
                    $query = $pdo->prepare($sql);

                    $query->execute();
                    $vaccin_nb = $query->fetchColumn();
                    ?>
                    <p id="compteur_vaccin"><?php echo $vaccin_nb;?></p>
                    <p>Vaccins différents disponibles</p>

                </div>
                <div>
                    <?php $sql = "SELECT COUNT(*) FROM vac_users";
                    $query = $pdo->prepare($sql);

                    $query->execute();
                    $user_nb = $query->fetchColumn();
                    ?>
                    <p id="compteur_users"><?php echo $user_nb;?></p>
                    <p>Utilisateurs inscrits</p>
                </div>
                <div>
                    <?php $sql = "SELECT COUNT(*) FROM vac_usersvaccins";
                    $query = $pdo->prepare($sql);

                    $query->execute();
                    $gestion_nb = $query->fetchColumn();
                    ?>
                    <p id="compteur_geres"><?php echo $gestion_nb;?></p>
                    <p>Vaccins gérés</p>
                </div>

                <script>
                    var n_vaccin = <?php echo $vaccin_nb; ?>;
                    var n_users = <?php echo $user_nb; ?>;
                    var n_geres = <?php echo $gestion_nb; ?>;
                    var cpt_vaccin = 0;
                    var cpt_users = 0;
                    var cpt_geres = 0;
                    var duree = 3.5;
                    var delta_vaccin = Math.ceil((duree * 1000) / n_vaccin);
                    var delta_users = Math.ceil((duree * 1000) / n_users);
                    var delta_geres = Math.ceil((duree * 1000) / n_geres);
                    var node_vaccin =  document.getElementById("compteur_vaccin");
                    var node_users =  document.getElementById("compteur_users");
                    var node_geres =  document.getElementById("compteur_geres");

                    function countdown_vaccin() {
                        node_vaccin.innerHTML = ++cpt_vaccin;
                        if( cpt_vaccin < n_vaccin ) {
                            setTimeout(countdown_vaccin, delta_vaccin);
                        }
                    }

                    function countdown_users() {
                        node_users.innerHTML = ++cpt_users;
                        if( cpt_users < n_users ) {
                            setTimeout(countdown_users, delta_users);
                        }
                    }

                    function countdown_geres() {
                        node_geres.innerHTML = ++cpt_geres;
                        if( cpt_geres < n_geres ) {
                            setTimeout(countdown_geres, delta_geres);
                        }
                    }

                    setTimeout(countdown_vaccin, delta_vaccin);
                    setTimeout(countdown_users, delta_users);
                    setTimeout(countdown_geres, delta_geres);
                </script>
            </div>
        </div>
    </section>

<?php include('inc/footer.php');
