<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$page_title = 'Bannisement';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

if (!empty($_GET['banid']) && is_numeric($_GET['banid'])) {
    $id = $_GET['banid'];
    $currentUser = getEntityById('vac_users',$id);

    if (!empty($currentUser)) {
        $sql = "UPDATE vac_users SET role='banni' WHERE id = :id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':id',$id,PDO::PARAM_INT);
        $query->execute();
        header('location: gestion_users.php');

    }  else {
        redirect404();
    }
} elseif (!empty($_GET['unbanid']) && is_numeric($_GET['unbanid'])) {
    $id = $_GET['unbanid'];
    $currentUser = getEntityById('vac_users', $id);

    if (!empty($currentUser)) {
        $sql = "UPDATE vac_users SET role='normal' WHERE id = :id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        header('location: gestion_users.php');

    } else {
        redirect404();
    }
}
else {
    redirect404();
}
