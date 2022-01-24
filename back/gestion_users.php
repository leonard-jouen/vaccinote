
<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
require('../vendor/autoload.php');
$page_title = 'Gestion utilisateurs';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

$itemsPerPage = 10;
$page = 1;
$recherche = '';
if(!empty($_GET['page']) && is_numeric($_GET['page'])){
    $page = intval($_GET['page']);
}

if(!empty($_POST['submitted']) && !empty($_POST['search'])){
    $recherche = cleanXSS('search');

    $sql = "SELECT * FROM vac_users WHERE id LIKE :recherche OR nom LIKE :recherche OR prenom LIKE :recherche OR email LIKE :recherche OR tel LIKE :recherche OR ville LIKE :recherche OR adresse LIKE :recherche OR postal LIKE :recherche OR role LIKE :recherche OR created_at LIKE :recherche LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
    $query = $pdo->prepare($sql);
    $query->bindValue(':recherche','%'.$recherche.'%',PDO::PARAM_STR);
    $query->execute();
    $users = $query->fetchAll();

    $sql2 = "SELECT COUNT(id) FROM vac_users WHERE id LIKE :recherche OR nom LIKE :recherche OR prenom LIKE :recherche OR email LIKE :recherche OR tel LIKE :recherche OR ville LIKE :recherche OR adresse LIKE :recherche OR postal LIKE :recherche OR role LIKE :recherche OR created_at LIKE :recherche";
    $query2 = $pdo ->prepare($sql2);
    $query2->bindValue(':recherche',$recherche,PDO::PARAM_STR);
    $query2->execute();
    $users_total = $query2->fetchColumn();
}

else{
    $sql = "SELECT * FROM vac_users WHERE role = 'normal' OR role = 'admin' ORDER BY created_at DESC LIMIT ".$itemsPerPage." OFFSET ".($page*$itemsPerPage-$itemsPerPage);
    $query = $pdo ->prepare($sql);
//execute la query
    $query ->execute();
//récupérer les données
    $users = $query->fetchAll();
//debug($articles);

    $users_total = "SELECT COUNT(*) FROM vac_users ORDER BY created_at DESC";


//prépare la requete
    $query = $pdo ->prepare($users_total);

//execute la query
    $query ->execute();
//récupérer les données
    $users_total = $query->fetchColumn();
}





use JasonGrimes\Paginator;

require '../vendor/autoload.php';

$totalItems = $users_total;
$urlPattern = 'gestion_users.php?page=(:num)';
if(mb_strlen($recherche) > 0){
    $urlPattern .= '&search='.$recherche;
}
$paginator = new Paginator($totalItems, $itemsPerPage, $page, $urlPattern);

include('inc/header.php');

?>

<h1><strong>Gestion des utilisateurs</strong></h1>

<?php

echo '<div class="flex sb tableau" style="flex-flow: wrap">';
?>
<form action="" method="post">
    <label for="search">Recherche :</label>
    <input type="text" name="search" id="search" value="<?= $recherche ?>">
    <input type="submit" name="submitted" value="Rechercher">
</form>

<a class="gestion_user_banned_btn" href="banned_users.php">Gestion des utilisateurs bannis</a>

<?php
echo '<table>';
?>

    <tr class="tr_back">
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Age</th>
        <th>Email</th>
        <th>Sexe</th>
        <th>Vaccins</th>
        <th>Rôle</th>
        <th>Date d'inscription</th>
    </tr>
<?php
foreach ($users as $user){
    $dateOfBirth = $user['naissance'];
    $today = date("Y-m-d");
    $diff = date_diff(date_create($dateOfBirth), date_create($today));
    $age = $diff->format('%y');
    if($age <= 0){
        $ageAr = getAgeDataFromDate($dateOfBirth);
        $age = $ageAr[1];
    }
    $sql = "SELECT COUNT(*) FROM vac_usersvaccins WHERE user_id = :userid";
    $query = $pdo ->prepare($sql);
    $query->bindValue('userid',$user['id'],PDO::PARAM_INT);
    $query ->execute();
    $vaccins = $query->fetchColumn();

    echo
        '<tr class="tr_back">
        <td>'.$user['id'].'</td>
        <td>'.$user['nom'].'</td>
        <td>'.$user['prenom'].'</td>
        <td>',$age,'</td> 
        <td>',$user['email'],'</td> ';
        if ($user['sexe'] == 0) {
        echo '<td>H</td>';}
        elseif ($user['sexe'] == 2) {echo '<td>N/A</td>';}
        else {
        echo '<td>F</td>';};

        echo '<td>',$vaccins,'</td> 
        <td>'.$user['role'].'</td>
        <td>',$user['created_at'],'</td>   
        <td class="flex column">
        <a class="back_edit_table_btn" href="userdetails.php?id='.$user['id'].'">Voir</a>
        <a class="back_edit_table_btn" style="color: var(--rouge);" href="ban.php?banid='.$user['id'].'" onclick="return confirm(\'Voulez vous vraiment bannir cet utilisateur?\')">Bannir</a>
        </td>
        
    </tr>';
}
echo '</table>';

?>
<?php

echo '</div>';
?>




<?php

echo $paginator;
?>


<?php

include('inc/footer.php');

