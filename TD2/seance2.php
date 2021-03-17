<?php
require_once __DIR__."/../vendor/autoload.php";
use gamepedia\model\Game;
use gamepedia\model\Company;
use gamepedia\model\GameRating;
use gamepedia\model\Genre;
use gamepedia\model\Plateform;
use gamepedia\model\RatingBoard;

use Illuminate\Database\Capsule\Manager as DB;

$db = new DB();

print ("eloquent est installé !\n");

$config = parse_ini_file(__DIR__.'/../conf/db.conf.ini');
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

print "S/O la data base jsuis connecté<br>";

$personnagesDuJeuN = Game::with('personnages')->where('id', '=', 12342)->first();

echo '<h2>Question 1 : </h2>';
foreach ($personnagesDuJeuN['personnages'] as $unPerso) {
    print '<br>'."Nom : " . $unPerso['name'] . ', ' . $unPerso['deck'];
}

echo '<h2>Question 2 : </h2>';
$jeuxOuYaMario = Game::with('personnages')->where('name', 'like', 'Mario%')->get();
echo "Nom des personnages : ";
foreach ($jeuxOuYaMario as $unJeu) {
    foreach ($unJeu['personnages'] as $unPerso) {
        print $unPerso['name']. ", ";
    }
}
print ".";

echo '<h2>Question 3 : </h2>';
$lesCompagnie = Company::with('jeux')->where('name', 'like', '%Sony%')->get();
echo "Nom des jeux développés par Sony : ";
foreach ($lesCompagnie as $uneCompagnie){
    foreach ($uneCompagnie['jeux'] as $unJeu) {
        print $unJeu['name']. ", ";
    }
}
print ".";

echo '<h2>Question 4 : </h2>';
$lesJeuxRates = Game::with('rating')->where('name', 'like', '%Mario%')->get();
echo "Le rating des jeux Mario : ";
foreach ($lesJeuxRates as $unJeuMario) {
    echo '<br>Nom : ' . $unJeuMario['name'] . ', rates : ';
    foreach ($unJeuMario['rating'] as $ratings) {
        print RatingBoard::find($ratings['rating_board_id'])['name'] . ', ';
    }
}
print ".";

echo '<h2>Question 5 : </h2>';
$jeuxMarioPlusDe3Persos = Game::where('name','LIKE','Mario%')->has('personnages','>',3)->get();
echo "Les jeux qui commencent par 'Mario' et ou ya plus de 3 personnages : ";
foreach ($jeuxMarioPlusDe3Persos as $unJeu) {
    print "<br>" . $unJeu['name'];
}
print ".";

echo '<h2>Question 6 : </h2>';
$lesJeuxMario3 = Game::query()->where('name', 'like', 'Mario%')->whereHas('rating', function($q){
    $q->where('name', 'like', '%3+%');
})->get();
echo "Les jeux qui commencent par 'Mario' et qu'il soit '3+' : ";
foreach ($lesJeuxMario3 as $unJeuMario) {
    echo '<br>Nom : ' . $unJeuMario['name'];
}
print ".";

echo '<h2>Question 7 : </h2>';
$lesJeuxMarioPubByIncAnd3Plus = Game::where('name', 'like', 'Mario%')
    ->whereHas('compagniesPublish', function($q) {
        $q->where('name', 'like', '%Inc.%');
    })
    ->whereHas('rating', function($q) {
        $q->where('name', 'like', '%3+%');
    })
    ->get();
echo "Les jeux qui commencent par 'Mario' et publiés par une compagnie contenant 'Inc.' et contient 3+ : ";
foreach ($lesJeuxMarioPubByIncAnd3Plus as $unJeu) {
    print "<br>Nom : " . $unJeu['name'];
}
print ".";

echo '<h2>Question 8 : </h2>';
$lesJeuxMarioPubByInc3PlusAndAvisByCERO = Game::where('name', 'like', 'Mario%')
    ->whereHas('compagniesPublish', function($q) {
        $q->where('name', 'like', '%Inc.%');
    })
    ->whereHas('rating', function($q) {
        $q->where('name', 'like', '%3+%')
            ->where('rating_board_id', '=', RatingBoard::where('name', '=', 'CERO')->first()['id']);
    })
    ->get();
foreach ($lesJeuxMarioPubByInc3PlusAndAvisByCERO as $unJeu) {
    print "<br>Nom : " . $unJeu['name'];
}
print ".";

echo '<h2>Question 9 : </h2>';
$newGenre = new Genre();
$newGenre->name = 'Romantique';
$newGenre->deck = "The 'Romantique' genre is more difficult than other games";
$newGenre->description = "description en html normalement";
$newGenre->save();
echo "Ajout du nouveau genre effectué <br>";

$nouveauGenreId = Genre::where('name', '=', 'Romantique')->first()['id'];
$leGenre = Genre::find($nouveauGenreId);
$leGenre->genres()->attach([12, 56, 12, 345]);
echo "Assosiation des jeux à ce genre efféctuer";