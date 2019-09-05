<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link id="CSSsource" href="style.css" rel="stylesheet" />
        <link id="CSSprogressBar" href="css/progressbar.css" rel="stylesheet" />
        <script src="jquery-3.1.1.js"></script>
        <script src="ui/js/mover.js"></script>
        <script src="ui/js/alerts.js"></script>
        <script src="ui/js/GameField.js"></script>
        <script src="ui/js/socket.js"></script>
        <script src="ui/js/player.js"></script>
        <script src="game.js"></script>
    </head>
    <body>
        <div id="play_zone">
            <div id="gamefield-ob">
                <div id="gamefield">
                    <img class="gameimage" id="playerShip" draggable="false" style="display: none">
                    <img class="gameimage" id="enemieShip" draggable="false" style="display: none">
                    <div class="progress" id="enemieHealthProgressBarPlace" style="display: none;">
                        <progress max="100" id="enemieHealthProgressBar" value="0"></progress>
                        <div class="progress-bg"><div class="progress-bar" id="enemieHealthProgressBarColor"></div></div>
                    </div>
                </div>
                <div id="fieldMessages" class="fieldMessages"></div>
            </div>
            <div id="shipStatus-ob" style="display: none;">
                <div class="progress" style="width: 200px;">
                    <progress max="100"  id="playerHealthProgressBar" value="0" ></progress>
                    <div class="progress-value"></div>
                    <div class="progress-bg" style="height: 13px;"><div class="progress-bar" id="HealthProgressBarColor"></div></div>
                </div>
                <span id="game-messages"></span>
            </div>
        </div>
        <div id="userZone">
            <div id="game-button-ob">
                <input type="submit" id="startgame" class="startGameButton" value="Start the game">
            </div>
        </div>
        <?php echo '<input type="hidden" id="username" value="' . rand(1, 10000000) . '">'; ?>
    </body>
</html>