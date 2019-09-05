<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 10.05.2019
 * Time: 9:19
 */

class GameField
{

    public function initGameField()
    {
        global $field;
        $players = $this->GetPlayersId();
        $this->InitFieldForPlayer($players);
        $this->InitFieldForPlayer(array_reverse($players));
        foreach ($players as $key => $value) {
            send_personal_message(mask(json_encode(array("type" => "initGameField", "message" => $field->players[$value]))), GetResourceFromId($value));
        }
    }

    public function UpdateShipsCoords($playerId, $positionX, $positionY, $enemieId = null)
    {
        global $field;
        $field->players[$playerId]->ship->positionX = $positionX;
        $field->players[$playerId]->ship->positionY = $positionY;
        $players = $this->GetPlayersId();
        if ($players[0] == $playerId) {
            $enemieId = $players[1];
        } else if ($players[1] == $playerId) {
            $enemieId = $players[0];
        }
        $field->players[$enemieId]->enemie->ship->positionX = $field->players[$enemieId]->screenWidth - $field->players[$playerId]->ship->positionX;
        $field->players[$enemieId]->enemie->ship->positionY = $field->players[$enemieId]->screenHeight - $field->players[$playerId]->ship->positionY;

        $field->players[$enemieId]->enemie->ship->health->progressBarXPosition = $field->players[$enemieId]->screenWidth - $field->players[$playerId]->ship->positionX;
        $field->players[$enemieId]->enemie->ship->health->progressBarYPosition = $field->players[$enemieId]->screenHeight - $field->players[$playerId]->ship->positionY;
        foreach ($players as $key => $value) {
            send_personal_message(mask(json_encode(array("type" => "updateGameField", "message" => $field->players[$value]))), GetResourceFromId($value));
        }
    }

    public function CheckAmmos()
    {
        global $field;
        $players = $this->GetPlayersId();
        for ($i = 0; $i <= 1; $i++) {
            if (empty($field->players[$players[$i]]->ship->ammos)) {
                $this->CreateNewPlayerAmmo($players[$i], $players);
            } else {
                $this->CheckForAmmoToDelete($players[$i], $players);
                $this->UpdatePlayerAmmosCoords($players[$i]);
                $this->UpdateEnemieAmmosCoords($players[$i], $players);
                if (round(microtime(true) * 1000) - $field->players[$players[$i]]->ship->lastAmmo > 500) {
                    $this->CreateNewPlayerAmmo($players[$i], $players);
                }
                $this->ChechHits($players[$i]);
            }
        }
        foreach ($players as $key => $value) {
            send_personal_message(mask(json_encode(array("type" => "updateGameField", "message" => $field->players[$value]))), GetResourceFromId($value));
        }
    }

    public function ChechHits($playerId)
    {
        global $field;
        $enemieId = $this->GetEnemieId($playerId);
        foreach ($field->players[$playerId]->ship->ammos as $key => $value) {
            if ($this->HaveXCoordShoot($value->x, $field->players[$playerId]->enemie->ship->positionX) and $this->HaveYCoordShoot($value->y, $field->players[$playerId]->enemie->ship->positionY)) {
                $value->status = "destroy";
                $enemieAmmoId = $value->id + 1;
                $field->players[$enemieId]->enemie->ship->ammos[$enemieAmmoId]->status = "destroy";
                $field->players[$enemieId]->ship->health->shipHealth -= 5;
                if ($field->players[$enemieId]->ship->health->shipHealth <= 20) {
                    $field->players[$enemieId]->ship->health->progressBarColor = "red";
                    $field->players[$playerId]->enemie->ship->health->progressBarColor = "red";
                } else if ($field->players[$enemieId]->ship->health->shipHealth <= 50) {
                    $field->players[$enemieId]->ship->health->progressBarColor = "yellow";
                    $field->players[$playerId]->enemie->ship->health->progressBarColor = "yellow";
                }
                $field->players[$playerId]->enemie->ship->health->shipHealth -= 5;
            }
        }
    }

    public function CheckWinners()
    {
        global $field, $gameFieldInited;
        $players = $this->GetPlayersId();
        if ($field->players[$players[0]]->ship->health->shipHealth <= 0) {
            send_personal_message(mask(json_encode(array("type" => "fieldMessage", "message" => "You defeat!"))), GetResourceFromId($players[0]));
            send_personal_message(mask(json_encode(array("type" => "clearWindow"))), GetResourceFromId($players[0]));
            send_personal_message(mask(json_encode(array("type" => "fieldMessage", "message" => "You win!"))), GetResourceFromId($players[1]));
            send_personal_message(mask(json_encode(array("type" => "clearWindow"))), GetResourceFromId($players[1]));
            $gameFieldInited = false;
            $field->currentPlayers = 0;
            $field->players = array();
        }
        if ($field->players[$players[1]]->ship->health->shipHealth <= 0) {
            send_personal_message(mask(json_encode(array("type" => "fieldMessage", "message" => "You defeat!"))), GetResourceFromId($players[1]));
            send_personal_message(mask(json_encode(array("type" => "clearWindow"))), GetResourceFromId($players[1]));
            send_personal_message(mask(json_encode(array("type" => "fieldMessage", "message" => "You win!"))), GetResourceFromId($players[0]));
            send_personal_message(mask(json_encode(array("type" => "clearWindow"))), GetResourceFromId($players[0]));
            $gameFieldInited = false;
            $field->currentPlayers = 0;
            $field->players = array();
        }
    }

    private function HaveXCoordShoot($ammoXCoord, $enemieShipXCoord)
    {
        if ($ammoXCoord >= $enemieShipXCoord and $ammoXCoord <= $enemieShipXCoord + 50) {
            return true;
        }
        return false;
    }

    private function HaveYCoordShoot($ammoYCoord, $enemieShipYCoord)
    {
        if ($ammoYCoord >= $enemieShipYCoord and $ammoYCoord <= $enemieShipYCoord + 66) {
            return true;
        }
        return false;
    }

    private function GetPlayersId($players = array())
    {
        global $field;
        foreach ($field->players as $player) {
            if ($player->isPlayer) {
                $players[] = $player->name;
            }
        }
        return $players;
    }

    private function InitFieldForPlayer($players)
    {
        global $field;

        $field->players[$players[0]]->screenWidthDelta = $field->players[$players[0]]->screenWidth - $field->players[$players[1]]->screenWidth;

        //player ship
        $field->players[$players[0]]->ship->lastAmmo = round(microtime(true) * 1000);
        $field->players[$players[0]]->ship->positionX = $field->players[$players[0]]->screenWidth / 2;
        $field->players[$players[0]]->ship->positionY = $field->players[$players[0]]->screenHeight;
        $field->players[$players[0]]->ship->image = "images/varship.png";

        $playerShipHealth = new Health();
        $playerShipHealth->shipHealth = 100;
        $playerShipHealth->progressBarColor = "green";

        $field->players[$players[0]]->ship->health = $playerShipHealth;

        //enemie first player
        $enemie = new Player();
        $enemie->name = $players[1];
        $enemie->isPlayer = true;
        $enemie->screenWidth = $field->players[$players[1]]->screenWidth;
        $enemie->screenHeight = $field->players[$players[1]]->screenHeight;
        $enemie->screenWidthDelta = 0;

        //enemie ship for player
        $enemieFirst = new Ship();
        $enemieFirst->positionX = $enemie->screenWidth / 2 - $field->players[$players[0]]->screenWidthDelta;
        $enemieFirst->positionY = 0;
        $enemieFirst->image = "images/varshipinverse.png";

        $enemieShipHealth = new Health();
        $enemieShipHealth->shipHealth = 100;
        $enemieShipHealth->progressBarColor = "green";
        $enemieShipHealth->progressBarXPosition = $enemieFirst->positionX;
        $enemieShipHealth->progressBarYPosition = $enemieFirst->positionY;

        $enemieFirst->health = $enemieShipHealth;

        $enemie->ship = $enemieFirst;

        $field->players[$players[0]]->enemie = $enemie;
    }

    private function CreateNewPlayerAmmo($playerId, $players, $enemieId = null)
    {
        global $field;
        $ammoId = round(microtime(true) * 1000);

        //player ammo
        $ammo = new Ammo();
        $ammo->status = "new";
        $ammo->id = $ammoId;
        $ammo->x = $field->players[$playerId]->ship->positionX + 15;
        $ammo->y = $field->players[$playerId]->ship->positionY - 10;
        $ammo->image = "images/shipammo.png";
        $field->players[$playerId]->ship->ammos[$ammoId] = $ammo;

        //enemie ammo
        if ($players[0] == $playerId) {
            $enemieId = $players[1];
        } else if ($players[1] == $playerId) {
            $enemieId = $players[0];
        }

        $ammo = new Ammo();
        $ammo->status = "new";
        $ammo->id = $ammoId + 1;
        $ammo->x = $field->players[$enemieId]->enemie->ship->positionX + 15;
        $ammo->y = $field->players[$enemieId]->enemie->ship->positionY + 80;
        $ammo->image = "images/shipammoinverse.png";
        $field->players[$enemieId]->enemie->ship->ammos[$ammo->id] = $ammo;

        $field->players[$playerId]->ship->lastAmmo = round(microtime(true) * 1000);
    }

    private function UpdatePlayerAmmosCoords($playerId)
    {
        global $field;
        foreach ($field->players[$playerId]->ship->ammos as $key => $value) {
            if ($value->y <= -21) {
                $value->status = "destroy";
            } else {
                $value->y -= 1;
                $value->status = "move";
            }
        }
    }

    private function UpdateEnemieAmmosCoords($playerId, $players, $enemieId = null)
    {
        global $field;
        if ($players[0] == $playerId) {
            $enemieId = $players[1];
        } else if ($players[1] == $playerId) {
            $enemieId = $players[0];
        }
        foreach ($field->players[$enemieId]->enemie->ship->ammos as $key => $value) {
            if ($value->y >= 800) {
                $value->status = "destroy";
            } else {
                $value->y += 1;
                $value->status = "move";
            }
        }
    }

    private function CheckForAmmoToDelete($playerId, $players, $enemieId = null)
    {
        global $field;
        if ($players[0] == $playerId) {
            $enemieId = $players[1];
        } else if ($players[1] == $playerId) {
            $enemieId = $players[0];
        }
        foreach ($field->players[$playerId]->ship->ammos as $key => $value) {
            if ($value->status == "destroy") {
                unset($field->players[$playerId]->ship->ammos[$key]);
            }
        }

        foreach ($field->players[$enemieId]->enemie->ship->ammos as $key => $value) {
            if ($value->status == "destroy") {
                unset($field->players[$enemieId]->enemie->ship->ammos[$key]);
            }
        }
    }

    public function HaveHits($playerId, $players, $enemieId = null)
    {
        global $field;
        if ($players[0] == $playerId) {
            $enemieId = $players[1];
        } else if ($players[1] == $playerId) {
            $enemieId = $players[0];
        }
        foreach ($field->players[$playerId]->enemie->ship->ammos as $key => $value) {
            if ($value->x >= $field->players[$playerId]->ship->positionX or $value->x <= $field->players[$playerId]->ship->positionX + 50) {
                if ($value->y >= $field->players[$playerId]->ship->positionY or $value->y <= $field->players[$playerId]->ship->positionX + 66) {
                    $value->status = "destroy";
                    $field->players[$playerId]->ship->health->shipHealth -= 10;
                    $field->players[$playerId]->enemie->ship->health->shipHealth -= 10;
                }
            }
        }
    }

    public function GetEnemieId($playerId)
    {
        $players = $this->GetPlayersId();
        if ($players[0] == $playerId) {
            return $players[1];
        } else if ($players[1] == $playerId) {
            return $players[0];
        }
    }

}