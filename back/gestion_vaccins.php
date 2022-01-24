
<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
require('../vendor/autoload.php');
$page_title = 'Gestion vaccins';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

$itemsPerPage = 10;

$page = 1;
if(!empty($_GET['page']) && is_numeric($_GET['page'])){
    $page = intval($_GET['page']);
}

$recherche = '';
if(!empty($_GET['search'])){
    $recherche = cleanXssGet('search');

    if($recherche == 'disponible' || $recherche == 'indisponible'){
        $sql = "SELECT * FROM vac_vaccins WHERE status = :recherche LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
        $query = $pdo->prepare($sql);
        $query->bindValue(':recherche',$recherche,PDO::PARAM_STR);

        $sql2 = "SELECT COUNT(id) FROM vac_vaccins WHERE status = :recherche";
        $query2 = $pdo ->prepare($sql2);
        $query2->bindValue(':recherche',$recherche,PDO::PARAM_STR);
        $query2->execute();
        $vaccins_total = $query2->fetchColumn();
    }
    else{
        $sql = "SELECT * FROM vac_vaccins WHERE id LIKE :recherche OR nom_vaccin LIKE :recherche OR createur LIKE :recherche OR status LIKE :recherche OR description LIKE :recherche LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
        $query = $pdo->prepare($sql);
        $query->bindValue(':recherche','%'.$recherche.'%',PDO::PARAM_STR);

        $sql2 = "SELECT COUNT(id) FROM vac_vaccins WHERE id LIKE :recherche OR nom_vaccin LIKE :recherche OR createur LIKE :recherche OR status LIKE :recherche OR description LIKE :recherche";
        $query2 = $pdo ->prepare($sql2);
        $query2->bindValue(':recherche','%'.$recherche.'%',PDO::PARAM_STR);
        $query2->execute();
        $vaccins_total = $query2->fetchColumn();
    }
    $query->execute();
    $vaccins = $query->fetchAll();
}
else{
    $sql = "SELECT * FROM vac_vaccins 
ORDER BY id ASC LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
    $query = $pdo ->prepare($sql);
    $query ->execute();
    $vaccins = $query->fetchAll();

    $vaccins_total = "SELECT COUNT(id) FROM vac_vaccins";
    $query = $pdo ->prepare($vaccins_total);
    $query ->execute();
    $vaccins_total = $query->fetchColumn();
}



use JasonGrimes\Paginator;

require '../vendor/autoload.php';

$totalItems = $vaccins_total;
$urlPattern = 'gestion_vaccins.php?page=(:num)';
if(mb_strlen($recherche) > 0){
    $urlPattern .= '&search='.$recherche;
}
$paginator = new Paginator($totalItems, $itemsPerPage, $page, $urlPattern);

include('inc/header.php');

?>
    <h1><strong>Gestion des vaccins</strong></h1>
<?php

echo '<div class="flex sb tableau" style="flex-flow: wrap">';
?>
<form action="" method="get">
    <label for="search">Recherche :</label>
    <input type="text" name="search" id="search" value="<?= $recherche ?>">
    <input type="submit" value="Rechercher">
</form>
<?php
echo '<table>';
?>

    <tr class="tr_back">
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Créateur</th>
        <th>Status actuel</th>

    </tr>
<?php
foreach ($vaccins as $vaccin){

    echo
        '<tr class="tr_back">
        <td>'.$vaccin['id'].'</td>
        <td>'.$vaccin['nom_vaccin'].'</td>
        <td>'.mb_strimwidth($vaccin['description'],0,50,'...' ).'</td>
        <td>',$vaccin['createur'],'</td> 
        <td>',$vaccin['status'],'</td> 
        <td class="column flex">
        <a class="back_edit_table_btn" href="editvax.php?id='.$vaccin['id'].'">Modifier</a>';

        if($vaccin['status'] == 'disponible'){
            echo '<a class="back_edit_table_btn" style="color: var(--rouge);" href="banvax.php?banid='.$vaccin['id'].'" onclick="return confirm(\'Voulez vous vraiment retirer ce vaccin du site?\')">Retirer</a>';
        }
        else{
            echo '<a class="back_edit_table_btn" style="color: green;" href="banvax.php?unbanid='.$vaccin['id'].'">Remettre</a>';
        }
        echo '</td>
        
    </tr>';
}
echo '</table>';





echo '</div>';
?>



    <?php

    echo $paginator;
    ?>

<?php

include('inc/footer.php');

