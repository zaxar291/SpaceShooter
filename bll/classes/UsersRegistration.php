<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 09.05.2019
 * Time: 20:23
 */

class UsersRegistration {

    private $haveErrors = false;

    public function IsPlayablePlacesAvailible() {
        global $field;
        if( $field->currentPlayers <= $field->maxPlayers ) {
            return true;
        }else {
            return false;
        }
    }

    public function RegisterPlayer($id, $screenWidth, $screenHeight) {
        global $field;
        if(isset($users[$id])) {
            $this->haveErrors = true;
            return false;
        }
        $player = new Player();
        $player->name = $id;
        $player->isPlayer = true;
        $player->screenWidth = $screenWidth;
        $player->screenHeight = $screenHeight;
        $player->screenWidthDelta = 0;

        $ship = new Ship();
        $ship->health = 100;
        $ship->image = null;
        $ship->positionX = 0;
        $ship->positionY = 0;

        $player->ship = $ship;

        $field->players[$id] = $player;
        $field->currentPlayers++;
        return true;
    }

    public function RegisterObserver($id, $screenWidth, $screenHeight) {
        global $field;
        if(isset($users[$id])) {
            $this->haveErrors = true;
            return false;
        }
        $player = new UserObserver();
        $player->name = $id;
        $player->screenWidth = $screenWidth;
        $player->screenHeight = $screenHeight;
        $player->screenWidthDelta = 0;

        $ship = new Ship();
        $ship->health = 100;
        $ship->image = null;
        $ship->positionX = 0;
        $ship->positionY = 0;

        $player->ship = array(
            $ship
        );

        $field->observers[$id] = $player;
        return true;
    }

    public function DisconnectUser($userId) {
        global $field, $gameSrarted, $gameField, $gameFieldInited;
        if(!$gameFieldInited) {
            if($this->IsPlayer($userId)) {
                unset($field->players[$userId]);
                $field->currentPlayers -= 1;
            }else {
                unset($field->observers[$userId]);
            }
        }else {
            if($this->IsPlayer($userId)) {
                $enemieId = $gameField->GetEnemieId($userId);
                send_personal_message(mask(json_encode(array("type" => "fieldMessage", "message" => "Game over!"))), GetResourceFromId($enemieId));
                send_personal_message(mask(json_encode(array("type" => "clearWindow"))), GetResourceFromId($enemieId));
                send_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "warn", "message" => "Sorry, but one of the player was disconnected, game work impossible now!"))));
                send_message(mask(json_encode(array("type" => "clearWindow"))));
                $gameFieldInited = false;
                $field->currentPlayers = 0;
                $field->players = array();
            } else {
                unset($field->observers[$userId]);
            }

        }
    }

    private function IsPlayer($userId){
        global $field;
        return (isset($field->players[$userId])) ? true : false;
    }
}