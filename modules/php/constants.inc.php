<?php

/*
 * State constants
 */
const ST_BGA_GAME_SETUP = 1;

const ST_PLAYER_TURN = 10;
const ST_CHECK_CAN_PUT_DOWN_SET = 11;
const ST_PUT_DOWN_SET = 12;
const ST_NEXT_PLAYER = 13;

const ST_PLAY_ACTION_CARD = 20;

const ST_END_GAME = 99;

/*
 * Game actions
 */
const ACT_SURF_TURF = "surfAndTurf";
const ACT_EXCHANGE = "exchangeCard";
const ACT_YELLOW_CARD = "yellowCard";
const ACT_PLAY_ACTION_CARD = "playActionCard";

const ACT_ALLOW_PUT_DOWN_SET = "allowPutDownSet";
const ACT_CANNOT_PUT_DOWN_SET = "cannotPutDownSet";

const ACT_PUT_DOWN_SET = "putDownSet";
const ACT_PASS = "pass";

const ACT_NEXT_PLAYER = "nextPlayer";
const ACT_END_GAME = "endGame";

/*
 * Cards types
 */
const BLUE_CARD = 0;
const YELLOW_CARD = 1;

/*
 * Card type args
 */
const CARD_FLIP = 5;
const CARD_FLOP = 9;
const CARD_CONE = 13;
const CARD_SCOOP = 11;
const CARD_LEFT_SWIMMIE = 6;
const CARD_RIGHT_SWIMMIE = 4;
const CARD_PADDLE = 8;
const CARD_PADDLE_BALL = 9;
const CARD_FRISBEE = 3;
const CARD_KITE = 0;
const CARD_SANDCASTLE = 7;
const CARD_STARFISH = 12;
const CARD_SEASHELL = 10;
const CARD_SWIMMER = 14;
const CARD_LIFESAVER = 1;

const CARD_PLAYFUL_PUPPY = 10;
const CARD_HURRICANE = 18;
const CARD_BEACH_UMBRELLA = 8;
const CARD_SEAGULL = 6;
const CARD_HERMIT_CRAB = 15;
const CARD_LIFEGUARD = 4;
const CARD_BOAT = 1;
const CARD_FISHIN = 2;
const CARD_METAL_DETECTOR = 9;
const CARD_BONFIRE = 14;
const CARD_SHARK_ATTACK = 16;
const CARD_SUNBURN = 13;
const CARD_WIPEOUT = 12;
const CARD_THE_WAVE = 17;
const CARD_JETSKI = 3;
const CARD_PIRATE = 5;
const CARD_BEACH_BALL = 0;
const CARD_TREASURE_CHEST = 11;
const CARD_SURFER = 7;