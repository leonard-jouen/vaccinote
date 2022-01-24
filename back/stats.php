<?php
session_start();
require('inc/fonctions.php');
require('inc/pdo.php');
$page_title = 'Nos statistiques';

if(!isLoggedAsAdmin()){
    redirect403();
    die();
}

$sql = "SELECT count(id) FROM vac_users";
$query = $pdo->prepare($sql);
$query->execute();
$userCount = $query->fetchColumn();

$sql = "SELECT count(id) FROM vac_vaccins";
$query = $pdo->prepare($sql);
$query->execute();
$vaccinCount = $query->fetchColumn();

$sql = "SELECT count(id) FROM vac_usersvaccins";
$query = $pdo->prepare($sql);
$query->execute();
$rappelsCount = $query->fetchColumn();

$sql = "SELECT count(id) FROM vac_rdv";
$query = $pdo->prepare($sql);
$query->execute();
$rdvCount = $query->fetchColumn();

$sql = "SELECT id,created_at FROM vac_users";
$query = $pdo->prepare($sql);
$query->execute();
$users = $query->fetchAll();

$newUsersCount = 0;
if(!empty($users)){
    foreach ($users as $user){
        if(!empty($user['created_at'])){
            if(getDateDiffInDays($user['created_at'], date('Y-m-d')) == 0){
                $newUsersCount++;
            }
        }
    }
}

include('inc/header.php'); ?>

<h1>Statistiques</h1>

<section id="compteurs">
    <div class="wrap">
        <div class="compteur_item">
            <p class="compteur_title">Utilisateurs</p>
            <p class="compteur_nb"><?= $userCount ?></p>
        </div>
        <div class="compteur_item">
            <p class="compteur_title">Vaccins</p>
            <p class="compteur_nb"><?= $vaccinCount ?></p>
        </div>
        <div class="compteur_item">
            <p class="compteur_title">Inscriptions aujourd'hui</p>
            <p class="compteur_nb"><?= $newUsersCount ?></p>
        </div>
        <div class="compteur_item">
            <p class="compteur_title">Rappels</p>
            <p class="compteur_nb"><?= $rappelsCount ?></p>
        </div>
        <div class="compteur_item">
            <p class="compteur_title">Rendez-vous</p>
            <p class="compteur_nb"><?= $rdvCount ?></p>
        </div>
    </div>
</section>

<?php

/* VACCINATION & RAPPELS */

$mois = array();
$mois[] = date('Y-m', strtotime("-3 months"));
$mois[] = date('Y-m', strtotime("-2 months"));
$mois[] = date('Y-m', strtotime("-1 months"));
$mois[] = date('Y-m');
$mois[] = date('Y-m', strtotime("+1 months"));
$mois[] = date('Y-m', strtotime("+2 months"));
$mois[] = date('Y-m', strtotime("+3 months"));

$vaccins = array();
$rappels = array();
for($i = 0; $i < count($mois); $i++){
    $vaccins[$i] = 0;
    $rappels[$i] = 0;
}

$sql = "SELECT date_vaccination, date_rappel FROM vac_usersvaccins";
$query = $pdo->prepare($sql);
$query->execute();
$vaccinArray = $query->fetchAll();

foreach ($vaccinArray as $vaccinData){

    if(!empty($vaccinData['date_vaccination'])){
        $date = date('Y-m', strtotime($vaccinData['date_vaccination']));

        for($i = 0; $i < count($mois); $i++){
            if($mois[$i] == $date){
                $vaccins[$i]++;
                break;
            }
        }
    }

    if(!empty($vaccinData['date_rappel'])){
        $date = date('Y-m', strtotime($vaccinData['date_rappel']));

        for($i = 0; $i < count($mois); $i++){
            if($mois[$i] == $date){
                $rappels[$i]++;
                break;
            }
        }
    }
}

for($i = 0;$i < count($mois); $i++){
    $mois[$i] = getFormattedMonth($mois[$i]);
}



/* AGE DES UTILISATEURS */

$sql = "SELECT naissance FROM vac_users";
$query = $pdo->prepare($sql);
$query->execute();
$naissances = $query->fetchAll();

$tranche_0_10 = 0;
$tranche_10_18 = 0;
$tranche_18_25 = 0;
$tranche_25_40 = 0;
$tranche_40_60 = 0;
$tranche_60 = 0;

foreach ($naissances as $naissance){
    if(!empty($naissance['naissance'])){
        $age = getAgeDataFromDate($naissance['naissance']);

        if($age[0] == 'mois' || $age[1] <= 10){
            $tranche_0_10++;
        }
        elseif($age[1] <= 18){
            $tranche_10_18++;
        }
        elseif($age[1] <= 25){
            $tranche_18_25++;
        }
        elseif($age[1] <= 40){
            $tranche_25_40++;
        }
        elseif($age[1] <= 60){
            $tranche_40_60++;
        }
        else{
            $tranche_60++;
        }
    }
}

?>

<section id="graphes">
    <div class="wrap">
        <div class="vaccin_rappel">
            <div class="title">
                <h2>Vaccinations & Rappels</h2>
            </div>
            <canvas id="mixed-chart"></canvas>
        </div>
        <div class="age_moyen">
            <div class="title">
                <h2>Âge des utilisateurs</h2>
            </div>
            <canvas id="doughnut-chart"></canvas>
        </div>
    </div>
</section>

<script>

    var moisArray = <?php echo json_encode($mois); ?>;
    var vaccinsArray = <?php echo json_encode($vaccins); ?>;
    var rappelsArray = <?php echo json_encode($rappels); ?>;

    new Chart(document.getElementById("mixed-chart"), {
        type: 'bar',
        data: {
            labels: moisArray,
            datasets: [{
                label: "Rappels",
                type: "line",
                borderColor: "#c45850",
                data: rappelsArray,
                fill: false
            },{
                label: "Vaccinations",
                type: "bar",
                backgroundColor: "#3cba9f",
                data: vaccinsArray,
            }
            ]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Vaccinations & Rappels'
            },
            legend: { display: false },
        }
    });

    var tranche_0_10 = <?php echo $tranche_0_10; ?>;
    var tranche_10_18 = <?php echo $tranche_10_18; ?>;
    var tranche_18_25 = <?php echo $tranche_18_25; ?>;
    var tranche_25_40 = <?php echo $tranche_25_40; ?>;
    var tranche_40_60 = <?php echo $tranche_40_60; ?>;
    var tranche_60 = <?php echo $tranche_60; ?>;

    function handleHover(evt, item, legend) {
        legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
            colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
        });
        legend.chart.update();
    }

    function handleLeave(evt, item, legend) {
        legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
            colors[index] = color.length === 9 ? color.slice(0, -2) : color;
        });
        legend.chart.update();
    }

    new Chart(document.getElementById("doughnut-chart"), {
        type: 'doughnut',
        data: {
            labels: ["0-10", "10-18", "18-25", "25-40", "40-60", "60+"],
            datasets: [
                {
                    label: "Tranches d'âge",
                    backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850", "#44dd58"],
                    data: [tranche_0_10, tranche_10_18, tranche_18_25, tranche_25_40, tranche_40_60, tranche_60]
                }
            ]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Tranches d\'âge'
            },
            plugins: {
                legend: {
                    onHover: handleHover,
                    onLeave: handleLeave
                }
            }
        }
    });
</script>


<?php

$sql = "SELECT count(uv.id) AS rappel_count FROM vac_usersvaccins AS uv LEFT JOIN vac_vaccins AS v ON v.id = uv.vaccin_id GROUP BY v.id";
$query = $pdo->prepare($sql);
$query->execute();
$rappels = $query->fetchAll();
$totalRappelCount = 0;
foreach ($rappels as $rappel){
    $totalRappelCount += $rappel['rappel_count'];
}

$sql = "SELECT v.nom_vaccin, count(uv.id) AS rappel_count FROM vac_usersvaccins AS uv LEFT JOIN vac_vaccins AS v ON v.id = uv.vaccin_id GROUP BY v.id ORDER BY count(uv.id) DESC LIMIT 5";
$query = $pdo->prepare($sql);
$query->execute();
$rappels = $query->fetchAll();

?>

    <section id="rappels">
        <div class="wrap">
            <div class="title">
                <h2>TOP 5 des vaccins</h2>
            </div>
            <div class="vaccin_list">
                <?php
                foreach ($rappels as $rappel){

                    $prct = ($rappel['rappel_count'] / $totalRappelCount) * 100;
                    $prctProgressBar = $prct / 1.2;
                    ?>
                    <div class="vaccin_item">
                        <div class="title"><?php echo $rappel['nom_vaccin']; ?></div>
                        <div class="progress">
                            <div class="progress_bar" style="width: <?= round($prctProgressBar); ?>%">
                                <div class="rappel_count">
                                    <span><?= $rappel['rappel_count']; ?></span>
                                </div></div>
                            <span><?= round($prct); ?>%</span>
                        </div>
                    </div>
                    <?php
                }

                ?>
            </div>
        </div>
    </section>
<br>

<?php
include('inc/footer.php');
