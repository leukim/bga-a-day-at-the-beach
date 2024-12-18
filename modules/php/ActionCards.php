<?php

class ActionCards {
    private $game;

    public function __construct($game) {
        $this->game = $game;
    }

    public function playCard($player_id, $payload) {
        $card = $this->game->deck->getCard($payload['card_id']);

        $card_type_id = $card['type'] * 19 + $card['type_arg'];
        $card_name = $this->game->card_types[$card_type_id]['card_name'];
        
        $this->game->notifyAllPlayers('playYellowCard', clienttranslate('${playerName} plays ${yellowCardName}'), [
            'playerName'=> $this->game->getActivePlayerName(),
            'yellowCardName'=> $card_name,
            'yellow_card' => $card,
            'yellow_card_id' => $payload['card_id'],
            'yellow_card_type_id' => $card_type_id,
            'player_id' => $player_id,
        ]);

        $this->game->deck->playActionCard($payload['card_id']);

        if ($card['type'] == YELLOW_CARD) {
            switch ($card['type_arg']) {
                case CARD_PLAYFUL_PUPPY:
                    $this->takeFromOcean($player_id, BLUE_CARD, CARD_FRISBEE);
                    break;
                case CARD_HURRICANE:
                    $this->takeFromOcean($player_id, BLUE_CARD, CARD_KITE);
                    break;
                case CARD_BEACH_UMBRELLA:
                    $this->takeFromOcean($player_id, BLUE_CARD, CARD_SANDCASTLE);
                    break;
                case CARD_SEAGULL:
                    $this->takeFromOcean($player_id, BLUE_CARD, CARD_STARFISH);
                    break;
                case CARD_HERMIT_CRAB:
                    $this->takeFromOcean($player_id, BLUE_CARD, CARD_SEASHELL);
                    break;
                case CARD_LIFEGUARD:
                    $this->takeFromOcean($player_id, BLUE_CARD, CARD_SWIMMER);
                    break;
                case CARD_BOAT:
                    $this->boat($player_id, $payload['target_id']);
                    return; // Boat handles state transition
                case CARD_BONFIRE:
                    $this->bonfire($player_id);
                    break;
                case CARD_PIRATE:
                    $this->pirate($payload['card_id'], $player_id, $payload['target_id']);
                    break;
                case CARD_JETSKI:
                    $this->jetski($payload['card_id'], $payload['target'], $player_id);
                    break;
                case CARD_THE_WAVE:
                    $this->theWave($payload['card_id'], $payload['target'], $player_id);
                    break;
                case CARD_TREASURE_CHEST:
                    $this->treasureChest($payload['card_id'], $payload['target'], $player_id);
                    break;
                case CARD_METAL_DETECTOR:
                    $this->metalDetector($payload['target'], $player_id);
                    return; // Metal detector handles state transition
            }
        }

        $this->game->gamestate->nextState(ACT_YELLOW_CARD);
    }

    private function takeFromOcean($player_id, $card_type, $card_type_arg) {
        $ocean = $this->game->deck->getOcean();
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

    private function boat($player_id, $target_card_id) {
        $target_card = $this->game->deck->getCard($target_card_id);

        $target_card_type_id = $this->game->deck->getCardTypeId($target_card_id);

        $this->game->deck->cardToPlayer($target_card_id, $player_id);

        $card_name = $this->game->card_types[$target_card_type_id]['card_name'];

        $this->game->notifyAllPlayers('others_takeFromOcean', clienttranslate('${playerName} takes ${cardName} to play from the ocean'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'cardName' => $card_name,
            'card_id' => $target_card_id,
            'player_id' => $player_id
        ]);

        $this->game->notifyPlayer($player_id, 'cardToHandFromOcean', clienttranslate('You take ${cardName} from the ocean'), [
            'cardName' => $card_name,
            'card' => $target_card,
        ]);

        $this->game->globals->set('cardIdToPlay', $target_card_id);
        $this->game->gamestate->nextState(ACT_PLAY_ACTION_CARD);
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

    private function pirate($pirate_card_id, $player_id, $target_player_id) {
        $this->game->deck->tradeHands($player_id, $target_player_id);

        $player_infos = $this->game->loadPlayersBasicInfos();

        $this->game->notifyAllPlayers('tradeHands', clienttranslate('${player1} trades hands with ${player2}') , [
            'player1'=> $player_infos[$player_id]['player_name'],
            'player2'=> $player_infos[$target_player_id]['player_name'],
            'player_id_1' => $player_id,
            'player_id_2' => $target_player_id,
            'player_nbr_1' => $this->game->deck->handSize($player_id),
            'player_nbr_2' => $this->game->deck->handSize($target_player_id),
        ]);

        $this->game->notifyPlayer($player_id, 'getCardsFrom', clienttranslate('You get cards from ${playerName}'), [
            'playerName'=> $player_infos[$target_player_id]['player_name'],
            'player_id' => $target_player_id,
            'cards' => $this->game->deck->getHand($player_id),
        ]);

        $this->game->notifyPlayer($target_player_id, 'getCardsFrom', clienttranslate('You get cards from ${playerName}'), [
            'playerName'=> $player_infos[$player_id]['player_name'],
            'player_id' => $player_id,
            'cards' => $this->game->deck->getHand($target_player_id),
        ]);
    }

    private function jetski($jetski_card, $picked_cards, $player_id) {
        $taken_cards = [];
        $nbr = 0;
        foreach ($picked_cards as $card_id) {
            $taken_cards[] = $this->game->deck->cardToPlayer($card_id, $player_id);
            $nbr++;
        }

        $this->game->notifyAllPlayers('takeFromOcean', clienttranslate('${playerName} takes ${nbr} cards from the ocean'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'nbr' => $nbr,
            'player_id' => $player_id,
            'taken_cards' => $taken_cards,
        ]);
    }

    private function theWave($wave_card, $picked_cards, $player_id) {
        $taken_cards = [];
        $nbr = 0;
        foreach ($picked_cards as $card_id) {
            $taken_cards[] = $this->game->deck->cardToPlayer($card_id, $player_id);
            $nbr++;
        }

        $this->game->notifyAllPlayers('takeFromOcean', clienttranslate('${playerName} takes ${nbr} cards from the ocean'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'nbr' => $nbr,
            'player_id' => $player_id,
            'taken_cards' => $taken_cards,
        ]);

        $this->game->deck->discardOcean();

        $this->game->notifyAllPlayers('discardOcean', clienttranslate('The ocean is discarded'), []);

        $ocean = [];
        foreach (range(1,3) as $_) {
            $ocean[] = $this->game->deck->pickCardToOcean();
        }

        $this->game->notifyAllPlayers('cardsToOcean', clienttranslate('New cards are drawn into the ocean'), [
            'cards' => $ocean
        ]);
    }

    private function treasureChest($chest_card, $picked_cards, $player_id) {
        $nbr = 0;
        foreach ($picked_cards as $card_id) {
            $card = $this->game->deck->cardToPlayer($card_id, $player_id);
            $nbr++;

            $card_type_id = $card['type'] * 19 + $card['type_arg'];
            $this->game->notifyPlayer($player_id, 'cardToHandFromDiscard', clienttranslate('You get ${cardName} from the discard'), [
                'cardName' => $this->game->card_types[$card_type_id]['card_name'],
                'card' => $card,
            ]);
        }

        $this->game->notifyAllPlayers('others_takeFromDiscard', clienttranslate('${playerName} takes ${nbr} cards from the discard'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'nbr' => $nbr,
            'player_id' => $player_id,
        ]);
    }

    private function metalDetector($target_card_id, $player_id) {
        $card = $this->game->deck->getCard($target_card_id);

        $this->game->deck->cardToPlayer($target_card_id, $player_id);
        $card_type_id = $card['type'] * 19 + $card['type_arg'];

        $this->game->notifyAllPlayers('others_takeFromDiscard', clienttranslate('${playerName} takes ${nbr} card from the discard'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'nbr' => 1,
            'player_id' => $player_id,
        ]);

        $this->game->notifyPlayer($player_id, 'cardToHandFromDiscard', clienttranslate('You get ${cardName} from the discard'), [
            'cardName' => $this->game->card_types[$card_type_id]['card_name'],
            'card' => $card,
        ]);

        if ($card['type'] == BLUE_CARD) {
            $this->game->gamestate->nextState(ACT_YELLOW_CARD);
        } else {
            $this->game->globals->set('cardIdToPlay', $card['id']);
            $this->game->gamestate->nextState(ACT_PLAY_ACTION_CARD);
        }
    }

}