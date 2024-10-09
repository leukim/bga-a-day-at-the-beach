<?php

class ActionCards {
    private $game;

    public function __construct($game) {
        $this->game = $game;
    }

    public function playCard($card_id) {
        $card = $this->game->deck->getCard($card_id);

        $this->game->debug(json_encode($card));
        if ($card['type'] == YELLOW_CARD) {
            switch ($card['type_arg']) {
                case 10:
                    $this->playfulPuppy();
                    break;
            }
        }
    }

    public function playfulPuppy() { // Pick al frisbees from the Ocean
        $ocean = $this->game->deck->getOcean();
        $player_id = $this->game->getActivePlayerId();
        $nbr = 0;
        $taken_cards = [];

        foreach ($ocean as $card) {
            if ($card['type'] == BLUE_CARD and $card['type_arg'] == 3) {
                $this->game->deck->cardToPlayer($card['id'], $player_id);
                $nbr++;
                $taken_cards[] = $card;
            }
        }

        // TODO Log player plays card

        $this->game->notifyAllPlayers('takeFromOcean', clienttranslate('${playerName} takes ${nbr} ${cardName} from the Ocean'), [
            'playerName' =>  $this->game->getActivePlayerName(),
            'nbr' => $nbr,
            'cardName' => $this->game->card_types[3]['card_name'],
            'player_id' => $player_id,
            'taken_cards' => $taken_cards,
        ]);
    }
}