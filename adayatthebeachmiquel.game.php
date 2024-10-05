<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * ADayAtTheBeachMiquel implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * adayatthebeachmiquel.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */
declare(strict_types=1);

require_once(APP_GAMEMODULE_PATH . "module/table/table.game.php");

use \Bga\GameFramework\Actions\Types\IntArrayParam;

require_once "modules/php/constants.inc.php";
require_once "modules/php/CardDeck.php";
require_once "modules/php/SetDetector.php";

class ADayAtTheBeachMiquel extends Table
{
    private $deck;
    private $set_detector;

    /**
     * Your global variables labels:
     *
     * Here, you can assign labels to global variables you are using for this game. You can use any number of global
     * variables with IDs between 10 and 99. If your game has options (variants), you also have to associate here a
     * label to the corresponding ID in `gameoptions.inc.php`.
     *
     * NOTE: afterward, you can get/set the global variables with `getGameStateValue`, `setGameStateInitialValue` or
     * `setGameStateValue` functions.
     */
    public function __construct()
    {
        parent::__construct();

        $this->deck = new CardDeck($this->getNew( "module.common.deck" ));
        $this->set_detector = new SetDetector($this->deck, $this->card_types, $this);

        $this->initGameStateLabels([
            "my_first_global_variable" => 10,
            "my_second_global_variable" => 11,
            "my_first_game_variant" => 100,
            "my_second_game_variant" => 101,
        ]);
    }

    public function actSurfTurf() {
        // TODO Check is current player?
        $current_player_id = (int)$this->getActivePlayerId();

        $cardToOcean = $this->deck->pickCardToOcean();
        $cardToHand = $this->deck->pickCardToHand($current_player_id);

        // TODO Log "player plays surf and turf"

        $this->notifyAllPlayers('cardToOcean', clienttranslate('${playerName} draws a card into the ocean'), [
            "playerName" => $this->getActivePlayerName(),
            "player_id" => $current_player_id,
            "card" => $cardToOcean,
        ]);

        $players = $this->loadPlayersBasicInfos();
        foreach ($players as $player_id => $player) {
            if ($current_player_id === $player_id) {
                // TODO Add card name to log
                $this->notifyPlayer($player_id, 'cardToHand', clienttranslate('${playerName} draws a card'), [
                    "playerName" => $this->getActivePlayerName(),
                    "card" => $cardToHand,
                ]);
            } else {
                $this->notifyPlayer($player_id, 'cardToPlayer', clienttranslate('${playerName} draws a card'), [
                    'playerName' => $this->getActivePlayerName(),
                    'player_id' => $current_player_id
                ]);
            }
        }

        

        $this->gamestate->nextState(ACT_SURF_TURF);
    }

    public function actExchange(int $ocean_card_id, int $hand_card_id): void {
        $player_id = (int)$this->getActivePlayerId();

        $card_to_player = $this->deck->cardToPlayer($ocean_card_id, $player_id);
        $card_to_ocean = $this->deck->cardToOcean($hand_card_id);

        $this->notifyAllPlayers('exchange', clienttranslate('${playerName} exchanges cards with the ocean'), [
            "playerName" => $this->getActivePlayerName(),
            "player_id" => $player_id,
            "card_to_ocean" => $card_to_ocean,
            "card_to_player" => $card_to_player,
        ]);

        $this->gamestate->nextState(ACT_EXCHANGE);
    }
    
    public function actPutDownSet(#[IntArrayParam] array $card_ids) {
        $player_id = (int)$this->getActivePlayerId();

        $this->deck->putDownSet($player_id, $card_ids);

        $sql = "UPDATE player SET player_score = player_score + 1 WHERE player_id = ".$player_id;
        $this->dbQuery($sql);

        $this->notifyAllPlayers("discard", clienttranslate('${playerName} puts down a set'), [
            'playerName'=> $this->getActivePlayerName(),
            'card_ids_to_discard' => $card_ids,
            'from_player_id' => $player_id,
        ]);

        $this->notifyAllPlayers('increaseScore', clienttranslate('${playerName} scores one set'), [
            'playerName' => $this->getActivePlayerName(),
            'player_id' => $player_id,
        ]);

        $this->gamestate->nextState(ACT_PUT_DOWN_SET);
    }
    public function actPass(): void
    {
        $this->gamestate->nextState(ACT_PASS);
    }

    /**
     * Game state arguments, example content.
     *
     * This method returns some additional information that is very specific to the `playerTurn` game state.
     *
     * @return string[]
     * @see ./states.inc.php
     */
    public function argPutDownSet()
    {
        $player_id = self::getActivePlayerId();

        $sets = $this->set_detector->get_available_sets($player_id);

        return $sets;
    }

    /**
     * Compute and return the current game progression.
     *
     * The number returned must be an integer between 0 and 100.
     *
     * This method is called each time we are in a game state with the "updateGameProgression" property set to true.
     *
     * @return int
     * @see ./states.inc.php
     */
    public function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }

    /**
     * Game state action, example content.
     *
     * The action method of state `nextPlayer` is called everytime the current game state is set to `nextPlayer`.
     */
    public function stNextPlayer(): void {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        $score = $this->get_player_score($player_id);

        if ($score < 3) {
            // Give some extra time to the active player when he completed an action
            $this->giveExtraTime($player_id);
            
            $this->activeNextPlayer();

            // Go to another gamestate
            // Here, we would detect if the game is over, and in this case use "endGame" transition instead 
            $this->gamestate->nextState(ACT_NEXT_PLAYER);
        } else {
            $this->gamestate->nextState(ACT_END_GAME);
        }
    }

    public function stCheckCanPutDownSet(): void {
        $player_id = (int)$this->getActivePlayerId();

        if (count($this->set_detector->get_available_sets($player_id)) > 0) {
            $this->gamestate->nextState(ACT_ALLOW_PUT_DOWN_SET);
        } else {
            $this->gamestate->nextState(ACT_CANNOT_PUT_DOWN_SET);
        }
    }

    /**
     * Migrate database.
     *
     * You don't have to care about this until your game has been published on BGA. Once your game is on BGA, this
     * method is called everytime the system detects a game running with your old database scheme. In this case, if you
     * change your database scheme, you just have to apply the needed changes in order to update the game database and
     * allow the game to continue to run with your new version.
     *
     * @param int $from_version
     * @return void
     */
    public function upgradeTableDb($from_version)
    {
//       if ($from_version <= 1404301345)
//       {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
//
//       if ($from_version <= 1405061421)
//       {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
    }

    /*
     * Gather all information about current game situation (visible by the current player).
     *
     * The method is called each time the game interface is displayed to a player, i.e.:
     *
     * - when the game starts
     * - when a player refreshes the game page (F5)
     */
    protected function getAllDatas()
    {
        $result = [];

        // WARNING: We must only return information visible by the current player.
        $current_player_id = (int) $this->getCurrentPlayerId();

        // Get information about players.
        // NOTE: you can retrieve some extra field you added for "player" table in `dbmodel.sql` if you need it.
        $result["players"] = $this->getCollectionFromDb(
            "SELECT player_id, player_score score FROM player"
        );

        $result['hand'] = $this->deck->getHand($current_player_id);
        $result['ocean'] = $this->deck->getOcean();

        $result['sizes'] = [
            'deck' => $this->deck->deckSize(),
            'discard' => $this->deck->discardSize(),
        ];

        return $result;
    }

    /**
     * Returns the game name.
     *
     * IMPORTANT: Please do not modify.
     */
    protected function getGameName()
    {
        return "adayatthebeachmiquel";
    }

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = [])
    {
        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $player_id => $player) {
            // Now you can access both $player_id and $player array
            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                array_shift($default_colors),
                $player["player_canal"],
                addslashes($player["player_name"]),
                addslashes($player["player_avatar"]),
            ]);
        }

        // Create players based on generic information.
        //
        // NOTE: You can add extra field on player table in the database (see dbmodel.sql) and initialize
        // additional fields directly here.
        static::DbQuery(
            sprintf(
                "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Init global values with their initial values.

        // Dummy content.
        $this->setGameStateInitialValue("my_first_global_variable", 0);

        // Init game statistics.
        //
        // NOTE: statistics used in this file must be defined in your `stats.inc.php` file.

        // Dummy content.
        // $this->initStat("table", "table_teststat1", 0);
        // $this->initStat("player", "player_teststat1", 0);

        $this->deck->init($this->card_types, $this->loadPlayersBasicInfos());

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: pass).
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, otherwise it will fail with a
     * "Not logged" error message.
     *
     * @param array{ type: string, name: string } $state
     * @param int $active_player
     * @return void
     * @throws feException if the zombie mode is not supported at this game state.
     */
    protected function zombieTurn(array $state, int $active_player): void
    {
        $state_name = $state["name"];

        if ($state["type"] === "activeplayer") {
            switch ($state_name) {
                default:
                {
                    $this->gamestate->nextState("zombiePass");
                    break;
                }
            }

            return;
        }

        // Make sure player is in a non-blocking status for role turn.
        if ($state["type"] === "multipleactiveplayer") {
            $this->gamestate->setPlayerNonMultiactive($active_player, '');
            return;
        }

        throw new feException("Zombie mode not supported at this game state: \"{$state_name}\".");
    }

    function get_player_score($player_id) {
        return $this->getUniqueValueFromDB("SELECT player_score FROM player WHERE player_id='$player_id'");
    }
}
