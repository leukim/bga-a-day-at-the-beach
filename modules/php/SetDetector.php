<?php

require_once "constants.inc.php";

class SetDetector {
    private $cards;
    private $card_types;

    public function __construct($deck, $card_types) {
        $this->cards = $deck;
        $this->card_types = $card_types;
    }

    public function get_available_sets($player_id): array {
        $hand = $this->cards->getHand($player_id);

        $sets = [];

        foreach ($hand as $key => $card) {
            $card_type = $this->get_card_type($card);
            if ($card_type['card_type'] == BLUE_CARD) {
                switch ($card_type['set_type']) {
                    case 'pair':
                        $pair_in_hand_card = $this->find_pair_in_hand($card_type, $hand);
                        if ($pair_in_hand_card != null) {
                            $other_card_type = $this->get_card_type($pair_in_hand_card);
                            $sets[] = [
                                'name' => $card_type['card_name']." and ".$other_card_type['card_name'],
                                'card_ids' => [$card['id'], $pair_in_hand_card['id']],
                            ];
                            if ($hand[$key]['type_arg'] != CARD_LIFESAVER) { // Is not a Joker
                                unset($hand[$key]);
                            }
                        }
                        break;
                    case 'group':
                        // TODO BUG YELLOW CARDS
                        $keys = $this->find_group_in_hand($card_type, $hand);
                        $card_ids = [];
                        foreach ($keys as $key) {
                            $card_ids[] = $hand[$key]['id'];
                            if ($hand[$key]['type_arg'] != CARD_LIFESAVER) { // Is not a Joker
                                unset($hand[$key]);
                            }
                        }
                        if (count($keys) >= $card_type['set_size']) {
                            $sets[] = [
                                'name' => $card_type['card_name'],
                                'card_ids' => $card_ids,
                            ];
                        }
                        
                        break;
                    default:
                        break;
                }
            }
        }

        return $sets;
    }

    private function get_card_type($card) {
        $type = $card['type_arg'];
        if ($card['type'] == YELLOW_CARD) {
            $type += 19;
        }
        return $this->card_types[$type];
    }

    private function find_pair_in_hand($card_type, $hand): mixed {
        return $this->get_card_id_in_hand($card_type['pair_id'], $hand);
    }

    private function get_card_id_in_hand($card_type_id, $hand): mixed {
        foreach ($hand as $card) {
            $card_type = $this->get_card_type($card);
            if (
                $card_type['card_type'] == BLUE_CARD and 
                ( $card['type_arg'] == $card_type_id or $card['type_arg'] == CARD_LIFESAVER)
            ) {
                return $card;
            }
        }
        return null;
    }

    private function find_group_in_hand($card_type, $hand): array {
        $group = [];
        foreach ($hand as $key => $card) {
            $hand_card_type = $this->get_card_type($card);
            if (
                $hand_card_type['card_type'] == BLUE_CARD and 
                ($card['type_arg'] == $card_type['card_type_arg'] or $card['type_arg'] == CARD_LIFESAVER)
            ) {
                $group[] = $key;
            }
        }
        return $group;
    }
}