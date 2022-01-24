<?php
require '../vendor/autoload.php';

function debug($tab) {
    echo '<pre style="height: 200px;overflow-y: scroll;font-size: 0.7rem; padding: 0.5rem; font-family: Consolas, monospace; background-color: black; color: palegreen;  text-shadow:0 0 15px forestgreen,
    0 0 20px forestgreen,
    0 0 31px forestgreen,
    0 0 42px forestgreen,
    0 0 92px forestgreen,
    0 0 102px forestgreen,
    0 0 112px forestgreen,
    0 0 161px forestgreen;
    
}">';
    print_r($tab);
    echo '</pre>';
}

function cleanXssGet($key) {
    return trim(strip_tags($_GET[$key]));
}

function cleanXss($key) {
    return trim(strip_tags($_POST[$key]));
}

function echoError($errors,$key) {
    if (!empty($errors[$key])) {
        echo $errors[$key];
    }
}

function recupInputValue($key) {
    if (!empty($_POST[$key])) {
        echo $_POST[$key];
    }
}

function textValidation($errors,$value,$name,$min,$max) {

    if (empty($value)){
        $errors[$name] = 'veuillez renseigner ce champ';
    } elseif (strlen($value)<$min) {
        $errors[$name] = 'veuillez renseigner plus de '.($min-1).' caractères';
    } elseif (strlen($value)>$max) {
        $errors[$name] = 'veuillez renseigner moins de '.($max+1).' caractères';
    }
    return $errors;
}

function uniqueValidation($errors,$value,$name,$target) {

    if (!empty($value)){
        $errors[$name] = $target.' is already in use';
    }
    return $errors;
}


function intValidation($errors,$value,$name,$num) {

    if (empty($value)){
        $errors[$name] = 'veuillez renseigner ce champ';
    } elseif (strlen($value)!= $num) {
        $errors[$name] = 'veuillez renseigner '.$num.' caractères';
    }
    return $errors;
}

function confPassword($errors,$pw1,$pw2,$name) {

    if ($pw1 != $pw2){
        $errors[$name] = 'Passwords doesn\'t match password confirmation';
    }
    return $errors;
}



function selectValidation($errors, $id, $name) {

    if (empty($id)){
        $errors[$name] = 'veuillez renseigner ce champ';
    }
    return $errors;
}


function getEntityById($table,$id) {
    global $pdo;
    $sql = "SELECT * FROM $table WHERE id = :id";
    $query = $pdo ->prepare($sql);
    $query->bindValue(':id',$id,PDO::PARAM_INT);
    $query ->execute();
    return  $query->fetch();
}

function redirect403()
{
    header('Location: ../403.php');
    exit;
}

function redirect404()
{
    header('HTTP/1.1 404 Not Found');
    exit;
}

function verifyUpdate($errors,$key,$target) {

    if (count($errors) > 0) { recupInputValue($key); }
    else {echo $target[$key];}
}

function dateFormat($target,$format='d/m/Y | h:i') {
    if (!empty($target))
        echo date($format, strtotime($target));
};

function clearXssAll($p) {
    $post = array();
    foreach ($p as $key => $value) {
        $post[$key] = trim(strip_tags($value));
    }
    return $post;
};

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function isLoggedIn ():bool
{
    if (!empty($_SESSION['user']) && !empty($_SESSION['user']['id']) && !empty($_SESSION['user']['nom']) && !empty($_SESSION['user']['prenom']) && !empty($_SESSION['user']['email']) && !empty($_SESSION['user']['created_at']) && !empty($_SESSION['user']['role']) && !empty($_SESSION['user']['ip'])) {
        return true;
    }
    return false;
}

function isLoggedAsAdmin():bool {
    if(isLoggedIn() && $_SESSION['user']['role'] == 'admin'){
        return true;
    }
    return false;
}

/**
 * @param $dateString
 * @return string
 */

function getFormattedMonth($dateString):string {
    $month = date('m', strtotime($dateString));
    if($month == 1){
        $fullDate = 'Janvier';
    }
    elseif($month == 2){
        $fullDate = 'Février';
    }
    elseif($month == 3){
        $fullDate = 'Mars';
    }
    elseif($month == 4){
        $fullDate = 'Avril';
    }
    elseif($month == 5){
        $fullDate = 'Mai';
    }
    elseif($month == 6){
        $fullDate = 'Juin';
    }
    elseif($month == 7){
        $fullDate = 'Juillet';
    }
    elseif($month == 8){
        $fullDate = 'Août';
    }
    elseif($month == 9) {
        $fullDate = 'Septembre';
    }
    elseif($month == 10){
        $fullDate = 'Octobre';
    }
    elseif($month == 11){
        $fullDate = 'Novembre';
    }
    elseif($month == 12){
        $fullDate = 'Décembre';
    }

    $fullDate .= ' '.date('Y', strtotime($dateString));
    return $fullDate;
}

/**
 * @param $date
 * @return array
 */

function getAgeDataFromDate($date): array
{
    $year = date('Y', strtotime($date));
    $age = date('Y') - $year;
    if($age == 0){
        $mois = date('m', strtotime($date));
        $age = date('m') - $mois;
        return array('mois', $age);
    }
    else{
        return array('annees', $age);
    }
}

/**
 * @param $errors
 * @param $email
 * @param $key
 * @return mixed
 */

function emailValidation($errors,$email,$key)
{
    if(!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[$key] = 'Veuillez renseigner un email valide';
        }
    } else {
        $errors[$key] = 'Veuillez renseigner un email';
    }
    return $errors;
}

function sendMdpOubliMail($email, $newPassword){
    header('Location: http://jouen.eu/vaccinote/mail_oublimdp.php?mdp='.$newPassword.'&email='.$email.'&redir='.getActualPageLink());
}
function sendContactMail($email, $message){
    header('Location: http://jouen.eu/vaccinote/mail_contact.php?message='.$message.'&email='.$email.'&redir='.getActualPageLink());
}
function sendVerificationMail($email, $token){
    header('Location: http://jouen.eu/vaccinote/mail_verification.php?token='.$token.'&email='.$email.'&redir='.getActualPageLink());
}

/**
 * @return string
 */

function getActualPageLink(): string
{
    $fullLink = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $linkParts = explode('/', $fullLink);
    $fullLink = '';
    for($i = 0; $i < count($linkParts) - 1; $i++){
        if($i > 0){
            $fullLink .= '/';
        }
        $fullLink .= $linkParts[$i];
    }
    return $fullLink.'/';
}

/**
 * @param $date1
 * @param $date2
 * @return int
 */

function getDateDiffInDays($date1, $date2, $abso = false):int
{
    $date1 = new DateTime($date1);
    $date2 = new DateTime($date2);
    $days  = $date2->diff($date1)->format('%a');
    return $days;
}


/**
 * @param $dateString
 * @return string
 */

function getFormattedDate($dateString, $useHour = false):string {
    $date = date('Y-m-d', strtotime($dateString));
    $dateDiff = getDateDiffInDays(date('Y-m-d'), $date);
    $fullDate = '';

    if($dateDiff == 1){
        $fullDate = 'Demain';
    }
    elseif($dateDiff == -1){
        $fullDate = 'Hier';
    }
    elseif($dateDiff == 0){
        $fullDate = 'Aujourd\'hui';
    }
    else{
        $dayOfWeek = date('w', strtotime($dateString));

        if($dayOfWeek == 1){
            $fullDate = 'Lundi';
        }
        elseif($dayOfWeek == 2){
            $fullDate = 'Mardi';
        }
        elseif($dayOfWeek == 3){
            $fullDate = 'Mercredi';
        }
        elseif($dayOfWeek == 4){
            $fullDate = 'Jeudi';
        }
        elseif($dayOfWeek == 5){
            $fullDate = 'Vendredi';
        }
        elseif($dayOfWeek == 6){
            $fullDate = 'Samedi';
        }
        elseif($dayOfWeek == 7){
            $fullDate = 'Dimanche';
        }

        $fullDate .= ' ' . date('d', strtotime($dateString)). ' ';

        $month = date('m', strtotime($dateString));
        if($month == 1){
            $fullDate .= 'janvier';
        }
        elseif($month == 2){
            $fullDate .= 'février';
        }
        elseif($month == 3){
            $fullDate .= 'mars';
        }
        elseif($month == 4){
            $fullDate .= 'avril';
        }
        elseif($month == 5){
            $fullDate .= 'mai';
        }
        elseif($month == 6){
            $fullDate .= 'juin';
        }
        elseif($month == 7){
            $fullDate .= 'juillet';
        }
        elseif($month == 8){
            $fullDate .= 'août';
        }
        elseif($month == 9) {
            $fullDate .= 'septembre';
        }
        elseif($month == 10){
            $fullDate .= 'octobre';
        }
        elseif($month == 11){
            $fullDate .= 'novembre';
        }
        elseif($month == 12){
            $fullDate .= 'décembre';
        }

        if(date('Y', strtotime($dateString)) != date('Y')){
            $fullDate .= ' '.date('Y', strtotime($dateString));
        }
    }

    if($useHour){
        $fullDate .= ' à '.date('H\hi', strtotime($dateString));
    }

    return $fullDate;
}

/**
 * @param $email
 * @param $token
 */

function sendNewsletterMail($email, $message, $titre){
    header('Location: http://jouen.eu/vaccinote/mail_newsletter.php?titre='.$titre.'&message='.$message.'&email='.$email.'&redir='.getActualPageLink());
}