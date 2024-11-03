/*
* Card type IDs
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

const CARD_PLAYFUL_PUPPY = 29;
const CARD_HURRICANE = 37;
const CARD_BEACH_UMBRELLA = 27;
const CARD_SEAGULL = 25;
const CARD_HERMIT_CRAB = 34;
const CARD_LIFEGUARD = 23;
const CARD_BOAT = 20;
const CARD_FISHIN = 21;
const CARD_METAL_DETECTOR = 28;
const CARD_BONFIRE = 33;
const CARD_SHARK_ATTACK = 35;
const CARD_SUNBURN = 32;
const CARD_WIPEOUT = 31;
const CARD_THE_WAVE = 36;
const CARD_JETSKI = 22;
const CARD_PIRATE = 24;
const CARD_BEACH_BALL = 19;
const CARD_TREASURE_CHEST = 30;
const CARD_SURFER = 26;

class ActionCards {
    constructor(table) {
        this.table = table;
    }

    play(card, target = null) {
        var payload = {};

        switch(card.type) {
            case CARD_PLAYFUL_PUPPY:
            case CARD_HURRICANE:
            case CARD_BEACH_UMBRELLA:
            case CARD_SEAGULL:
            case CARD_HERMIT_CRAB:
            case CARD_LIFEGUARD:
                payload = {card_id: card.id};
                break;
            case CARD_BOAT:
                if (target === null) {
                    this._showBoatDialog(card);  // If initial boat selection we will only show the dialog
                    return;
                }
                // Boat target selected, send action
                payload = {card_id: card.id, target_id: target.id}
                break;
            case CARD_BONFIRE:
                payload = {card_id: card.id};
                break;
            case CARD_PIRATE:
                if (target === null) {
                    this._showPirateDialog(card);
                    return;
                }
                payload = {card_id: card.id, target_id: target};
                break;
            case CARD_JETSKI:
                if (target === null || target.length == 0) {
                    this._showJetskiDialog(card);
                    return;
                }
                payload = {card_id: card.id, target: [target[0].id, target[1].id]}
            case CARD_THE_WAVE:
                if (target === null || target.length == 0) {
                    this._showTheWaveDialog(card);
                    return;
                }
                payload = {card_id: card.id, target: [target[0].id, target[1].id]}
        }

        this.table.bgaPerformAction("actYellowCard", {payload: JSON.stringify(payload)}); 
    }

    _showBoatDialog(card) {
        this.table.clientStateArgs = {
            card,
        };
        this.table.setClientState("client_playerPicksActionCardFromOcean", {
            descriptionmyturn : _("${you} must pick an action card from the ocean to play"),
        });
    }

    _showPirateDialog(card) {
        this.table.clientStateArgs = {
            card,
        };
        this.table.setClientState("client_playerPicksPlayerToTradeHands", {
            descriptionmyturn : _("${you} must pick another player to trade hands"),
        });
    }

    _showJetskiDialog(card) {
        this.table.clientStateArgs = {
            card,
        };
        this.table.setClientState("client_playerPicksBlueCardsFromOcean", {
            descriptionmyturn : _("${you} can pick up to two blue cards from the ocean"),
        });
    }

    _showTheWaveDialog(card) {
        this.table.clientStateArgs = {
            card,
        };
        this.table.setClientState("client_playerPicksBlueCardsFromOcean", {
            descriptionmyturn : _("${you} can pick up to two blue cards from the ocean. The rest will be discarded."),
        });
    }
}

define(["dojo"],
    {
        ActionCards
    }
);