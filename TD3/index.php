<?php

use gamepedia\models\Character;
use gamepedia\models\Game;
use \gamepedia\models\RatingBoard;
use gamepedia\models\Compagnie;
use Illuminate\Database\Capsule\Manager as DB;
require_once '../../vendor/autoload.php';

$db = new DB();
$db->addConnection(parse_ini_file('../conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

/**
 * PARTIE 1
 */
echo "<h1>Partie 1</h1>";
/**
 * QUESTION 1
 */
echo "<h2>Question 1</h2>";
$tmp1 = microtime(true);

$tousLesJeux = Game::all();

$tmp2 = microtime(true);

echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";

/**
 * QUESTION 2
 */
echo "<h2>Question 2</h2>";
$tmp1 = microtime(true);

$jeuxMario = Game::where('name','LIKE','%Mario%');

$tmp2 = microtime(true);

echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";

/**
 * QUESTION 3
 */
echo "<h2>Question 3</h2>";
$tmp1 = microtime(true);

$jeuMarios = Game::where('name','LIKE','Mario%')->with('personnages')->get();

$tmp2 = microtime(true);

echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";

/**
 * QUESTION 4
 */
echo "<h2>Question 4</h2>";
$tmp1 = microtime(true);

$jeuMarios = Game::where('name','LIKE','Mario%')->whereHas('ratings', function($q) {
    $q->where('name', 'LIKE', '%3+%');
})->get();

$tmp2 = microtime(true);

echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";

/**
 * QUESTION 5
 */

/**
 * AVEC INDEX
 */
echo "<h2>Question 5</h2>";

/**
 * On observe une amélioration du temps d'exécution grâce à l'index
 */
$tmp1 = microtime(true);
$jeuMarios = Game::where('name','LIKE','F%')->get();
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";
$tmp1 = microtime(true);
$jeuDesert = Game::where('name','LIKE','H%')->get();
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";
$tmp1 = microtime(true);
$jeuSonic = Game::where('name','LIKE','Z%')->get();
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";

/**
 * Ici l'index ne permet de réduire le temps d'exécution des requêtes
 * Le fait de chercher les valeurs contenant 'value' nous oblige à parcourir toutes les valeurs de la table
 * l'indexation ne sert à rien
 */
$tmp1 = microtime(true);
$jeuMarios = Game::where('name','LIKE','%F%')->get();
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";
$tmp1 = microtime(true);
$jeuDesert = Game::where('name','LIKE','%H%')->get();
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";
$tmp1 = microtime(true);
$jeuSonic = Game::where('name','LIKE','%Z%')->get();
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";


/**
 * on observe un gain de temps, cependant le temps d'exécution sans l'index est déjà en lui même rapide
 * SANS INDEX : ~0.0085s
 * AVEC INDEX : ~0.0003s
 */
$tmp1 = microtime(true);
$compagnieJaponaise = Compagnie::where('location_country', 'LIKE', 'Japan');
$tmp2 = microtime(true);
echo "Temps exécution : " . ($tmp2 - $tmp1) . " sec.</br></br>";


echo '<h1>Partie : 2</h1>';
echo '<h2>Question : 1-5 sans chargement lié</h2>';


$jeuxOuYaMario = Game::where('name','LIKE','%Mario%')->get()->toArray(); // 1 requête

$lesPersonnages = Game::find(12342)->personnages()->get(); // 2 requêtes

$personnagesApparuLaFirstTimeDansUnMario = Game::query()->where('name', 'like', '%Mario%')->whereHas('personnages', function ($q) {
    $q->where('first_appeared_in_game_id', '=', 'game_id');
})->get(); // 1 requête

$jeuMario = Game::where('name','like','%Mario%')->get();
foreach ($jeuMario as $item) {
    $item->personnages()->get();
} // 159 requêtes

$companySony = Compagnie::where('name','like','%Sony%')->get();
foreach ($companySony as $item) {
    $item->jeux()->get();
} // 14 requêtes

echo '<h3>Nb de requêtes executées : '. sizeof(DB::getQueryLog()) .'</h3>';
DB::connection()->enableQueryLog();


echo '<h2>Question : 4 avec chargement lié</h2>';


$personnagesEdgerLoad = Character::with('games')->where('name','like','%Mario%')->get();
$tmp = DB::getQueryLog();
$i = 0;
foreach( DB::getQueryLog() as $q){
    if ($i>176 && $i<179) {
        echo "-------------- <br>";
        echo "query : " . $q['query'] ."<br>";
        echo " --- bindings : [ ";
        foreach ($q['bindings'] as $b ) {
            echo " ". $b."," ;
        }
        echo " ] ---<br>";
        echo "-------------- <br> <br>";
    }
    $i++;
}


echo '<h2>Question : 5 avec chargement lié</h2>';


$comapny = Compagnie::with('jeux')->where('name','like','%Sony%')->get();

$i = 0;
foreach( DB::getQueryLog() as $q){
    if ($i>178) {
        echo "-------------- <br>";
        echo "query : " . $q['query'] ."<br>";
        echo " --- bindings : [ ";
        foreach ($q['bindings'] as $b ) {
            echo " ". $b."," ;
        }
        echo " ] ---<br>";
        echo "-------------- <br> <br>";
    }
    $i++;
};

echo "<U style='color: red;'>La technique utilisée par SQL est la jointure c'est pourquoi, une requête est exécutée ce qui améliore les performances.</U>";
echo '<h2>Historique de toutes les requêtes :</h2>';

foreach( DB::getQueryLog() as $q){
    echo "-------------- <br>";
    echo "query : " . $q['query'] ."<br>";
    echo " --- bindings : [ ";
    foreach ($q['bindings'] as $b ) {
        echo " ". $b."," ;
    }
    echo " ] ---<br>";
    echo "-------------- <br> <br>";
};
