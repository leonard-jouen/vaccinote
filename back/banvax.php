<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$page_title = 'Supprime vaccins';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

if (!empty($_GET['banid']) && is_numeric($_GET['banid'])) {
    $id = $_GET['banid'];
    $currentVaccin = getEntityById('vac_vaccins',$id);

    if (!empty($currentVaccin)) {
        $sql = "UPDATE vac_vaccins SET status='indisponible' WHERE id = :id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':id',$id,PDO::PARAM_INT);
        $query->execute();
        header('location: gestion_vaccins.php');

    }  else {
        redirect404();
    }
} elseif (!empty($_GET['unbanid']) && is_numeric($_GET['unbanid'])) {
    $id = $_GET['unbanid'];
    $currentVaccin = getEntityById('vac_vaccins', $id);

    if (!empty($currentVaccin)) {
        $sql = "UPDATE vac_vaccins SET status='disponible' WHERE id = :id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        header('location: gestion_vaccins.php');

    } else {
        redirect404();
    }
}
else {
    redirect404();
}