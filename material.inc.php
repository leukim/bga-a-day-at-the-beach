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
        "card_name" => clienttranslate('Two hearts'),
        "card_type" => "blue",
        "card_id" => 1,
        "set_type" => "group",
        "set_size" => 2,
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('Three hearts'),
        "card_type" => "blue",
        "card_id" => 2,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 7
    ],
    [
        "card_name" => clienttranslate('Four hearts'),
        "card_type" => "blue",
        "card_id" => 3,
        "set_type" => "group",
        "set_size" => 4,
        "nbr" => 9
    ],
    [
        "card_name" => clienttranslate('Five hearts'),
        "card_type" => "blue",
        "card_id" => 4,
        "set_type" => "group",
        "set_size" => 5,
        "nbr" => 11
    ],
    [
        "card_name" => clienttranslate('Queen of hearts'),
        "card_type" => "blue",
        "card_id" => 10,
        "set_type" => "pair",
        "pair_id" => 13,
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('King of hearts'),
        "card_type" => "blue",
        "card_id" => 11,
        "set_type" => "pair",
        "pair_id" => 12,
        "nbr" => 5
    ],
];





