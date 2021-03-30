<?php


namespace gamepedia\controller;


use gamepedia\models\Comments;
use gamepedia\models\Game;
use gamepedia\models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller
{

    private $c = null;

    /**
     * Controller constructor.
     * @param null $c
     */
    public function __construct($c)
    {
        $this->c = $c;
    }

    public function displayGameId(Request $rq, Response $rs, array $args): Response
    {
        try {
            $jeu = Game::select('id','name','alias','deck','description','original_release_date')->where('id','=',$args['id'])->firstOrFail();
            $rs->getBody()->write($jeu);
            return $rs->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("Jeu inexistant");
            return $rs->withStatus(404);
        }
    }

    public function displayGames(Request $rq, Response $rs, array $args): Response
    {
        $jeuxParPage = 200;
        $pageCourante = null;

        $page = $rq->getQueryParam('page');

        if (isset($page) AND !empty($page) AND $page > 0) {
            $page = intval($page);
            $pageCourante = $page;
        } else {
            $pageCourante = 1;
        }

        $depart = ($pageCourante-1) * $jeuxParPage;
        $allGames = Game::query()->select('id','name','alias','deck')->orderBy('id', 'asc')->skip($depart)->take($jeuxParPage)->get();

        $gamesArray = [];

        foreach ($allGames as $game) {
            array_push($gamesArray, [
                "game" => $game,
                "links" => ["self" => [
                    "href" => $this->c->router->pathFor('gameId', ['id' => $game->id])
                ]]
            ]);
        }

        $links = [
            "prev" => ["href" => "api/games?page=" . ($pageCourante-1)],
            "next" => ["href" => "api/games?page=" . ($pageCourante+1)]
        ];

        $jsonTmp = [
            "games" => $gamesArray,
            "links" => $links
        ];

        if (!$allGames->isEmpty()) {
            return $rs->withHeader('Content-Type', 'application/json')->write(json_encode($jsonTmp));
        } else {
            $rs->getBody()->write("Page inexistante");
            return $rs->withStatus(404);
        }

    }

    public function displayCommentsGame(Request $rq, Response $rs, array $args): Response
    {
        try {
            $jeu = Game::with('comments')->where('id', '=', $args['id'])->firstOrFail();

            $jsonTmp = [];

            foreach ($jeu['comments'] as $commentaire) {
                $com = Comments::find($commentaire->id);
                array_push($jsonTmp, [
                    "id" => $commentaire['id'],
                    "titre" => $commentaire['titre'],
                    "texte" => $commentaire['contenu'],
                    "dateCreation" => $commentaire['created_at'],
                    "auteur" => User::find($com->auteur)->first()->nom
                ]);
            }

            $rs->getBody()->write(json_encode($jsonTmp));
            return $rs->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("Jeu inexistant");
            return $rs->withStatus(404);
        }
    }

}
