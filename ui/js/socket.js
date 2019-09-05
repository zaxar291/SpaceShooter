var connectionService = {
    wsUrl: "ws://localhost:9999/",
    websocket: "",
    InitConnection: function() {
        this.websocket = new WebSocket(this.wsUrl);
    },

    WsError: function(e) {
        if( !alertService.haveWindows() ) {
            alertService.bindERROR("Error occured: connection to the server cannot be estabilished, try to reload the page");
        } else {
            alertService.updateCurrentModalWindow({
                message: "Error occured: connection to the server cannot be estabilished, try to reload the page",
                messageType: "error"
            });
        }
    },

    WsClose: function() {
        if( !alertService.haveWindows() ) {
            alertService.bindERROR("Error occured: connection to the server cannot be estabilished, try to reload the page");
        } else {
            alertService.updateCurrentModalWindow({
                message: "Connection with server lost, try to restart the page",
                messageType: "error"
            });
        }
    },

    WsOpen: function() {
        if( !alertService.haveWindows() ) {
            alertService.bindINFO("Websocket connection with server estabilished successfully!");
        } else {
            alertService.updateCurrentModalWindow({
                message: "Websocket connection with server estabilished successfully!",
                messageType: "info"
            });
        }
        this.SendWsMessage({
            type: "connectUser",
            id: player.playerId
        });
    },

    WsMessage: function(socketMessage) {
        if (socketMessage === undefined || socketMessage.data === undefined || socketMessage === "") {
            if( !alertService.haveWindows() ) {
                alertService.bindWARN("Error occured: empty or crashed data from server");
            } else {
                alertService.updateCurrentModalWindow({
                    message: "Error occured: empty or crashed data from server",
                    messageType: "warn"
                });
            }
            return;
        }
        var decodedSocketMessage = JSON.parse(socketMessage.data);
        if( decodedSocketMessage.type === "windowMessage" ) {
            if( !alertService.haveWindows() ) {
                alertService.bindWARN(decodedSocketMessage["message"]);
            } else {
                alertService.updateCurrentModalWindow({
                    message: decodedSocketMessage["message"],
                    messageType: decodedSocketMessage["windowMessageType"]
                });
            }
            return;
        }

        if( decodedSocketMessage.type === "clearWindow" ) {
            gameFieldObject.ClearGameField();
        }

        if( decodedSocketMessage.type === "initGameField" ) {
            gameFieldObject.InitGameField(decodedSocketMessage.message);
        }

        if( decodedSocketMessage.type === "updateGameField" ) {
            gameFieldObject.UpdateGameField(decodedSocketMessage.message);
        }

        if( decodedSocketMessage.type === "fieldMessage" ) {
            gameFieldObject.UpdateFieldText(decodedSocketMessage.message);
        }

        if( decodedSocketMessage.type === "tstmsg" ) {
            console.log(decodedSocketMessage["message"]);
        }
    },

    SendWsMessage: function(message) {
        this.websocket.send(JSON.stringify(message));
    },

    GetWsSatus: function() {
        return this.websocket.readyState;
    }
};