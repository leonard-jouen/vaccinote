<?php
session_start();
require ('inc/fonctions.php');
require ('inc/pdo.php');
$page_title = 'Centre d\'informations';

$itemsPerPage = 5;
$page = 1;
$recherche = '';
if(!empty($_GET['page']) && is_numeric($_GET['page'])){
    $page = intval($_GET['page']);
}

if(!empty($_GET['search'])){
    $recherche = cleanXSSGet('search');

    $sql = "SELECT * FROM vac_vaccins WHERE id LIKE :recherche OR nom_vaccin LIKE :recherche OR createur LIKE :recherche OR status LIKE :recherche OR description LIKE :recherche LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
    $query = $pdo->prepare($sql);
    $query->bindValue(':recherche','%'.$recherche.'%',PDO::PARAM_STR);
    $query->execute();
    $vaccins = $query->fetchAll();

    $sql2 = "SELECT COUNT(id) FROM vac_vaccins WHERE id LIKE :recherche OR nom_vaccin LIKE :recherche OR createur LIKE :recherche OR status LIKE :recherche OR description LIKE :recherche";
    $query2 = $pdo ->prepare($sql2);
    $query2->bindValue(':recherche','%'.$recherche.'%',PDO::PARAM_STR);
    $query2->execute();
    $vaccins_total = $query2->fetchColumn();

}
else{
    $sql = "SELECT * FROM vac_vaccins
ORDER BY nom_vaccin ASC LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
    $query = $pdo ->prepare($sql);
    $query ->execute();
    $vaccins = $query->fetchAll();

    $vaccins_total = "SELECT COUNT(id) FROM vac_vaccins ORDER BY nom_vaccin ASC";


//prépare la requete
    $query = $pdo ->prepare($vaccins_total);

//execute la query
    $query ->execute();
//récupérer les données
    $vaccins_total = $query->fetchColumn();
}

use JasonGrimes\Paginator;

$totalItems = $vaccins_total;
$urlPattern = 'infos.php?page=(:num)';
if(mb_strlen($recherche) > 0){
    $urlPattern .= '&search='.$recherche;
}
$paginator = new Paginator($totalItems, $itemsPerPage, $page, $urlPattern);


include('inc/header.php'); ?>
    <section id="info_vaccin">
        <div class="recherche">
            <form action="" method="get">
                <label for="search">Recherche :</label>
                <input type="text" name="search" id="search" value="<?= $recherche ?>">
                <input class="input_rechercher" type="submit" value="Rechercher">
            </form>
        </div>
        <?php foreach ($vaccins as $vaccin) { ?>
            <div class="info_background">
                <div class="info_vaccin">
                    <h2 class="vaccin_titre"><?php echo $vaccin['nom_vaccin']; ?></h2>
                    <p class="vaccin_description"><?php echo $vaccin['description']; ?></p>
                    <div class="separator"></div>
                </div>
            </div>
            <br>
        <?php } ?>
    </section>

<?php

echo $paginator;

include('inc/footer.php');
