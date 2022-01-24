<!doctype html>
<html lang="fr">


<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$page_title?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asset\css\style.css">
</head>
<body class="wrap">
<header class="flex sb">
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
                        <a href="./index.php"><p>ACCUEIL</p>
                            <div class="blue_stripe_header"></div>
                        </a>
                    </li>
        
                    <li>
                        <a href="./rdv.php"><p>RENDEZ-VOUS</p>
                            <div class="blue_stripe_header"></div>
                        </a>
                    </li>
        
                    <li>
                        <a href="carnet.php"><p>VOIR MES VACCINS</p>
                            <div class="blue_stripe_header"></div>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="buttons">';
            if (!isLoggedIn()) {
                echo '<div class="flex sb column">';
                    echo '<a href="./ajax_inscription.php" class="bouton_inscription"><strong>Créer un compte</strong></a>';
                    echo '<a href="./connexion.php" class="bouton_inscription"><strong>Se connecter</strong></a>';
                echo '</div>';
            } else {
                echo '<div class="flex sb column">';
                    echo '<a href="compte.php" class="bouton_compte"><strong>Mon compte</strong></a>';
                    echo '<a href="./logout.php" class="bouton_deco"><strong>Déconnexion</strong></a>';
                echo '</div>';
            }
            echo '</div>
        </div>';
    }
    ?>


    <div>
        <a class="header_logo" href="./index.php">
            <img src="asset/img/logo.png" alt="logo Vaccinote">
        </a>
    </div>
    <nav>
        <ul class="flex">
            <li>
                <a href="./index.php"><p>ACCUEIL</p>
                    <div class="blue_stripe_header"></div>
                </a>
            </li>

            <li>
                <a href="./rdv.php"><p>RENDEZ-VOUS</p>
                    <div class="blue_stripe_header"></div>
                </a>
            </li>

            <li>
                <a href="carnet.php"><p>MES VACCINS</p>
                    <div class="blue_stripe_header"></div>
                </a>
            </li>
        </ul>
    </nav>
    <div class="buttons">
    <?php if (!isLoggedIn()) {
        echo '<div class="flex sb">';
                echo '<a href="./ajax_inscription.php" class="bouton_inscription"><strong>Créer un compte</strong></a>';
                echo '<a href="./connexion.php" class="bouton_inscription"><strong>Se connecter</strong></a>';
        echo '</div>';
    } else {
        echo '<div class="flex sb">';
            echo '<a href="compte.php" class="bouton_compte"><strong>Mon compte</strong></a>';
            echo '<a href="./logout.php" class="bouton_deco"><strong>Déconnexion</strong></a>';
        echo '</div>';
    } ?>
    </div>
    <div class="burger">
        <a href="?menu=1"><i class="fas fa-bars"></i></a>
    </div>
</header>

<?php
