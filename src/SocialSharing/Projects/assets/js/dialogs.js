(function ($, app) {

    var Controller = function() {
        this.$buttonsDialog = $('.buttons-adnimation-dialog');
        this.$iconsDialog = $('.icons-adnimation-dialog');
    };

    Controller.prototype.initButtonsDialog = function() {
        var $button = $('#buttons-animation'),
            $effects = $('.choose-effect-buttons'),
            self = this;

        this.$buttonsDialog.dialog({
            autoOpen: false,
            modal:    true,
            width:    600,
            buttons : {
                Close : function() {
                    self.$iconsDialog.dialog('close');
                }
            }
        });

        $button.on('click', function(e) {
            e.preventDefault();

            self.$buttonsDialog.dialog('open');
        });

        $effects.on('click', function() {
            $('[name="settings[buttons_animation]"]').val($(this).data('animation'));
            self.$buttonsDialog.dialog('close');
        });
    };

    Controller.prototype.initIconsDialog = function() {
        var $button = $('#icons-animation'),
            $effects = $('.choose-effect-icons'),
            self = this;

        this.$iconsDialog.dialog({
            autoOpen: false,
            modal:    true,
            width:    600,
            buttons : {
                Close : function() {
                    self.$iconsDialog.dialog('close');
                }
            }
        });

        $button.on('click', function(e) {
            e.preventDefault();

            self.$iconsDialog.dialog('open');
        });

        $effects.on('click', function() {
            $('[name="settings[icons_animation]"]').val($(this).data('animation'));
            self.$iconsDialog.dialog('close');
        });
    };

    Controller.prototype.init = function() {
        this.initButtonsDialog();
        this.initIconsDialog();
    };

    $(document).ready(function() {
        var controller = new Controller();

        controller.init();
    });

}(window.jQuery, window.supsystic.SocialSharing));