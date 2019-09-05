var alertService = {
    insidercount: 0,
    bindERROR: function(message)
    {
        alertService.bindBASE({class: "error", header: "Error detected", message: message})
    },
    bindINFO: function(message)
    {
        alertService.bindBASE({class: "info", header: "Info message", message: message})
    },
    bindWARN: function(message)
    {
        alertService.bindBASE({class: "warn", header: "Warning", message: message})
    },
    bindBASE: function(inputObject)
    {
        $(document.body).append('<div class="modal" id="modal-'+alertService.insidercount+'"><div style="position:relative"></div><div class="btn-close" onclick="alertService.closeAlert('+alertService.insidercount+')"></div><div class="modalOb"><div id="modal-header-'+this.insidercount+'" class="modal-header '+inputObject.class+'">'+inputObject.header+'</div><div id="modal-body-'+this.insidercount+'" class="modal-body '+inputObject.class+'"><div class="modal-messages" id="modal-text-'+this.insidercount+'">'+inputObject.message+'</div></div></div></div>');
        alertService.showAlert(alertService.insidercount);
        alertService.insidercount++;
    },
    showAlert: function(elemId)
    {
        $("#modal-"+elemId).css("left", "-500px");
        $("#modal-"+elemId).css("display", "block");
        $("#modal-"+elemId).animate({left: screen.width - 400}, 500);
    },
    closeAlert: function(elemid)
    {
        $("#modal-"+elemid).animate({left: -500 + 'px'}, 500);
        setTimeout(() => {
            $("#modal-"+elemid).remove();
        }, 500);
    },

    getCurrentModalWindow: function() {
        return {
            message: $("#modal-text-" + (this.insidercount - 1)).text(),
            messageType: $("#modal-header-" + (this.insidercount - 1)).text()
        };
    },

    updateCurrentModalWindow: function(Object) {
        var CurrentModalParams = this.getCurrentModalWindow();
        $("#modal-text-" + (this.insidercount - 1)).text(Object.message);
        $("#modal-header-" + (this.insidercount - 1)).text(Object.messageType).removeClass(CurrentModalParams.messageType).addClass(Object.messageType);
        $("#modal-body-" + (this.insidercount - 1)).removeClass(CurrentModalParams.messageType).addClass(Object.messageType);
    },

    haveWindows: function() {
        if( $(".modal").length > 0 ) {
            return 1;
        }
        return 0;
    }
};