/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * ADayAtTheBeachMiquel implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * adayatthebeachmiquel.js
 *
 * ADayAtTheBeachMiquel user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock",
    "./modules/js/actionCards"
],
function (dojo, declare) {
    return declare("bgagame.adayatthebeachmiquel", ebg.core.gamegui, {
        constructor: function(){
            this.cards_per_row = 19;
            this.card_types = 2;

            this.action_cards = new ActionCards(this);
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas ) {
            this.ocean = new ebg.stock();
            this.ocean.create(this, $('ocean'), 81, 117);
            this.ocean.image_items_per_row = this.cards_per_row;
            this.ocean.setSelectionMode(0);
            dojo.connect(this.ocean, 'onChangeSelection', this, 'onChangeOceanSelection');

            this.hand = new ebg.stock();
            this.hand.create(this, $('hand'), 81, 117);
            this.hand.image_items_per_row = this.cards_per_row;
            this.hand.setSelectionMode(0);
            dojo.connect(this.hand, 'onChangeSelection', this, 'onChangeHandSelection');

            // Create cards:
            for (var type = 0; type < this.card_types; type++) {

                for (var value = 0; value < this.cards_per_row; value++) {
                    var card_type_id = this.getTypeFromCoords(type, value);
                    this.ocean.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/cards.png', card_type_id);
                    this.hand.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/cards.png', card_type_id);
                }
            }

            for (var id in gamedatas['ocean']) {
                const card = gamedatas['ocean'][id];

                this.addCardToOcean(card);
            }

            for (var id in gamedatas['hand']) {
                const card = gamedatas['hand'][id];

                this.addCardToHand(card);
            }

            this.deck_counter = new ebg.counter();
            this.deck_counter.create('deck_size');

            this.deck_counter.setValue(gamedatas['sizes']['deck']);

            this.discard = gamedatas['discard'];
            this.discard_counter = new ebg.counter();
            this.discard_counter.create('discard_size');

            if (gamedatas['discard'].length > 0) {
                this.discard_counter.setValue(gamedatas['discard'].length);
                dojo.attr('discard', 'data-state', 'card');
            }

            this.hand_counters = {};

            // Setting up player boards
            for( var player_id in gamedatas.players ) {
                // var player = gamedatas.players[player_id];
                            
                this.getPlayerPanelElement(player_id).innerHTML =
                    `<div style="padding: 5px;">
                        <span class="deck_mini"></span>
                        <span id="card_count_${player_id}"></span>
                    </div>`;

                this.hand_counters[player_id] = new ebg.counter();
                this.hand_counters[player_id].create(`card_count_${player_id}`);
            }

            for (const key in gamedatas['sizes']['players']) {
                const player_info = gamedatas['sizes']['players'][key];
                this.hand_counters[player_info.player_id].setValue(player_info.size);
            }
 
            this.setupNotifications();
        },
       
        // Get card type by row and column (x, y) 0-based
        getTypeFromCoords : function(x, y) {
            return (parseInt(x) * this.cards_per_row) + parseInt(y);
        },

        getTypeFromCard: function(card) {
            return this.getTypeFromCoords(card.type, card.type_arg)
        },

        getCardBackId: function () {
            return this.getTypeFromCoords(0, 4);
        },

        addCardToOcean: function(card) {
            this.ocean.addToStockWithId(this.getTypeFromCard(card), card.id);
        },

        addCardToHand: function(card) {
            this.hand.addToStockWithId(this.getTypeFromCard(card), card.id);
        },

        addImageActionButton: function(id, div, handler, bcolor, tooltip) {
            if (typeof bcolor == "undefined") {
                bcolor = "gray";
            }
            // this will actually make a transparent button id color = gray
            this.addActionButton(id, div, handler, null, false, bcolor);
            // remove border, for images it better without
            dojo.style(id, "border", "none");
            // but add shadow style (box-shadow, see css)
            dojo.addClass(id, "shadow bgaimagebutton");
            // you can also add additional styles, such as background
            if (tooltip) {
                dojo.attr(id, "title", tooltip);
            }
            return $(id);
        },

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args ) {
            console.log( 'Entering state: ', stateName, args );

            this.stateName = stateName;
            
            switch( stateName )
            {
                case 'playerTurn':
                    this.ocean.setSelectionMode(1);
                    this.ocean.unselectAll();
                    this.hand.setSelectionMode(1);
                    this.hand.unselectAll();
                    break;
                case 'client_playerPicksBlueCardsFromOcean':
                    this.ocean.setSelectionMode(2);
                    this.ocean.unselectAll();
                    break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName ) {
            console.log( 'Leaving state: ', stateName );
            
            switch( stateName )
            {
                case 'playerTurn':
                    this.ocean.setSelectionMode(0);
                    this.hand.setSelectionMode(0);
                    break;
                case 'client_playerPicksBlueCardsFromOcean':
                    this.ocean.setSelectionMode(0);
                    this.ocean.unselectAll();
                    break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args ) {
            if( this.isCurrentPlayerActive() )
            {            
                const discard = this.clientStateArgs?.discard;
                switch( stateName )
                {
                    case 'playerTurn':    
                        this.addActionButton('actSurfTurf-btn', _('Surf and Turf'), () => this.onSurfTurf());
                        if (this.deck_counter.getValue() + this.discard_counter.getValue() < 2) {
                            dojo.addClass('actSurfTurf-btn', 'disabled');
                        }
                        this.addActionButton('actExchange-btn', _('Exchange'), () => this.onExchange());
                        dojo.addClass('actExchange-btn', 'disabled');
                        this.addActionButton('actYellowCard-btn', _('Play action card'), () => this.onPlayActionCard());
                        dojo.addClass('actYellowCard-btn', 'disabled');
                        break;
                    case 'putDownSet':
                        for (var id in args) {
                            const set = args[id];
                            this.addActionButton(
                                'actPutDownSet'+set.name+'-btn',
                                dojo.string.substitute(_("Put down ${setName}"), {setName: set.name}),
                                () => this.onPutDownSet(set.card_ids)
                            );
                        }

                        this.addActionButton('actPass-btn', _('Pass'), () => this.onPass());
                        break;
                    // Action card client states
                    case 'client_playerPicksActionCardFromOcean':
                        const ocean = this.ocean.getAllItems();
                        for (var key in ocean) {
                            const card = ocean[key];
                            if (card.type >= 19) {
                                this.addImageActionButton(
                                    `client_selectCard${card.id}-btn`,
                                    `<div class="card card_${card.type}"></div>`,
                                    () => this.playBoat(card)
                                );
                            }
                        }
                        this.addActionButton('cancelClientState-btn', _('Cancel'), () => this.restoreServerGameState());
                        break;
                    case 'client_playerPicksPlayerToTradeHands':
                        for (const target_player_id in this.gamedatas.players) {
                            if (target_player_id != this.player_id) {
                                const playerInfo = this.gamedatas.players[target_player_id];
                                // const color = playerInfo.color;
                                const name = playerInfo.name;
                                this.addActionButton(
                                    `client_selectPlayer_${target_player_id}-btn`,
                                    `${name} - ${target_player_id}`,
                                    () => this.pirate(target_player_id)
                                );
                            }
                        }
                        break;
                    case 'client_playerPicksBlueCardsFromOcean':
                        this.addActionButton('pickBlueCards-btn', _('Pick cards'), () => this.jetskiOrWave());
                        break;
                    case 'client_playerPicksBlueCardsFromDiscard':
                        if (this.clientStateArgs.picked.length < 2) {
                            for (var key in discard) {
                                const card = discard[key];
                                const card_type = this.getTypeFromCard(card);
                                if (card.type <= 19) {
                                    this.addImageActionButton(
                                        `client_selectCard${card.id}-btn`,
                                        `<div class="card card_${card_type}"></div>`,
                                        () => this.processTreasureChest(card)
                                    );
                                }
                            }
                        }
                        this.addActionButton('confirmPickFromDiscard-btn', _('Confirm picked cards'), () => this.confirmTreasureChest());
                        this.addActionButton('cancelClientState-btn', _('Cancel'), () => this.restoreServerGameState());
                        break;
                    case 'client_playerPicksCardFromDiscard':
                        for (var key in discard) {
                            const card = discard[key];
                            const card_type = this.getTypeFromCard(card);
                            this.addImageActionButton(
                                `client_selectCard${card.id}-btn`,
                                `<div class="card card_${card_type}"></div>`,
                                () => this.metalDetector(card)
                            );
                        }
                        this.addActionButton('cancelClientState-btn', _('Cancel'), () => this.restoreServerGameState());
                        break;
                    // Action card states
                    case 'playActionCard':
                        const card = args['card'];
                        const card_type = this.getTypeFromCard(card);
                        this.addImageActionButton(
                            `client_selectCard${card['id']}-btn`,
                            `<div class="card card_${card_type}"></div>`,
                            () => this.playActionCard(card)
                        );
                        // this.addActionButton('cancelClientState-btn', _('Cancel'), () => this.restoreServerGameState());
                        break;
                }
            }
        },

        /**
         * Virtual states handlers
         */

        playBoat: function(card) {
            this.action_cards.play(this.clientStateArgs.card, card);
        },

        pirate: function(target_id) {
            this.action_cards.play(this.clientStateArgs.card, target_id);
        },

        jetskiOrWave: function() {
            this.action_cards.play(this.clientStateArgs.card, this.ocean.getSelectedItems());
        },

        processTreasureChest: function(card) {
            this.clientStateArgs.discard = this.clientStateArgs.discard.filter((el) => el.id != card.id);
            this.clientStateArgs.picked.push(card);
            dojo.addClass(`client_selectCard${card.id}-btn`, 'disabled');
        },

        confirmTreasureChest: function() {
            this.action_cards.play(this.clientStateArgs.card, this.clientStateArgs.picked);
        },

        metalDetector: function(card) {
            this.action_cards.play(this.clientStateArgs.card, card);
        },

        playActionCard: function(card) {
            this.action_cards.play({id: card.id, type: this.getTypeFromCard(card)});
        },

        /**
         * Selection change handlers
         */

        onChangeOceanSelection: function(control_name, item_id) {
            if( this.isCurrentPlayerActive() ) {
                if (this.stateName == 'client_playerPicksBlueCardsFromOcean') {
                    if (this.checkCardsForJetski()) {
                        dojo.removeClass('pickBlueCards-btn', 'disabled');
                    } else {
                        dojo.addClass('pickBlueCards-btn', 'disabled');
                    }
                } else {
                    const ocean_selected = this.ocean.getSelectedItems();
                    const hand_selected = this.hand.getSelectedItems();
                    if (
                        ocean_selected !== undefined && hand_selected !== undefined &&
                        ocean_selected.length === 1 && hand_selected.length === 1
                    ) {
                        dojo.removeClass('actExchange-btn', 'disabled');
                    } else {
                        dojo.addClass('actExchange-btn', 'disabled');
                    }
                }
            }
        },

        onChangeHandSelection: function(control_name, item_id) {
            if( this.isCurrentPlayerActive() ) {
                const ocean_selected = this.ocean.getSelectedItems();
                const hand_selected = this.hand.getSelectedItems();
                if (
                    ocean_selected !== undefined && hand_selected !== undefined &&
                    ocean_selected.length === 1 && hand_selected.length === 1
                ) {
                    dojo.removeClass('actExchange-btn', 'disabled');
                } else {
                    dojo.addClass('actExchange-btn', 'disabled');
                }

                if (hand_selected !== undefined && hand_selected.length === 1 && hand_selected[0].type >= 19) {
                    dojo.removeClass('actYellowCard-btn', 'disabled');
                } else {
                    dojo.addClass('actYellowCard-btn', 'disabled');
                }
            }
        },

        restoreServerGameState: function() {
            this.setClientState(this.clientStateArgs.returnTo);
        },

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

        checkCardsForJetski: function() {
            const selected = this.ocean.getSelectedItems();

            if (selected.length > 2) return false;

            for (const key in selected) {
                if (selected[key].type > 19) return false;
            }

            return true;
        },


        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        // Example:
        
        onSurfTurf: function() {
            this.bgaPerformAction("actSurfTurf");      
        },

        onExchange: function() {
            const ocean_card_id = this.ocean.getSelectedItems()[0].id;
            const hand_card_id = this.hand.getSelectedItems()[0].id;

            this.bgaPerformAction("actExchange", {ocean_card_id, hand_card_id});
        },

        onPlayActionCard: function() {
            const card = this.hand.getSelectedItems()[0];

            this.action_cards.play(card);    
        },

        onPutDownSet: function(card_ids) {
            this.bgaPerformAction("actPutDownSet", {card_ids: card_ids.join(',')});
        },

        onPass: function() {
            this.bgaPerformAction('actPass');
        },
        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your adayatthebeachmiquel.game.php file.
        
        */
        setupNotifications: function()
        {
            const notifs = [
                ['bonfire', 0],
                ['cardsToOcean', 0],
                ['cardToHand', 0], // TODO Rename to "fromDeck" // TODO unify 3 notifs
                ['cardToHandFromDiscard', 0],
                ['cardToHandFromOcean', 0],
                ['cardToOcean', 500],
                ['cardToPlayer', 0],
                ['discard', 0],
                ['discardHand', 0],
                ['discardOcean', 0],
                ['exchange', 0],
                ['getCardsFrom', 0],
                ['increaseScore', 0],
                ['others_takeFromDiscard', 0],
                ['others_takeFromOcean', 0],
                ['pickCards', 500],
                ['playBoat', 0],
                ['playYellowCard', 0],
                ['shuffle', 0],
                ['takeFromOcean', 0],
                ['tradeHands', 0],
            ];

            notifs.forEach((notif) => {
                dojo.subscribe(notif[0], this, `notif_${notif[0]}`);
                if (notif[1] > 0) {
                    this.notifqueue.setSynchronous(notif[0], notif[1]);
                }
            });
        },

        notif_bonfire: function(notif) {
            if (this.player_id != notif.args.player_id) {
                var animation_id = this.slideTemporaryObject(
                    `<div id="flip_card" class="deck"></div>`,
                    'discard',
                    `overall_player_board_${notif.args.player_id}`,
                    'discard',
                    1250
                ).play();
                dojo.connect(animation_id, 'onEnd', () => {
                    dojo.attr('discard', 'data-state', 'card');
                });
            }

            this.deck_counter.incValue(-4);
            console.log("len", notif.args.discards.length);
            this.discard_counter.incValue(notif.args.discards.length);
            for (var key in notif.args.discards) {
                var discard = notif.args.discards[key];
                this.discard.push(discard);
            }
            this.hand_counters[notif.args.player_id].setValue(4);
        },

        notif_cardsToOcean: function(notif) {
            const cards = notif.args.cards;
            for (const key in cards) {
                const card = cards[key];
                this.ocean.addToStockWithId(this.getTypeFromCard(card), card.id, 'deck');
            }
            
            this.deck_counter.incValue(-cards.length);
        },

        notif_cardToHand: function(notif) {
            const card = notif.args.card;
            this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, 'deck');
            this.deck_counter.incValue(-1);
            this.hand_counters[this.player_id].incValue(1);
        },

        notif_cardToHandFromDiscard: function(notif) {
            const card = notif.args.card;
            this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, 'discard');
            this.discard_counter.incValue(-1);
            this.hand_counters[this.player_id].incValue(1);
        },

        notif_cardToHandFromOcean: function(notif) {
            const card = notif.args.card;
            this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, 'ocean');
            this.hand_counters[this.player_id].incValue(1);
        },

        notif_cardToOcean: function(notif) {
            const card = notif.args.card;
            this.ocean.addToStockWithId(this.getTypeFromCard(card), card.id, 'deck');
            this.deck_counter.incValue(-1);
        },

        notif_cardToPlayer: function(notif) {
            const player_id = notif.args.player_id;
            document.getElementById('deck_panel').insertAdjacentHTML('beforeend', '<div id="flip_card" class="deck"></div>');
            this.placeOnObject('deck_panel', 'flip_card');
            this.slideToObjectAndDestroy('flip_card', 'overall_player_board_'+player_id, 1000);
            this.deck_counter.incValue(-1);
            this.hand_counters[player_id].incValue(1);
        },

        notif_discard: function(notif) {
            const card_ids = notif.args.card_ids_to_discard;
            const from_player_id = notif.args.from_player_id;

            for (var key in card_ids) {
                const card_id = card_ids[key];

                if (this.player_id === from_player_id) {
                    // Move from hand to discard
                    this.hand.removeFromStockById(card_id, 'discard');
                    dojo.attr('discard', 'data-state', 'card');
                } else {
                    var animation_id = this.slideTemporaryObject(
                        `<div id="flip_card" class="deck"></div>`,
                        `overall_player_board_${from_player_id}`,
                        `overall_player_board_${from_player_id}`,
                        'discard'
                    ).play();
                    dojo.connect(animation_id, 'onEnd', () => {
                        dojo.attr('discard', 'data-state', 'card');
                    });
                }
                
            }

            for (var key in notif.args.discards) {
                this.discard.push(notif.args.discards[key]);
            }
            this.discard_counter.incValue(card_ids.length);
            this.hand_counters[from_player_id].incValue(-card_ids.length);
        },

        notif_discardHand: function(notif) {
            this.hand.removeAllTo('discard');
        },

        notif_discardOcean: function(notif) {
            const nbr = this.ocean.count();
            this.ocean.removeAllTo('discard');
            this.discard_counter.incValue(nbr);
        },
        
        notif_exchange: function(notif) {
            const player_id = notif.args.player_id;

            const card_to_player = notif.args.card_to_player;
            const card_to_ocean = notif.args.card_to_ocean;

            const card_to_player_type = this.getTypeFromCard(card_to_player);
            const card_to_ocean_type = this.getTypeFromCard(card_to_ocean);

            if (this.player_id === notif.args.player_id) {
                this.hand.addToStockWithId(card_to_player_type, card_to_player.id, `ocean_item_${card_to_player.id}`);
                this.ocean.removeFromStockById(card_to_player.id);

                this.ocean.addToStockWithId(card_to_ocean_type, card_to_ocean.id, `hand_item_${card_to_ocean.id}`);
                this.hand.removeFromStockById(card_to_ocean.id);
            } else {
                this.ocean.removeFromStockById(card_to_player.id, `overall_player_board_${player_id}`);
                this.ocean.addToStockWithId(card_to_ocean_type, card_to_ocean.id, `overall_player_board_${notif.args.player_id}`);
            }
        },

        notif_getCardsFrom: function(notif) {
            this.hand.removeAllTo(`overall_player_board_${notif.args.player_id}`);

            for (const key in notif.args.cards) {
                const card = notif.args.cards[key];

                this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, `overall_player_board_${notif.args.player_id}`);
            }
        },

        notif_increaseScore: function(notif) {
            this.scoreCtrl[ notif.args.player_id ].incValue(1);
        },

        notif_others_takeFromDiscard: function(notif) {
            if (this.player_id != notif.args.player_id) {
                var animation_id = this.slideTemporaryObject(
                    `<div id="flip_card" class="deck"></div>`,
                    'discard',
                    `overall_player_board_${notif.args.player_id}`,
                    'discard',
                    1250
                ).play();
                dojo.connect(animation_id, 'onEnd', () => {
                    this.discard_counter.incValue(-notif.args.nbr);
                    if (this.discard_counter.getValue() == 0) {
                        dojo.attr('discard', 'data-state', 'empty');
                    }
                });

                this.hand_counters[notif.args.player_id].incValue(notif.args.nbr);
            }
        },

        notif_others_takeFromOcean: function(notif) {
            if (this.player_id != notif.args.player_id) {
                this.ocean.removeFromStockById(notif.args.card_id, `overall_player_board_${notif.args.player_id}`);
                this.hand_counters[notif.args.player_id].incValue(1);
            }
        },

        notif_pickCards: function(notif) {
            for (key in notif.args.picked_cards) {
                const card = notif.args.picked_cards[key];
                this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, 'deck');
            }
        },

        notif_playBoat: function(notif) {
            this.ocean.removeFromStockById(notif.args.boat_target_id, 'discard');
            this.discard.push(notif.args.boat_card);
            this.discard_counter.incValue(1);
        },

        notif_playYellowCard: function(notif) {
            if (this.player_id == notif.args.player_id) {
                this.hand.removeFromStockById(notif.args.yellow_card_id, 'discard');
                dojo.attr('discard', 'data-state', 'card');
            } else {
                var animation_id = this.slideTemporaryObject(
                    `<div id="flip_card" class="card card_${notif.args.yellow_card_type_id}"></div>`,
                    'discard',
                    `overall_player_board_${notif.args.player_id}`,
                    'discard',
                    1000
                ).play();
                dojo.connect(animation_id, 'onEnd', () => {
                    dojo.attr('discard', 'data-state', 'card');
                });
            }

            this.discard.push(notif.args.yellow_card);
            this.discard_counter.incValue(1);
            this.hand_counters[notif.args.player_id].incValue(-1);
        },

        notif_shuffle: function(notif) {
            var animation_id = this.slideTemporaryObject(
                `<div id="flip_card" class="deck"></div>`,
                `discard`,
                `discard`,
                'deck'
            ).play();
            dojo.connect(animation_id, 'onEnd', () => {
                dojo.attr('discard', 'data-state', 'empty');
            });

            while(this.discard.length > 0) this.discard.pop(); 
            this.discard_counter.toValue(0);
            this.deck_counter.toValue(notif.args.deck_size);
        },

        notif_takeFromOcean: function(notif) {
            const taken_cards = notif.args.taken_cards;

            for (var key in taken_cards) {
                var card = taken_cards[key];

                if (this.player_id == notif.args.player_id) {
                    this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, `ocean_item_${card.id}`);
                    this.ocean.removeFromStockById(card.id);
                } else {
                    this.ocean.removeFromStockById(card.id, `overall_player_board_${notif.args.player_id}`);
                }
            }
            
            this.hand_counters[notif.args.player_id].incValue(taken_cards.length);
        },

        notif_tradeHands: function(notif) {
            this.hand_counters[notif.args.player_id_1].setValue(notif.args.player_nbr_1);
            this.hand_counters[notif.args.player_id_2].setValue(notif.args.player_nbr_2);
        },

        
   });             
});
