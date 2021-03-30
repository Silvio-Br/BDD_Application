<?php

use gamepedia\models\Character;
use gamepedia\models\Game;
use \gamepedia\models\RatingBoard;
use gamepedia\models\Compagnie;
use gamepedia\models\User;
use gamepedia\models\Comments;
use Illuminate\Database\Capsule\Manager as DB;
require_once './vendor/autoload.php';

$db = new DB();
$db->addConnection(parse_ini_file('src/conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

$faker = Faker\Factory::create();

for ($i = 1; $i < 25000; $i++) {
    $user = new User();
    $user->email = $faker->email();
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
        $comment->auteur = User::where('email','=',$user->email)->first()->id;
        $comment->save();

        $game = Game::find($i);
        $game->comments()->attach($comment->id);
    }
    echo '</br></br>';
}
