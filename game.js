connectionService.InitConnection();
$(document).ready(function(){
    $("#startgame").click(function() {
        connectionService.SendWsMessage({
            type: "registerUser",
            playerId: player.playerId,
            playerScreenWidth: shipMover.fieldWidth,
            playerScreenHeight: 730
        });
    });

    shipMover.InitFieldWidth();

    connectionService.websocket.onerror = function(ev) {
        connectionService.WsError();
    };

    connectionService.websocket.onclose = function(ev) {
        connectionService.WsClose();
    };

    connectionService.websocket.onopen = function(ev) {
        connectionService.WsOpen();
    };

    connectionService.websocket.onmessage = function(ev) {
        connectionService.WsMessage(ev);
    };
    player.playerId = $("#username").val();
});

document.onkeypress = MoveShip;

document.onkeyup = StopAnimation;