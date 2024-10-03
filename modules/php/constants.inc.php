<?php

/*
 * State constants
 */
const ST_BGA_GAME_SETUP = 1;

const ST_PLAYER_TURN = 10;
const ST_CHECK_CAN_PUT_DOWN_SET = 11;
const ST_PUT_DOWN_SET = 12;
const ST_NEXT_PLAYER = 13;

const ST_END_GAME = 99;

/*
 * Game actions
 */
const ACT_SURF_TURF = "surfAndTurf";
const ACT_EXCHANGE = "exchangeCard";
const ACT_YELLOW_CARD = "yellowCard";

const ACT_ALLOW_PUT_DOWN_SET = "allowPutDownSet";
const ACT_CANNOT_PUT_DOWN_SET = "cannotPutDownSet";

const ACT_PUT_DOWN_SET = "putDownSet";
const ACT_PASS = "pass";

const ACT_NEXT_PLAYER = "nextPlayer";
const ACT_END_GAME = "endGame";

/*
 * Cards
 */
const BLUE_CARD = 0;
const YELLOW_CARD = 1;
