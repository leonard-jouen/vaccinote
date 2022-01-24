<!doctype html>
<html lang="fr">

<?php
//require('inc/fonctions.php');
//require('inc/pdo.php'); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="..\asset\css\style.css">

    <!-- script pour la visualisation des données sur le back -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js"></script>

</head>
<body class="wrap">
<header id="header_back" class="flex sb">

    <?php
    if (!empty($_GET['menu'])) {

        echo '<div class="absolute menu_mobile">
            
            <div>
                <a class="cross_menu" href="?menu=0">
                    <i class="far fa-times-circle"></i>
                </a>
            </div>
            <nav>
                <ul class="flex sb column">
                    <li>
                        <a href="../index.php"><p>Retour sur le site</p>
                        </a>
                    </li>
                    <li>
                        <a href="gestion_users.php"><p>Gestion des utilisateurs</p>
                        </a>
                    </li>
                    <li>
                        <a href="gestion_vaccins.php"><p>Gestion des vaccins</p>
                        </a>
                    </li>
                    <li>
                        <a href="newsletter.php"><p>Newsletter</p>
                        </a>
                    </li>
                    <li>
                        <a href="stats.php"><p>Statistiques</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>';
    }
    ?>

    <div>
        <a class="header_logo" href="./index_back.php">
            <img src="../asset/img/logo.png" alt="logo Vaccinote">
            <?php
            if(empty($_GET['menu']) || intval($_GET['menu']) == 0){
                ?>
                <div class="etiquette_admin">Admin</div>
                <?php
            }
            ?>
        </a>


    </div>

    <nav>
        <ul class="flex sb">
            <li>
                <a href="../index.php"><p>Retour sur le site</p>
                </a>
            </li>
            <li>
                <a href="gestion_users.php"><p>Gestion des utilisateurs</p>
                </a>
            </li>
            <li>
                <a href="gestion_vaccins.php"><p>Gestion des vaccins</p>
                </a>
            </li>
            <li>
                <a href="newsletter.php"><p>Newsletter</p>
                </a>
            </li>
            <li>
                <a href="stats.php"><p>Statistiques</p>
                </a>
            </li>


        </ul>
    </nav>

    <div class="burger">
        <a href="?menu=1"><i class="fas fa-bars"></i></a>
    </div>

</header>
<div class="admin_title">
    <h2>Administration du site</h2>
    <div class="separator-back"></div>
</div>

<?php
