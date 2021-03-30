<?php

use gamepedia\models\Character;
use gamepedia\models\Game;
use \gamepedia\models\RatingBoard;
use gamepedia\models\Compagnie;
use gamepedia\models\User;
use gamepedia\models\Comments;
use Illuminate\Database\Capsule\Manager as DB;
require_once '../../vendor/autoload.php';

$db = new DB();
$db->addConnection(parse_ini_file('../conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

echo '<h1>PARTIE 1</h1>';

/**
 * creation des 2 utilisteurs
 */
$faker = Faker\Factory::create();
$user1 = new User();
$user1->mail = $faker->email();
$user1->prenom = $faker->firstName();
$user1->nom = $faker->lastName();
$user1->adresse = $faker->address();
$user1->tel = $faker->phoneNumber();
$user1->dateNaissance = $faker->dateTimeBetween('1990-01-01', '2012-12-31')->format('d/m/Y');
$user1->save();

$user2 = new User();
$user2->mail = $faker->email();
$user2->prenom = $faker->firstName();
$user2->nom = $faker->lastName();
$user2->adresse = $faker->address();
$user2->tel = $faker->phoneNumber();
$user2->dateNaissance = $faker->dateTimeBetween('1990-01-01', '2012-12-31')->format('d/m/Y');
$user2->save();


echo '<h2> Utilisateurs </h2>';
echo ($user1 . '</br>' . $user2 . '</br>');

/**
 * creation des 3 commentaires
 */
echo '<h2>Commentaires</h2>';
$users = [$user1, $user2];
foreach ($users as $user) {
    for ($i = 0; $i < 3; $i++) {
        $comment = new Comments();
        $comment->titre = $faker->text();
        $comment->contenu = $faker->text();
        $comment->save();

        $user = User::where('mail', '=', $user->mail)->first();
        $user->comments()->attach($comment->id);

        $game = Game::find(12342);
        $game->comments()->attach($comment->id);

        echo $comment . '</br></br>';
    }
}

echo '<h1>PARTIE 2</h1>';

for ($i = 0; $i < 25000; $i++) {
    $user = new User();
    $user->mail = $faker->email();
    $user->prenom = $faker->firstName();
    $user->nom = $faker->lastName();
    $user->adresse = $faker->address();
    $user->tel = $faker->phoneNumber();
    $user->dateNaissance = $faker->dateTimeBetween('1990-01-01', '2012-12-31')->format('d/m/Y');
    $user->save();
    for ($j = 0; $j < 10; $j++) {
        $comment = new Comments();
        $comment->titre = $faker->text();
        $comment->contenu = $faker->text();
        $comment->save();

        $user = User::where('mail', '=', $user->mail)->first();
        $user->comments()->attach($comment->id);

        $game = Game::find(12342);
        $game->comments()->attach($comment->id);
    }
    echo '</br></br>';

}

echo '<h2>Commentaires du user numero 4600</h2>';
$userRandom = User::where('id', '=' ,4600)->first();
$commentaires = $userRandom->comments()->orderBy('created_at', 'DESC')->get();
foreach ($commentaires as $c) {
    echo '<i>' . $c->titre . '</i> '. $c->created_at . '</br></br>';
}

echo '<h2>Utilisateurs ayant posté plus de 5 commentaires</h2>';
$userPlusDe5 = User::has('comments','>',5)->get();
foreach ($userPlusDe5 as $user) {
    echo $user . '</br></br>';
}