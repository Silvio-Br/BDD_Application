<?php


namespace gamepedia\controller;


use gamepedia\models\Character;
use gamepedia\models\Comments;
use gamepedia\models\Game;
use gamepedia\models\Plateformes;
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

            $platforms = Game::query()->where('id', '=',$args['id'])->with('platforms:id,name,alias,abbreviation')->firstOrFail();

            $plat =[];

            foreach ($platforms['platforms'] as $platform) {
                $com = Plateformes::find($platform->id);
                array_push($plat, ["platform"=>$platform,
                    "links" =>["detail"=>["href"=>$this->c->router->pathFor('platformDetail', ['id'=>$com['id']])]]
                ]);

            }

            $rep =[];

            array_push($rep, [
                "game"=>$jeu,
                "platforms"=>$plat,
                "links"=>["comments" => ["href"=> $this->c->router->pathFor('gameComments', ['id' => $jeu->id])],
                    "characters" => ["href"=> $this->c->router->pathFor('gameCharacters', ['id' => $jeu->id])]
                ]
            ]);

            return $rs->withHeader('Content-Type', 'application/json')->write(json_encode($rep, JSON_PRETTY_PRINT));
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
            return $rs->withHeader('Content-Type', 'application/json')->write(json_encode($jsonTmp, JSON_PRETTY_PRINT));
        } else {
            $rs->getBody()->write("Page inexistante");
            return $rs->withStatus(404);
        }

    }

    public function displayCommentsGame(Request $rq, Response $rs, array $args): Response
    {
        try {
            $jeu = Game::with('comments:id,titre,contenu,created_at')->where('id', '=', $args['id'])->firstOrFail();

            $jsonTmp = [];

            foreach ($jeu['comments'] as $commentaire) {
                $com = Comments::find($commentaire->id);
                $commentaire->auteur = User::find($com->auteur)->first()->nom;

                array_push($jsonTmp, $commentaire);
            }

            $rs->getBody()->write(json_encode($jsonTmp, JSON_PRETTY_PRINT));

            $html = <<<END
<form methode="post">
<input type="text" name="email"/>
<input type="text" name="titre"/>
<textarea name="contenue"/>
</form>

END;


            $rs->getBody()->write($html);

            return $rs->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("Jeu inexistant");
            return $rs->withStatus(404);
        }
    }

    public function displayCharactersGame(Request $rq, Response $rs, array $args): Response
    {
        try {
            $personnages = Game::with('personnages:id,name')->where('id', '=', $args['id'])->firstOrFail();

            $characArray = [];
            foreach ($personnages['personnages'] as $perso) {
                array_push($characArray, [
                    "character" => $perso,
                    "links" => ["self" => [
                        "href" => $this->c->router->pathFor('characterId', ['id' => $perso->id])
                    ]]
                ]);
            }

            $jsonTmp = [
                "characters" => $characArray
            ];

            $rs->getBody()->write(json_encode($jsonTmp, JSON_PRETTY_PRINT));
            return $rs->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("Jeu inexistant");
            return $rs->withStatus(404);
        }
    }

    public function displayCharacterId(Request $rq, Response $rs, array $args): Response {
        try {
            $character = Character::query()->where('id','=',$args['id'])->firstOrFail();

            return $rs->withHeader('Content-Type', 'application/json')->write(json_encode($character, JSON_PRETTY_PRINT));
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("Jeu inexistant");
            return $rs->withStatus(404);
        }
    }

    public function displayPlatformDetail(Request $rq, Response $rs, array $args): Response {
        try{
            $platform = Plateformes::query()->select('id', 'name', 'alias', 'abbreviation', 'deck', 'description', 'release_date', 'original_price')->where('id', '=', $args['id'])->firstOrFail();

            return $rs->withHeader('Content-Type', 'application/json')->write(json_encode($platform, JSON_PRETTY_PRINT));
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("Jeu inexistant");
            return $rs->withStatus(404);
        }

    }

}
