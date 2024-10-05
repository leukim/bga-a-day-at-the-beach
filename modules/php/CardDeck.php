<?php 

class CardDeck {
    private $cards;
    private $game;

    public function __construct($deck, $game) {
        $this->cards = $deck;
        $this->cards->init( "card" );
        $this->game = $game;
    }

    public function init($card_types, $players): void {

        $cards = [];
        foreach($card_types as $id =>$card_type) {
            $cards[] = [ 'type' => $card_type['card_type'], 'type_arg' => $card_type['card_type_arg'], 'nbr' => $card_type['nbr']];
        }

        $this->cards->createCards( $cards, 'deck' );

        $this->cards->moveAllCardsInLocation( null, 'deck');
        $this->cards->shuffle('deck');

        foreach( $players as $player_id => $player )
        {
            $cards = $this->cards->pickCards( 4, 'deck', $player_id );
        }
        $this->cards->pickCardsForLocation(4, 'deck', 'ocean');
    }

    private function checkCanPickCard() {
        if ($this->deckSize() == 0) {
            $this->cards->moveAllCardsInLocation('discard', 'deck');
            $this->cards->shuffle('deck');
            $this->game->on_deck_autoreshuffle($this->cards->countCardsInLocation('deck'));
        }
    }

    public function getOcean() {
        return $this->cards->getCardsInLocation('ocean');
    }

    public function getHand($player_id) {
        return $this->cards->getCardsInLocation( 'hand', $player_id );
    }

    public function pickCardToOcean() {
        $this->checkCanPickCard();
        return $this->cards->pickCardForLocation('deck', 'ocean');
    }

    public function pickCardToHand($player_id) {
        $this->checkCanPickCard();
        return $this->cards->pickCardForLocation('deck', 'hand', $player_id);
    }

    public function cardToPlayer($card_id, $player_id) {
        $this->cards->moveCard($card_id, 'hand', $player_id);
        return $this->cards->getCard($card_id);
    }

    public function cardToOcean($card_id) {
        $this->cards->moveCard($card_id, 'ocean');
        return $this->cards->getCard($card_id);
    }

    public function putDownSet($player_id, $card_ids) {
        $hand = $this->getHand($player_id);

        foreach ($hand as $card) {
            if (in_array($card['id'], $card_ids)) {
                $this->cards->playCard($card['id']);
            }
        }
    }

    public function deckSize() {
        return $this->cards->countCardsInLocation('deck');
    }

    public function discardSize() {
        return $this->cards->countCardsInLocation('discard');
    }
}