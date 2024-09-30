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
        "card_name" => clienttranslate('Kite'),
        "card_type" => "blue",
        "card_id" => 1,
        "set_type" => "group",
        "set_size" => 3,
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('Lifesaver'),
        "card_type" => "blue",
        "card_id" => 2,
        "set_type" => "joker",
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('Four hearts'),
        "card_type" => "blue",
        "card_id" => 3,
        "set_type" => "group",
        "set_size" => 4,
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('Paddle ball'),
        "card_type" => "blue",
        "card_id" => 4,
        "set_type" => "pair",
        "pair_id" => 999,
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('Frisbee'),
        "card_type" => "blue",
        "card_id" => 10,
        "set_type" => "group",
        "set_size"=> 3,
        "nbr" => 5
    ],
    [
        "card_name" => clienttranslate('Right swimmie'),
        "card_type" => "blue",
        "card_id" => 11,
        "set_type" => "pair",
        "pair_id" => 999,
        "nbr" => 5
    ],
];





