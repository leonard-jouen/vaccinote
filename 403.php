<?php
session_start();

require('inc/fonctions.php');
require('inc/pdo.php');
include('inc/header.php'); ?>

    <section id="acces_refuse">
        <div class="titre">
            <h2>Accès refusé</h2>
            <div class="separator"></div>
        </div>
        <div>
            <p>Vous n'avez pas accès à cette page</p>
        </div>
    </section>


<?php include('inc/footer.php');
