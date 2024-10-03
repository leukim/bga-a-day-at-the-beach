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
 * states.inc.php
 *
 * ADayAtTheBeachMiquel game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: $this->checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

require_once "modules/php/constants.inc.php";

$machinestates = [

    // The initial state. Please do not modify.

    ST_BGA_GAME_SETUP => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["" => ST_PLAYER_TURN]
    ),

    ST_PLAYER_TURN => [
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must choose an action'),
        "descriptionmyturn" => clienttranslate('${you} must choose an action'),
        "type" => "activeplayer",
        "possibleactions" => [
            "actSurfTurf", 
            "actExchange",
            "actYellowCard"
        ],
        "transitions" => [ACT_SURF_TURF => ST_CHECK_CAN_PUT_DOWN_SET, ACT_EXCHANGE => ST_CHECK_CAN_PUT_DOWN_SET, ACT_YELLOW_CARD => ST_CHECK_CAN_PUT_DOWN_SET]
    ],

    ST_CHECK_CAN_PUT_DOWN_SET => [
        "name" => "checkCanPutDownSet",
        "description" => '',
        "type" => "game",
        "action" => "stCheckCanPutDownSet",
        "updateGameProgression" => false,
        "transitions" => [ACT_ALLOW_PUT_DOWN_SET => ST_PUT_DOWN_SET, ACT_CANNOT_PUT_DOWN_SET => ST_NEXT_PLAYER]
    ],

    ST_PUT_DOWN_SET => [
        "name" => "putDownSet",
        "description" => clienttranslate('${actplayer} can put down a set'),
        "descriptionmyturn" => clienttranslate('${you} can put down a set'),
        "type" => "activeplayer",
        "args" => "argPutDownSet",
        "possibleactions" => [
            "actPutDownSet",
            "actPass"
        ],
        "transitions" => [ACT_PUT_DOWN_SET => ST_NEXT_PLAYER, ACT_PASS => ST_NEXT_PLAYER]
    ],

    ST_NEXT_PLAYER => [
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,
        "transitions" => [ACT_END_GAME => ST_END_GAME, ACT_NEXT_PLAYER => ST_PLAYER_TURN]
    ],

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    ST_END_GAME => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],

];



