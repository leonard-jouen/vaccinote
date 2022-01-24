
<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
require('../vendor/autoload.php');
$page_title = 'Bannisement utilisateur';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

$itemsPerPage = 10;

if (!empty($_GET['page'])) {
    cleanXss($_GET['page']);
    $sql = "SELECT * FROM vac_users WHERE role = 'banni'
ORDER BY created_at DESC LIMIT ".$itemsPerPage." OFFSET ".($_GET['page']*$itemsPerPage-$itemsPerPage)."";
} else {
    $sql = "SELECT * FROM vac_users WHERE role = 'banni' ORDER BY created_at DESC LIMIT 10 ";
}//prépare la requete
$query = $pdo ->prepare($sql);
//execute la query
$query ->execute();
//récupérer les données
$users = $query->fetchAll();
//debug($articles);

$users_total = "SELECT COUNT(*) FROM vac_users WHERE role = 'banni' ORDER BY created_at DESC";


//prépare la requete
$query = $pdo ->prepare($users_total);

//execute la query
$query ->execute();
//récupérer les données
$users_total = $query->fetchColumn();



use JasonGrimes\Paginator;

$totalItems = $users_total;
$currentPage = 1;
$urlPattern = 'index.php?page=(:num)';
$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

include('inc/header.php');

?>

    <h1><strong>Utilisateurs bannis :</strong></h1>

<?php

echo '<div class="flex sb" style="flex-flow: wrap">';
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
        <td>',$diff->format('%y'),'</td> 
        <td>',$user['email'],'</td> ';
    if ($user['sexe'] == 0) {
        echo '<td>H</td>';} elseif ($user['sexe'] == 1) {
        echo '<td>F</td>';} else {
        echo '<td>N/A</td>';}

    echo '<td>',$vaccins,'</td> 
        <td>'.$user['role'].'</td>
        <td>',$user['created_at'],'</td>   
        <td class="flex column">
        <a class="back_edit_table_btn" href="userdetails.php?id='.$user['id'].'">Voir</a>
        <a class="back_edit_table_btn" style="color: red;" href="ban.php?unbanid='.$user['id'].'" onclick="return confirm(\'Voulez vous vraiment débannir cet utilisateur?\')">Débannir</a>
        </td>
        
    </tr>';
}
echo '</table>';





echo '</div>';
?>

    <html>
    <head>
        <!-- The default, built-in template supports the Twitter Bootstrap pagination styles. -->
        <!--    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">-->
    </head>
    <body>

    <?php
    // Example of rendering the pagination control with the built-in template.
    // See below for information about using other templates or custom rendering.

    echo $paginator;
    ?>

    </body>
    </html>

<?php

include('inc/footer.php');

