<?php

require_once "constants.inc.php";

class SetDetector {
    private $JOKER_TYPE_ARG = 1;

    private $cards;
    private $card_types;
    private $that;

    public function __construct($deck, $card_types, $that) {
        $this->cards = $deck;
        $this->card_types = $card_types;
        $this->that = $that;
    }

    public function has_set($player_id): bool {
        $hand = $this->cards->getHand($player_id);

        foreach ($hand as $card_id => $card) {
            $card_type = $this->get_card_type($card);
            if ($card_type['card_type'] == BLUE_CARD) {
                switch ($card_type['set_type']) {
                    case 'pair':
                        $pair_in_hand = $this->find_pair_in_hand($card_type, $hand);
                        if ($pair_in_hand) return true;
                        break;
                    case 'group':
                        $group_in_hand = $this->find_group_in_hand($card_type, $hand);
                        if ($group_in_hand) return true;
                        break;
                    default:
                        break;
                }
            }
        }

        return false;
    }

    public function get_available_sets($player_id): array {
        $hand = $this->cards->getHand($player_id);

        $sets = [];

        foreach ($hand as $card) {
            $card_type = $this->get_card_type($card);
            if ($card_type['card_type'] == BLUE_CARD) {
                switch ($card_type['set_type']) {
                    case 'pair':
                        $pair_in_hand = $this->find_pair_in_hand($card_type, $hand);
                        if ($pair_in_hand) {
                            $sets[] = [
                                'name' => $card_type['card_name'],
                                'card_type_arg' => $card_type['card_type_arg'],
                            ];
                        }
                        break;
                    case 'group':
                        $group_in_hand = $this->find_group_in_hand($card_type, $hand);
                        if ($group_in_hand) {
                            $sets[] = [
                                'name' => $card_type['card_name'],
                                'card_type_arg' => $card_type['card_type_arg'],
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

    private function find_pair_in_hand($card_type, $hand): bool {
        return $this->is_card_id_in_hand($card_type['pair_id'], $hand);
    }

    private function is_card_id_in_hand($card_type_id, $hand): bool {
        foreach ($hand as $card) {
            if ($card['type_arg'] == $card_type_id or $card['type_arg'] == $this->JOKER_TYPE_ARG) {
                return true;
            }
        }
        return false;
    }

    private function find_group_in_hand($card_type, $hand): bool {
        $count = 0;
        foreach ($hand as $card) {
            if ($card['type_arg'] == $card_type['card_type_arg'] or $card['type_arg'] == $this->JOKER_TYPE_ARG) {
                $count += 1;
            }
        }
        return $count >= $card_type['set_size'];
    }
}