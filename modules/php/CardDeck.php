<?php 

class CardDeck {
    private $cards;

    public function __construct($deck) {
        $this->cards = $deck;
        $this->cards->init( "card" );
    }

    public function init($card_types, $players): void {
        $cards = [];
        foreach($card_types as $card_type) {
            $cards[] = [ 'type' => $card_type['card_type'], 'type_arg' => $card_type['card_id'], 'nbr' => $card_type['nbr']];
        }

        $this->cards->createCards( $cards, 'deck' );

        $this->cards->moveAllCardsInLocation( null, "deck" );
        $this->cards->shuffle("deck");

        foreach( $players as $player_id => $player )
        {
            $cards = $this->cards->pickCards( 4, 'deck', $player_id );
        }
        $this->cards->pickCardsForLocation(4, 'deck', 'ocean');
    }

    public function getOcean() {
        return $this->cards->getCardsInLocation('ocean');
    }

    public function getHand($player_id) {
        return $this->cards->getCardsInLocation( 'hand', $player_id );
    }

    public function pickCardToOcean() {
        return $this->cards->pickCardForLocation('deck', 'ocean');
    }

    public function pickCardToHand($player_id) {
        return $this->cards->pickCardForLocation('deck', 'hand', $player_id);
    }
}