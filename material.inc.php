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

 require_once "modules/php/constants.inc.php";

$this->card_types = [
    // Blue cards have card type IDs 0-14
    5 => [
        "card_name" => clienttranslate('Flip'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 5,
        "set_type" => "pair",
        "pair_id"=> 9,
        "nbr" => 1
    ],
    9 => [
        "card_name" => clienttranslate('Flop'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 9,
        "set_type" => "pair",
        "pair_id"=> 5,
        "nbr" => 1
    ],
    13 => [
        "card_name" => clienttranslate('Cone'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 13,
        "set_type" => "pair",
        "pair_id"=> 11,
        "nbr" => 1
    ],
    11 => [
        "card_name" => clienttranslate('Scoop'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 11,
        "set_type" => "pair",
        "pair_id"=> 13,
        "nbr" => 1
    ],
    6 => [
        "card_name" => clienttranslate('Left swimmie'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 6,
        "set_type" => "pair",
        "pair_id"=> 4,
        "nbr" => 1
    ],
    4 => [
        "card_name" => clienttranslate('Right swimmie'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 4,
        "set_type" => "pair",
        "pair_id"=> 6,
        "nbr" => 1
    ],
    8 => [
        "card_name" => clienttranslate('Paddle'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 8,
        "set_type" => "pair",
        "pair_id"=> 2,
        "nbr" => 1
    ],
    2 => [
        "card_name" => clienttranslate('Paddle ball'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 2,
        "set_type" => "pair",
        "pair_id"=> 8,
        "nbr" => 1
    ],
    3 => [
        "card_name" => clienttranslate('Frisbee'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 3,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    0 => [
        "card_name" => clienttranslate('Kite'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 0,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    7 => [
        "card_name" => clienttranslate('Sandcastle'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 7,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    12 => [
        "card_name" => clienttranslate('Starfish'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 12,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    10 => [
        "card_name" => clienttranslate('Seashell'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 10,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 4
    ],
    14 => [
        "card_name" => clienttranslate('Swimmer'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 14,
        "set_type" => "group",
        "set_size" => 4,
        "nbr" => 6
    ],
    1 => [
        "card_name" => clienttranslate('Lifesaver'),
        "card_type" => BLUE_CARD,
        "card_type_arg" => 1,
        "set_type" => "joker",
        "nbr" => 1
    ],

    // Card type IDs 15-18 are not used
    // Action cards start at ID 19
    29 => [
        "card_name" => clienttranslate('Playful puppy'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 10,
        "nbr" => 1
    ],
    37 => [
        "card_name" => clienttranslate('Hurricane'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 18,
        "nbr" => 1
    ],
    27 => [
        "card_name" => clienttranslate('Beach umbrella'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 8,
        "nbr" => 1
    ],
    25 => [
        "card_name" => clienttranslate('Seagull'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 6,
        "nbr" => 1
    ],
    34 => [
        "card_name" => clienttranslate('Hermit crab'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 15,
        "nbr" => 1
    ],
    23 => [
        "card_name" => clienttranslate('Lifeguard'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 4,
        "nbr" => 2
    ],
    20 => [
        "card_name" => clienttranslate('Boat'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 1,
        "nbr" => 1
    ],
    21 => [
        "card_name" => clienttranslate('Fishin\''),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 2,
        "nbr" => 1
    ],
    28 => [
        "card_name" => clienttranslate('Metal detector'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 9,
        "nbr" => 1
    ],
    33 => [
        "card_name" => clienttranslate('Bonfire'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 14,
        "nbr" => 1
    ],
    35 => [
        "card_name" => clienttranslate('Shark attack'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 16,
        "nbr" => 1
    ],
    32 => [
        "card_name" => clienttranslate('Sunburn'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 15,
        "nbr" => 1
    ],
    31 => [
        "card_name" => clienttranslate('Wipeout'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 12,
        "nbr" => 1
    ],
    36 => [
        "card_name" => clienttranslate('The wave'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 17,
        "nbr" => 3
    ],
    22 => [
        "card_name" => clienttranslate('Jetski'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 3,
        "nbr" => 3
    ],
    24 => [
        "card_name" => clienttranslate('Pirate'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 5,
        "nbr" => 1
    ],
    19 => [
        "card_name" => clienttranslate('Beach ball'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 0,
        "nbr" => 1
    ],
    30 => [
        "card_name" => clienttranslate('Treasure chest'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 11,
        "nbr" => 1
    ],
    26 => [
        "card_name" => clienttranslate('Surfer'),
        "card_type" => YELLOW_CARD,
        "card_type_arg" => 7,
        "nbr" => 1
    ],
];





