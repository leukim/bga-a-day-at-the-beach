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
            console.log('adayatthebeachmiquel constructor');
              
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
            console.log( "Starting game setup" );
            
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

            this.discard_counter = new ebg.counter();
            this.discard_counter.create('discard_size');

            if (gamedatas['sizes']['discard'] > 0) {
                this.discard_counter.setValue(gamedatas['sizes']['discard']);
                dojo.attr('discard', 'data-state', 'card');
            }
 
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
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

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: ', stateName, args );
            
            switch( stateName )
            {
                case 'playerTurn':
                    // TODO Review, does not work
                    this.ocean.setSelectionMode(1);
                    this.ocean.unselectAll();
                    this.hand.setSelectionMode(1);
                    this.hand.unselectAll();
                    break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: ', stateName );
            
            switch( stateName )
            {
                case 'playerTurn':
                    this.ocean.setSelectionMode(0);
                    this.hand.setSelectionMode(0);
                    break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: ', stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
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
                }
            }
        },

        onChangeOceanSelection: function(control_name, item_id) {
            if( this.isCurrentPlayerActive() ) {
                console.log("onChangeOceanSelection");
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
        },

        onChangeHandSelection: function(control_name, item_id) {
            if( this.isCurrentPlayerActive() ) {
                console.log("onChangeHandSelection");
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

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */


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
        
        onSurfTurf: function()
        {
            console.log( 'onSurfTurf' );

            this.bgaPerformAction("actSurfTurf");      
        },

        onExchange: function()
        {
            console.log( 'onExchange');

            const ocean_card_id = this.ocean.getSelectedItems()[0].id;
            const hand_card_id = this.hand.getSelectedItems()[0].id;

            this.bgaPerformAction("actExchange", {ocean_card_id, hand_card_id});
        },

        onPlayActionCard: function()
        {
            console.log( 'onPlayActionCard' );

            const card = this.hand.getSelectedItems()[0];

            this.action_cards.play(card);    
        },

        onPutDownSet: function(card_ids) {
            console.log('onPutDownSet', card_ids);

            this.bgaPerformAction("actPutDownSet", {card_ids: card_ids.join(',')});
        },

        onPass: function() {
            console.log('onPass');

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
            console.log( 'notifications subscriptions setup' );

            dojo.subscribe('cardToOcean', this, "notif_cardToOcean");
            this.notifqueue.setSynchronous('cardToOcean', 500);
            dojo.subscribe('cardToHand', this, "notif_cardToHand");
            dojo.subscribe('cardToPlayer', this, "notif_cardToPlayer");
            dojo.subscribe('exchange', this, 'notif_exchange');
            dojo.subscribe('discard', this, 'notif_discard');
            dojo.subscribe('increaseScore', this, 'notif_increaseScore');
            dojo.subscribe('shuffle', this, 'notif_shuffle');
            dojo.subscribe('playYellowCard', this, 'notif_playYellowCard');
            dojo.subscribe('takeFromOcean', this, 'notif_takeFromOcean');
        },  


        notif_cardToOcean: function(notif) {
            console.log('notif_cardToOcean');
            
            const card = notif.args.card;
            this.ocean.addToStockWithId(this.getTypeFromCard(card), card.id, 'deck');
            this.deck_counter.incValue(-1);
        },

        notif_cardToHand: function(notif) {
            console.log('notif_cardToHand');
            
            const card = notif.args.card;
            this.hand.addToStockWithId(this.getTypeFromCard(card), card.id, 'deck');
            this.deck_counter.incValue(-1);
        },

        notif_cardToPlayer: function(notif) {
            console.log('nofid_cardToPlayer');

            const card = this.getCardBackId();
            const player_id = notif.args.player_id;
            document.getElementById('deck_panel').insertAdjacentHTML('beforeend', '<div id="flip_card" class="deck"></div>');
            this.placeOnObject('deck_panel', 'flip_card');
            this.slideToObjectAndDestroy('flip_card', 'overall_player_board_'+player_id, 1000);
            this.deck_counter.incValue(-1);
        },

        notif_exchange: function(notif) {
            console.log('notif_exchange', notif);

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

        notif_discard: function(notif) {
            console.log('notif_discard', notif);

            const card_ids = notif.args.card_ids_to_discard;
            const from_player_id = notif.args.from_player_id;

            for (var key in card_ids) {
                const card_id = card_ids[key];

                if (this.player_id === from_player_id) {
                    // Move from hand to discard
                    var animation = this.hand.removeFromStockById(card_id, 'discard');
                    console.log("animation", animation);
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
            this.discard_counter.incValue(card_ids.length);
        },

        notif_increaseScore: function(notif) {
            this.scoreCtrl[ notif.args.player_id ].incValue(1);
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

            this.discard_counter.toValue(0);
            this.deck_counter.toValue(notif.args.deck_size);
        },

        notif_playYellowCard: function(notif) {
            if (this.player_id == notif.args.player_id) {
                this.hand.removeFromStockById(notif.args.yellow_card_id, 'discard');
                dojo.attr('discard', 'data-state', 'card');
            } else {
                var animation_id = this.slideTemporaryObject(
                    '<div id="flip_card" class="deck"></div>',
                    'discard',
                    `overall_player_board_${notif.args.player_id}`,
                    'discard'
                ).play();
                dojo.connect(animation_id, 'onEnd', () => {
                    dojo.attr('discard', 'data-state', 'card');
                });
            }

            this.discard_counter.incValue(1);
        },

        notif_takeFromOcean: function(notif) {
            console.log('notif_takeFromOcean', notif.args);

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

            
        }

   });             
});
