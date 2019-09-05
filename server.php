<?php
$baseDir = dirname(__FILE__);
$pidfile = $baseDir . '/pid_file.pid';
if (isDaemonActive($pidfile)) {
    exit();
}
include 'bll/vars.php';
include 'bll/sockets.php';
include 'bll/functions.php';

InitFiles();

file_put_contents($pidfile, getmypid());


$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, 0, $port);
socket_listen($socket);
$clients = array($socket);
$isSocketActive = true;

while ($isSocketActive) {
    $changed = $clients;
    socket_select($changed, $null, $null, 0, 10);
    if (in_array($socket, $changed)) {
        $socket_new = socket_accept($socket);
        $clients[] = $socket_new;
        $header = socket_read($socket_new, 1024);
        perform_handshaking($header, $socket_new, $host, $port);
        socket_getpeername($socket_new, $ip);
        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
    }
    foreach ($changed as $changed_socket) {
        while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
            $maskedMessage = unmask($buf);
            $message = json_decode($maskedMessage);
            if (isset($message->type)) {
                if($message->type == "connectUser") {
                    ConnectPlayer($message);
                    $usersRegistration = new UsersRegistration();
                }
                if($message->type == "registerUser") {
                    if( $usersRegistration->IsPlayablePlacesAvailible() ) {
                        $usersRegistration->RegisterPlayer($message->playerId, $message->playerScreenWidth, $message->playerScreenHeight);
                        $socketResource = GetResourceFromId($message->playerId);
                        if(!$socketResource) {
                            send_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "info", "message" => "Player with id ".$message->playerId." successfully registered as a player!"))));
                        }else {
                            send_personal_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "info", "message" => "You was successfully registered as a player!"))), $socketResource);
                        }
                    }else {
                        $usersRegistration->RegisterObserver($message->playerId, $message->playerScreenWidth, $message->playerScreenHeight);
                        $socketResource = GetResourceFromId($message->playerId);
                        if(!$socketResource) {
                            send_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "info", "message" => "Observer with id ".$message->playerId." successfully registered as a player!"))));
                        }else {
                            send_personal_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "info", "message" => "You was successfully registered as a observer!"))), $socketResource);
                        }
                    }
                }
                if($message->type == "updateShipPosition") {
                    $gameField->UpdateShipsCoords($message->id, $message->positionX, $message->positionY);
                }
            }
            break 2;
        }

        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
        if ($buf === false) {
            global $usersRegistration;
            $found_socket = array_search($changed_socket, $clients);
            socket_getpeername($changed_socket, $ip);
            foreach ($userclients as $disconnectedClient) {
                if( $disconnectedClient["socket"] == $clients[$found_socket]) {
                    $usersRegistration->DisconnectUser($disconnectedClient["userId"]);
                    unset($userclients[$disconnectedClient["userId"]]);
                }
            }
            unset($clients[$found_socket]);
        }
    }
    if( $field->currentPlayers == 2 and !$gameFieldInited) {
        $gameField = new GameField();
        $gameField->initGameField();
        $gameFieldInited = true;
    }

    if($gameFieldInited) {
        $gameField->CheckAmmos();
        $gameField->CheckWinners();
    }
}
socket_close($socket);

function isDaemonActive($pidfile) {
    if (is_file($pidfile)) {
        $pid = file_get_contents($pidfile);
        $status = getDaemonStatus($pid);
        if ($status["run"]) {
            return true;
        } else {
            if (!unlink($pidfile)) {
                exit(-1);
            }
        }
    }
    return false;
}

function getDaemonStatus($pid) {
    $result = array('run' => false);
    $output = null;
    exec("ps -aux -p " . $pid, $output);

    if (count($output) > 1) {
        $result['run'] = true;
        $result['info'] = $output[1];
    }
    return $result;
}