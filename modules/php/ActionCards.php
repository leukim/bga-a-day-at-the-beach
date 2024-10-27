<?php

class ActionCards {
    private $game;

    public function __construct($game) {
        $this->game = $game;
    }

    public function playCard($player_id, $card_id, $target_id) {
        $card = $this->game->deck->getCard($card_id);

        if ($card['type'] == YELLOW_CARD) {
            switch ($card['type_arg']) {
                case CARD_PLAYFUL_PUPPY:
                    $this->takeFromOcean(BLUE_CARD, CARD_FRISBEE);
                    break;
                case CARD_HURRICANE:
                    $this->takeFromOcean(BLUE_CARD, CARD_KITE);
                    break;
                case CARD_BEACH_UMBRELLA:
                    $this->takeFromOcean(BLUE_CARD, CARD_SANDCASTLE);
                    break;
                case CARD_SEAGULL:
                    $this->takeFromOcean(BLUE_CARD, CARD_STARFISH);
                    break;
                case CARD_HERMIT_CRAB:
                    $this->takeFromOcean(BLUE_CARD, CARD_SEASHELL);
                    break;
                case CARD_LIFEGUARD:
                    $this->takeFromOcean(BLUE_CARD, CARD_SWIMMER);
                    break;
                case CARD_BOAT:
                    $this->boat($player_id, $card_id, $target_id);
                    break;
                case CARD_BONFIRE:
                    $this->bonfire($player_id);
            }
        }
    }

    private function takeFromOcean($card_type, $card_type_arg) {
        $ocean = $this->game->deck->getOcean();
        $player_id = $this->game->getActivePlayerId();
        $nbr = 0;
        $taken_cards = [];

        foreach ($ocean as $card) {
            if ($card['type'] == $card_type and $card['type_arg'] == $card_type_arg) {
                $this->game->deck->cardToPlayer($card['id'], $player_id);
                $nbr++;
                $taken_cards[] = $card;
            }
        }

        $card_type_id = $card_type * 19 + $card_type_arg;

        $this->game->notifyAllPlayers('takeFromOcean', clienttranslate('${playerName} takes ${nbr} ${cardName} from the ocean'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'nbr' => $nbr,
            'cardName' => $this->game->card_types[$card_type_id]['card_name'],
            'player_id' => $player_id,
            'taken_cards' => $taken_cards,
        ]);
    }

    private function boat($player_id, $card_id, $target_card_id) {

        $boat = $this->game->deck->getCard($card_id);
        $this->game->deck->discardOceanCard($card_id);
        $target_card_type_id = $this->game->deck->getCardTypeId($target_card_id);

        $this->game->notifyAllPlayers('playBoat', clienttranslate('${playerName} plays ${cardName} from the ocean'), [
            'playerName'=> $this->game->getActivePlayerName(),
            'cardName' => $this->game->card_types[$target_card_type_id]['card_name'],
            'boat_card' => $boat,
            'player_id' => $player_id,
            'boat_target_id' => $target_card_id
        ]);
        
        $this->playCard($player_id, $target_card_id, -1);
        $this->game->deck->playActionCard($target_card_id);
    }

    private function bonfire($player_id) {
        $discards = $this->game->deck->getHand($player_id);
        foreach ($discards as $key => $card) {
            if ($discards[$key]['type'] == YELLOW_CARD and $discards[$key]['type_arg'] == CARD_BONFIRE) {
                unset($discards[$key]);
            }
        }

        $this->game->deck->discardHand($player_id);

        $picked_cards = $this->game->deck->pickCards(4, $player_id);

        $this->game->notifyAllPlayers('bonfire', clienttranslate('${playerName} discards their hand and picks new cards'), [
            'playerName'=> $this->game->getActivePlayerName(),
            'player_id' => $player_id,
            'discards' => array_values($discards),
        ]);

        $this->game->notifyPlayer($player_id, 'discardHand', clienttranslate('You discard your hand'), []);

        $this->game->notifyPlayer($player_id, 'pickCards', clienttranslate('You pick 4 cards from the deck'), [
            'picked_cards' => $picked_cards
        ]);
    }
}