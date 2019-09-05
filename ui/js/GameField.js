var gameFieldObject = {

    gameFieldId: "gamefield",

    InitGameField: function(object) {
        this.InitPlayerField(object["ship"]);
        this.InitEnemieField(object["enemie"]["ship"]);
        this.InitHealthProgressBar(object["ship"]["health"]);
        this.InitEnemieHealthProgressBar(object["enemie"]["ship"]["health"]);
    },

    InitPlayerField: function(player) {
        var playerImage = document.getElementById("playerShip");
        playerImage.style.top = player["positionY"] + "px";
        playerImage.style.left = player["positionX"] + "px";
        playerImage.src = player["image"];
        playerImage.style.display = "block";
        shipMover.top = player["positionY"];
        shipMover.left = player["positionX"];
    },

    InitEnemieField: function(enemie) {
        var enemieImage = document.getElementById("enemieShip");
        enemieImage.style.top = enemie["positionY"] + "px";
        enemieImage.style.left = enemie["positionX"] + "px";
        enemieImage.src = enemie["image"];
        enemieImage.style.display = "block";
    },

    InitHealthProgressBar: function(health) {
        $("#shipStatus-ob").slideToggle("fast");
        $("#playerHealthProgressBar").css("display", "block").val(health["shipHealth"]);
    },

    InitEnemieHealthProgressBar: function(health) {
        $("#enemieHealthProgressBarPlace").slideToggle("fast").css("position", "absolute").css("left", health["progressBarXPosition"] + "px").css("top", health["progressBarYPosition"] + "px");
        $("#enemieHealthProgressBar").val(health["shipHealth"]);
    },

    UpdateGameField: function(object) {
        this.UpdateEnemieShip(object["enemie"]["ship"]);
        this.ProcessPlayerAmmos(object["ship"]["ammos"]);
        this.ProcessEnemieAmmos(object["enemie"]["ship"]["ammos"]);
        this.UpdateHealthProgressbar(object["ship"]["health"]);
        this.UpdateEnemieHealthProgressbar(object["enemie"]["ship"]["health"]);
    },

    UpdateEnemieShip: function(enemie) {
        var enemieImage = document.getElementById("enemieShip");
        enemieImage.style.top = enemie["positionY"] + "px";
        enemieImage.style.left = enemie["positionX"] + "px";
    },

    UpdateHealthProgressbar: function(health) {
        $("#playerHealthProgressBar").val(health["shipHealth"]);
        $("#HealthProgressBarColor").css("background", health["progressBarColor"]);
    },

    UpdateEnemieHealthProgressbar: function(health) {
        $("#enemieHealthProgressBarPlace").css("left", health["progressBarXPosition"] + "px").css("top", health["progressBarYPosition"] + "px");
        $("#enemieHealthProgressBar").val(health["shipHealth"]);
        $("#enemieHealthProgressBarColor").css("background", health["progressBarColor"]);
    },

    ProcessPlayerAmmos: function(playerAmmos) {
        for(var i in playerAmmos) {
            var ammo = playerAmmos[i];
            switch (ammo["status"]) {
                case "new": this.CreateNewAmmo(ammo);
                    break;
                case "move": this.MoveAmmo(ammo);
                    break;
                case "destroy": this.DestroyAmmo(ammo);
                    break;
            }
        }
    },

    ProcessEnemieAmmos: function(enemieAmmos) {
        for(var i in enemieAmmos) {
            var enemieAmmo = enemieAmmos[i];
            switch (enemieAmmo["status"]) {
                case "new": this.CreateNewAmmo(enemieAmmo);
                    break;
                case "move": this.MoveAmmo(enemieAmmo);
                    break;
                case "destroy": this.DestroyAmmo(enemieAmmo);
                    break;
            }
        }
    },

    CreateNewAmmo: function(ammo) {
        if($("#"+ammo["id"]).length > 0) {
            return;
        }
        var ammoImage = document.createElement("img");
        ammoImage.id = ammo["id"];
        ammoImage.classList.add("shipammo");
        ammoImage.src = ammo["image"];
        ammoImage.style.position = "absolute";
        ammoImage.style.top = ammo["y"] + "px";
        ammoImage.style.left = ammo["x"] + "px";
        document.getElementById(this.gameFieldId).appendChild(ammoImage);
    },

    MoveAmmo: function(ammoIn) {
        if($("#" + ammoIn["id"].length > 0)) {
            var ammo = document.getElementById(ammoIn["id"]);
            ammo.style.top = ammoIn["y"] + "px";
        }else {
            var ammoImage = document.createElement("img");
            ammoImage.id = ammoIn["id"];
            ammoImage.classList.add("shipammo");
            ammoImage.src = ammoIn["image"];
            ammoImage.style.position = "absolute";
            ammoImage.style.top = ammoIn["y"] + "px";
            ammoImage.style.left = ammoIn["x"] + "px";
            document.getElementById(this.gameFieldId).appendChild(ammoImage);
        }

    },

    DestroyAmmo: function(ammo) {
        if($("#" + ammo["id"]).length > 0) {
            document.getElementById(ammo["id"]).remove();
        }
    },

    ClearGameField: function() {
        var playerImage = document.getElementById("playerShip");
        var enemieImage = document.getElementById("enemieShip");
        $(".shipammo").remove();
        $("#shipStatus-ob").slideToggle("fast");
        $("#enemieHealthProgressBarPlace").css("display", "none");
        playerImage.style.display = "none";
        enemieImage.style.display = "none";
    },

    UpdateFieldText: function(text) {
        $("#fieldMessages").text(text)
    }

};