<?php

function InitFiles() {
    global $filesToInclude, $field;
    if( is_array($filesToInclude) ) {
        foreach ($filesToInclude as $key => $file) {
            if( is_file($file) ) {
                include $file;
            } else {
                die("File ".$file." not found and game work impossible now!");
            }
        }
    }
    if( $field == null ) {
        $field = new Field();
    }
    return true;
}

function ConnectPlayer($message) {
    global $userclients, $clients;
    $userId = $message->id;
    $userclients[$userId] = array("userId" => $userId, "socket" => end($clients));
    foreach ($userclients as $client) {
            if($client["userId"] == $userId) {
                send_personal_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "info", "message" => "You have been successfully connected to the server!"))), $client["socket"]);
                return true;
            }
    }
    send_message(mask(json_encode(array("type" => "windowMessage", "windowMessageType" => "error", "message" => "Player with id $userId cannot be connected."))));
    return false;
}
