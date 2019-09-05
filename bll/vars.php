<?php

$gameSrarted = false;

$gameFieldInited = false;

$filesToInclude = array("Entities/Field.php", "Entities/Player.php", "Entities/Ship.php", "Entities/UserObserver.php", "Entities/Ammo.php", "Entities/Health.php", "bll/classes/UsersRegistration.php", "bll/classes/GameField.php");

$isSocketActive = false;

$host = 'localhost';

$port = '9999';

$null = NULL;

error_reporting(E_ALL);

set_time_limit(0);

ob_implicit_flush();

ignore_user_abort(true);

$userclients = array();

$field = null;

$gameField = null;

$lastAmmoId = 1;

$usersRegistration = null;