var shipMover = {
    timer: null,
    animationStarted: false,
    fieldWidth: null,
    element: null,
    left: 0,
    top: 10,
    MoveDown: function() {
        if(this.element == null) {
            this.element = document.getElementById("playerShip");
        }
        if(this.top >= -1) {
            $("#gamefield").css("border-top", "3px solid green")
        }
        if(this.top >= 730) {
            $("#gamefield").css("border-bottom", "3px solid red");
            return;
        }
        this.element.style.top = this.top + 10 + "px";
        this.top += 10;
        this.UpdateShipPosition();
    },
    MoveUp: function() {
        if(this.element == null) {
            this.element = document.getElementById("playerShip");
        }
        if(this.top <= 0) {
            $("#gamefield").css("border-top", "3px solid red");
        }else {
            $("#gamefield").css("border-top", "3px solid green");
            $("#gamefield").css("border-bottom", "3px solid green");
            this.element.style.top = this.top - 10 + "px";
            this.top -= 10;
            this.UpdateShipPosition();
        }
    },
    MoveRight: function() {
        if(this.element == null) {
            this.element = document.getElementById("playerShip");
        }
        if(this.left >= -1) {
            $("#gamefield").css("border-left", "3px solid green");
        }
        if(this.left >= this.fieldWidth) {
            $("#gamefield").css("border-right", "3px solid red");
            return;
        }
        this.element.style.left = this.left + 10 + "px";
        this.left += 10;
        this.UpdateShipPosition();
    },
    MoveLeft: function() {
        if(this.element == null) {
            this.element = document.getElementById("playerShip");
        }
        if(this.left <= 0) {
            $("#gamefield").css("border-left", "3px solid red");
            return;
        }else {
            $("#gamefield").css("border-right", "3px solid green");
        }
        this.element.style.left = this.left - 10 + "px";
        this.left -= 10;
        this.UpdateShipPosition();
    },

    UpdateShipPosition: function () {
        if(connectionService.GetWsSatus() === 1) {
            connectionService.SendWsMessage({
                type: "updateShipPosition",
                id: player.playerId,
                positionY: this.top,
                positionX: this.left
            });
        }
    },

    InitFieldWidth: function() {
        this.fieldWidth = $("#gamefield").width() - 50;
    }
};

function MoveShip(e) {
    if(shipMover.animationStarted) {
        return false;
    }
    var keyCode = e.charCode;
    switch (keyCode) {
        case 115: shipMover.timer = setInterval(() => {
            shipMover.MoveDown()
        }, 15);
            break;
        case 119:  shipMover.timer = setInterval(() => {
            shipMover.MoveUp()
        }, 15);
            break;
        case 100:  shipMover.timer = setInterval(() => {
            shipMover.MoveRight()
        }, 15);
            break;
        case 97:  shipMover.timer = setInterval(() => {
            shipMover.MoveLeft()
        }, 15);
            break;
    }
    shipMover.animationStarted = true;
}

function StopAnimation() {
    clearInterval(shipMover.timer);
    shipMover.animationStarted = false;
}