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
 * material.inc.php
 *
 * ADayAtTheBeachMiquel game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


// Example:

$this->card_types = [
    [
        "card_name" => clienttranslate('Flip'),
        "card_type" => "blue",
        "card_id" => 6,
        "set_type" => "pair",
        "pair_id"=> 6,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Flop'),
        "card_type" => "blue",
        "card_id" => 10,
        "set_type" => "pair",
        "pair_id"=> 6,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Cone'),
        "card_type" => "blue",
        "card_id" => 14,
        "set_type" => "pair",
        "pair_id"=> 12,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Scoop'),
        "card_type" => "blue",
        "card_id" => 12,
        "set_type" => "pair",
        "pair_id"=> 14,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Left swimmie'),
        "card_type" => "blue",
        "card_id" => 7,
        "set_type" => "pair",
        "pair_id"=> 5,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Right swimmie'),
        "card_type" => "blue",
        "card_id" => 5,
        "set_type" => "pair",
        "pair_id"=> 7,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Paddle'),
        "card_type" => "blue",
        "card_id" => 9,
        "set_type" => "pair",
        "pair_id"=> 3,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Paddle ball'),
        "card_type" => "blue",
        "card_id" => 3,
        "set_type" => "pair",
        "pair_id"=> 9,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Frisbee'),
        "card_type" => "blue",
        "card_id" => 4,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    [
        "card_name" => clienttranslate('Kite'),
        "card_type" => "blue",
        "card_id" => 1,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    [
        "card_name" => clienttranslate('Sandcastle'),
        "card_type" => "blue",
        "card_id" => 8,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    [
        "card_name" => clienttranslate('Starfish'),
        "card_type" => "blue",
        "card_id" => 13,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    [
        "card_name" => clienttranslate('Seashell'),
        "card_type" => "blue",
        "card_id" => 11,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    [
        "card_name" => clienttranslate('Swimmer'),
        "card_type" => "blue",
        "card_id" => 15,
        "set_type" => "group",
        "set_size" => 4,
        "nbr" => 6
    ],
    [
        "card_name" => clienttranslate('Lifesaver'),
        "card_type" => "blue",
        "card_id" => 2,
        "set_type" => "joker",
        "nbr" => 1
    ],



    [
        "card_name" => clienttranslate('Playful puppy'),
        "card_type" => "yellow",
        "card_id" => 11,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Hurricane'),
        "card_type" => "yellow",
        "card_id" => 19,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Beach umbrella'),
        "card_type" => "yellow",
        "card_id" => 9,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Seagull'),
        "card_type" => "yellow",
        "card_id" => 7,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Hermit crab'),
        "card_type" => "yellow",
        "card_id" => 16,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Lifeguard'),
        "card_type" => "yellow",
        "card_id" => 5,
        "nbr" => 2
    ],
    [
        "card_name" => clienttranslate('Boat'),
        "card_type" => "yellow",
        "card_id" => 2,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Fishin\''),
        "card_type" => "yellow",
        "card_id" => 3,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Metal detector'),
        "card_type" => "yellow",
        "card_id" => 10,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Bonfire'),
        "card_type" => "yellow",
        "card_id" => 15,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Shark attack'),
        "card_type" => "yellow",
        "card_id" => 17,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Sunburn'),
        "card_type" => "yellow",
        "card_id" => 14,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Wipeout'),
        "card_type" => "yellow",
        "card_id" => 13,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('The wave'),
        "card_type" => "yellow",
        "card_id" => 18,
        "nbr" => 3
    ],
    [
        "card_name" => clienttranslate('Jetski'),
        "card_type" => "yellow",
        "card_id" => 4,
        "nbr" => 3
    ],
    [
        "card_name" => clienttranslate('Pirate'),
        "card_type" => "yellow",
        "card_id" => 6,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Beach ball'),
        "card_type" => "yellow",
        "card_id" => 1,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Treasure chest'),
        "card_type" => "yellow",
        "card_id" => 12,
        "nbr" => 1
    ],
    [
        "card_name" => clienttranslate('Surfer'),
        "card_type" => "yellow",
        "card_id" => 8,
        "nbr" => 1
    ],
];





