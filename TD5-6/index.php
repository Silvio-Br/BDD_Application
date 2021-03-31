<?php

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use gamepedia\controller\Controller as Controller;
use gamepedia\models\Game as Game;
use Illuminate\Database\Capsule\Manager as DB;

require_once './vendor/autoload.php';

$db = new DB();
$db->addConnection(parse_ini_file('src/conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

$app = new App();
$c = new Container(['settings'=>['displayErrorDetails'=>true]]);

$app->get('/api/games/{id}[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new Controller($this);
    return $c->displayGameId($rq,$rs,$args);
})->setName('gameId');

$app->get('/api/games/{id}/comments[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new Controller($this);
    return $c->displayCommentsGame($rq,$rs,$args);
})->setName('gameComments');

$app->post('/api/games/{id}/comments[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new Controller($this);
    return $c->displayCommentsGame($rq,$rs,$args);
})->setName('gameCommentsPost');

$app->get('/api/games[/]', function(Request $rq, Response $rs, array $args) {
    $c = new Controller($this);
    return $c->displayGames($rq,$rs,$args);
})->setName('games');

$app->get('/api/games/{id}/characters[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new Controller($this);
    return $c->displayCharactersGame($rq,$rs,$args);
})->setName('gameCharacters');

$app->get('/api/characters/{id}[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new Controller($this);
    return $c->displayCharacterId($rq,$rs,$args);
})->setName('characterId');

$app->get('/api/platform/{id}[/]', function(Request $rq, Response $rs, array $args): Response {
    $c = new Controller($this);
    return $c->displayPlatformDetail($rq,$rs,$args);
})->setName('platformDetail');

$app->run();

