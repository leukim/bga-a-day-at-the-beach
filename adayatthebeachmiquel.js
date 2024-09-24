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
    "ebg/stock"
],
function (dojo, declare) {
    return declare("bgagame.adayatthebeachmiquel", ebg.core.gamegui, {
        constructor: function(){
            console.log('adayatthebeachmiquel constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;
            this.card_types = {
                'blue': 0,
                'yellow': 3
            };

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
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            
            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
            }
            
            this.ocean = new ebg.stock();
            this.ocean.create(this, $('ocean'), 81, 117);
            this.ocean.image_items_per_row = 13;
            this.ocean.setSelectionMode(1);
            dojo.connect(this.ocean, 'onChangeSelection', this, 'onChangeOceanSelection');

            this.hand = new ebg.stock();
            this.hand.create(this, $('hand'), 81, 117);
            this.hand.image_items_per_row = 13;
            this.hand.setSelectionMode(1);
            dojo.connect(this.hand, 'onChangeSelection', this, 'onChangeHandSelection');

            // Create cards:
            for (var type in this.card_types) {
                const type_id = this.card_types[type];

                for (var value = 1; value < 13; value++) {
                    // Build card type id
                    var card_type_id = this.getCardUniqueId(type_id, value);
                    this.ocean.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/tmp_cards.gif', card_type_id);
                    this.hand.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/tmp_cards.gif', card_type_id);
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
 
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },
       
        // Get card unique identifier by row and column (x, y) 0-based
        getCardUniqueId : function(x, y) {
            return (parseInt(x) * 13) + parseInt(y);
        },

        getCardId: function(card) {
            const card_type = this.card_types[card.type];
            return this.getCardUniqueId(card_type, card.type_arg)
        },

        addCardToOcean: function(card) {
            this.ocean.addToStock(this.getCardId(card));
        },

        addCardToHand: function(card) {
            this.hand.addToStock(this.getCardId(card));
        },

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName, args );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName, args );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                 case 'playerTurn':    
                    this.addActionButton('actSurfTurf-btn', _('Surf and Turf'), () => this.onSurfTurf());
                    this.addActionButton('actExchange-btn', _('Exchange'), () => this.onExchange());
                    dojo.addClass('actExchange-btn', 'disabled');
                    this.addActionButton('actYellowCard-btn', _('Play action card'), () => this.onPlayActionCard());
                    dojo.addClass('actYellowCard-btn', 'disabled');

                    break;
                }
            }
        },

        onChangeOceanSelection: function(control_name, item_id) {
            console.log("onChangeOceanSelection", control_name, item_id);
            const ocean_selected = this.ocean.getSelectedItems();
            const hand_selected = this.ocean.getSelectedItems();
            if (
                ocean_selected !== undefined && hand_selected !== undefined &&
                ocean_selected.length === 1 && hand_selected.length === 1
            ) {
                dojo.removeClass('actExchange-btn', 'disabled');
            } else {
                dojo.addClass('actExchange-btn', 'disabled');
            }
        },

        onChangeHandSelection: function(control_name, item_id) {
            console.log("onChangeHandSelection", control_name, item_id);
            const ocean_selected = this.ocean.getSelectedItems();
            const hand_selected = this.ocean.getSelectedItems();
            if (
                ocean_selected !== undefined && hand_selected !== undefined &&
                ocean_selected.length === 1 && hand_selected.length === 1
            ) {
                dojo.removeClass('actExchange-btn', 'disabled');
            } else {
                dojo.addClass('actExchange-btn', 'disabled');
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
            console.log( 'onExchange' );

            this.bgaPerformAction("actExchange"); // TODO Add parameters
        },

        onPlayActionCard: function()
        {
            console.log( 'onPlayActionCard' );

            this.bgaPerformAction("actPlayActionCard");        
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
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 

            dojo.subscribe('cardToOcean', this, "notif_cardToOcean");
            dojo.subscribe('cardToHand', this, "notif_cardToHand");
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods

        
        notif_cardToOcean: function( notif )
        {
            console.log( 'notif_cardToOcean' );
            // console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            const card = notif.args.card;
            this.ocean.addToStock(this.getCardId(card), 'deck');
        },

        notif_cardToHand: function( notif )
        {
            console.log( 'notif_cardToHand' );
            //console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            const card = notif.args.card;
            this.hand.addToStock(this.getCardId(card), 'deck');
        },

   });             
});
