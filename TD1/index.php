<?php

use gamepedia\models\Game;
use gamepedia\models\Compagnie;
use gamepedia\models\Plateformes;
use Illuminate\Database\Capsule\Manager as DB;
require_once '../vendor/autoload.php';

$db = new DB();
$db->addConnection(parse_ini_file('./conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

/**
 * Q1 : lister les jeux dont le nom contient 'Mario'
 */
$jeuMario = Game::query()->where('name','LIKE','%Mario%')->get()->toArray();

/**
 * Q2 : lister les compagnies installées au Japon
 */
$compagnieJapon = Compagnie::query()->where('location_country','=', 'Japan')->get()->toArray();

/**
 * Q3 : listes les plateformes dont la base installée est >= 10 000 000
 */
$plateformes = Plateformes::query()->where('install_base','>=', '10000000')->get()->toArray();

/**
 * Q4 : lister 442 jeux à partir du 21 173ème
 */
$jeux = Game::query()->skip(21173)->take(442)->get()->toArray();

/**
 * Q5 : lister les jeux, afficher leur nom et deck, en pagninant
 */
$jeuxParPage = 500;
$pageCourante = null;
$nombreDeJeux = Game::count();

if (isset($_GET['page']) AND !empty($_GET['page']) AND $_GET['page'] > 0) {
    $_GET['page'] = intval($_GET['page']);
    $pageCourante = $_GET['page'];
} else {
    $pageCourante = 1;
}

$depart = ($pageCourante-1) * $jeuxParPage;

$allGames = Game::query()->orderBy('id', 'asc')->skip($depart)->take($jeuxParPage)->get();
$pageTotales = ceil($nombreDeJeux / $jeuxParPage);

for ($i = 1; $i <= $pageTotales; $i++) {
    echo '<a href="index.php?page=' . $i . '">' . $i . '</a> ';
}

foreach ($allGames as $game) {
    echo "<p><h4>Jeu ($game->id) :</h4> $game->name</p> <p><h4>Deck :</h4> $game->deck</p></br>";
}