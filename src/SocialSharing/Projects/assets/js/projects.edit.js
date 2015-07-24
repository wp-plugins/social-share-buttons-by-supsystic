(function ($, app) {

    $(document).ready(function () {

        var scroll,
            $networksList = $('.networks'),
            $networksDialog,
            $networksDialogTrigger,
            $wtsList = $('.where-to-show'),
            $wtsExtras = $('.wts-extra'),
            $showEverywhere = $('input[name="settings[show_at]"]'),
            $hideOnMobile = $('input[name="settings[hide_on_mobile]"]'),
            $pages = $('.chosen'),
            $displayTotalShares = $('input[name="settings[display_total_shares]"]'),
            $sharesRadios = $('input[name="settings[shares]"]'),
            $shortNumbers = $('input[name="settings[short_numbers]"]'),
            $previewButtons = $('.pricon'),
            $preview = $('.supsystic-social-sharing'),
            $design = $('input[name="settings[design]"]'),
            $animation = $('#ba-button-animation'),
            $iconAnimation = $('#ba-icons-animation'),
            $adminNavButtons = $('.admin-nav-button'),
            buttonWidth = $('.sharer-flat').width();

        // Rename
        $('h2[contenteditable]').on('keydown', function (e) {
            if ('keyCode' in e && e.keyCode === 13) {
                var $title, text, request;

                $title = $(this);
                text = $title.text();

                $title.removeAttr('contenteditable');
                $title.html($('<i/>', { class: 'fa fa-fw fa-spin fa-circle-o-notch' }));

                request = app.request({ module: 'projects', action: 'rename' }, {
                    title: text,
                    id: app.getParameterByName('id')
                });

                request.done(function (response) {
                    $title.text(text);
                    $title.attr('contenteditable', true);

                    if (!response.success) {
                        $title.text($title.data('original'));
                    }
                });

                e.preventDefault();
            }
        });

        // cb
        var onRemoveNetwork = (function onRemoveNetwork(e) {
            e.preventDefault();

            var element = e.data ? e.data.element : $(e.currentTarget).parents('.network'),
                checkbox = e.data ? e.data.checkbox : $('#' + element.attr('id') + '-checkbox');

            element.remove();
            checkbox.removeAttr('checked');

            $('body').trigger('networksChanged');
        });

        // Animation
        $animation.on('change', function () {
            var $preview = $('.animation-preview'),
                current = $preview.attr('data-animation');

            //$preview.removeClass(current);
            $preview.addClass($animation.val());
            $preview.attr('data-animation', $animation.val());
            $preview.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $(this).removeClass($animation.val() + ' animated');
            })
        });

        $iconAnimation.on('change', function () {
            var $preview = $('.icon-animation-preview'),
                current = $preview.attr('data-animation');

            //$preview.removeClass(current + ' animated');
            $preview.addClass($iconAnimation.val() + ' animated');
            $preview.attr('data-animation', $iconAnimation.val());
            $preview.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $(this).removeClass($iconAnimation.val() + ' animated');
            })
        });

        $('.animation-preview').hover(function () {
            var $preview = $(this),
                current = $animation.val();

            $preview.addClass($animation.val() + ' animated');
            $preview.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $(this).removeClass(current + ' animated');
            })
        });

        $('.animation-preview').hover(function () {
            var $preview = $('.icon-animation-preview'),
                current = $iconAnimation.val();

            $preview.addClass($iconAnimation.val() + ' animated');
            $preview.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $(this).removeClass(current + ' animated');
            })
        });

        // Design and animation
        $design.on('click', function () {
            var type = $(this).val(),
                $preview = $('.animation-preview');

            $preview.removeClass('sharer-flat-1 sharer-flat-2 sharer-flat-3 sharer-flat-4 sharer-flat-5 sharer-flat-6 sharer-flat-7 sharer-flat-8 sharer-flat-9');
            $preview.addClass('sharer-'+type);
        });

        // Chosen
        $pages.chosen();

        // Allow to save settings for non-HTML5 browsers.
        $('button#save').bind('click', function (e) {
            var oldHtml = $(e.currentTarget).html();

            $(e.currentTarget).html($('<i/>', { class: 'fa fa-circle-o-notch fa-spin' }));

            var stdstr = 'action=social-sharing&route%5Bmodule%5D=networks&route%5Baction%5D=addToProject&project_id=1';

            if ($('form#networks').serialize() === stdstr) {
                $('#nonetworks').show();
            } else {
                $('#nonetworks').hide();
            }

            var networksPositions = [];

            $networksList.find('.network').each(function(index) {
                networksPositions.push({
                    'network': $(this).find('[name="networks[]"]').val(),
                    'position': index
                });
            });

            $.post(
                $('form#networks').attr('action'),
                $('form#networks').serialize()
            ).done(function () {
                    $.post($('form#networks').attr('action'), {
                        'action': 'social-sharing',
                        'route': {
                            'module': 'networks',
                            'action': 'updateSorting'
                        },
                        'project_id': parseInt($('#networks [name="project_id"]').val()),
                        'positions': networksPositions
                    }).done(function(response) {
                        $.post($('form#settings').attr('action'), $('form#settings').serialize()).done(function (r) {
                            $(e.currentTarget).html(oldHtml);
                            if ('popup_id' in r) $('input[name="settings[popup_id]"]').val(r.popup_id);
                        })
                    });
                });
        });

        // Extra "Where to show" fields
        $wtsList.find('input[type="radio"]').on('click', function () {
            $wtsExtras.hide();

            var hasExtra = $(this).parents('li').has('ul.wts-extra').length;
            if (hasExtra) {
                $(this).parents('li')
                    .find('.wts-extra')
                    .show()
                    .find('li:first input')
                    .attr('checked', 'checked');
            }

            $('#wts-shortcode').hide();
            if (this.value == 'code') {
                $('#wts-shortcode').show();
            }
            window.ppsCheckUpdateArea($(this).closest('ul'));
        });

        $wtsExtras.find('input').bind('click', function (e) {
            $(e.currentTarget).attr('checked', 'checked');
            window.ppsCheckUpdateArea($(this).closest('ul'));
        });

        // Initialize horizontal scroll
        //scroll = new app.ScrollController('.scrollable-content', {blockWidth: 380});
        //scroll.init();

        // Networks list
        $networksList.find('.network > .delete').bind('click', onRemoveNetwork);
        $networksList.sortable({
            sort: function (e, ui) {
                ui.item.css({
                    backgroundColor: '#eee'
                });
            },
            update: function (e, ui) {

                if (ui && ui.item) {
                    ui.item.css({
                        backgroundColor: '#fff'
                    });
                }

                $('#savingNetworksSorting').show();

                var positions = [];

                $networksList.find('.network').each(function (i, el) {
                    var id = parseInt(el.id.slice(7));

                    positions.push({
                        network: id,
                        position: i
                    });
                });

                app.request({
                    module: 'networks',
                    action: 'updateSorting'
                }, {
                    project_id: app.getParameterByName('id'),
                    positions: positions
                }).always(function () {
                    $('#savingNetworksSorting').hide();
                }).fail(function (error) {
                    alert('Failed to save sort order: ' + error);
                });
            }
        });

        // Initialize networks dialog
        $networksDialogTrigger = $('#addNetwork');
        $networksDialogTrigger.bind('click', (function networksDialogTriggerClicked() {
            $networksDialog.dialog('open');
        }));

        $networksDialog = $('#networks-dialog');
        $networksDialog.dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            appendTo: '#wpwrap',
            buttons: {
                Save: (function btnSelect() {
                    var checked = $(this).find(':checked'),
                        form = $('form#settings');

                    if (!checked.length) {
                        return;
                    }

                    $.each(checked, function each(index, checkbox) {
                        var $checkbox = $(checkbox),
                            network = $.parseJSON($checkbox.val()),
                            $networkContainer = $('<div/>', {
                                class: 'network',
                                id: 'network' + network.id
                            });

                        $networkContainer.append(
                            $('<a/>', { class: 'delete', href: '#' })
                                .append($('<i/>', { class: 'fa fa-fw fa-times' }))
                                .bind('click', { element: $networkContainer, checkbox: $checkbox }, onRemoveNetwork)
                        ).append(
                            $('<span/>', { class: 'title' })
                                .text(network.name)
                        ).append(
                            $('<input>', { type: 'hidden', name: 'networks[]' })
                                .val(network.id)
                        ).append(
                            $('<nav/>', {class: 'network-navigation'})
                        ).append(
                            $('<div/>', {class: 'information-container'})
                        );

                        $.each(['title', 'name', 'tooltip'], function (index, value) {
                            $networkContainer.find('nav').append(
                                $('<a/>', { href: '', class: 'network-nav-item admin-nav-button ' + (!index ? 'active' : '') , data: { type: value } })
                                    .text(value[0].toUpperCase() + value.slice(1))
                            );
                        });

                        $.each(['title', 'name', 'tooltip'], function (index, value) {
                            var $line = null;
                            $networkContainer.find('div').append(
                                $line = $('<input/>', { class: 'network-' + value , name: (value == 'tooltip' ? 'networkTooltip' : '' ), data: { id: network.id }, hidden: 'hidden' })
                                    .text(value[0].toUpperCase() + value.slice(1))
                            );

                            if(value == 'title') {
                                $line.show();
                            }
                        });

                        if (!$networksList.has('#network' + network.id).length) {
                            $networksList.append($networkContainer);
                            $networkContainer.find('.information-container input').bind('focusout', function() {

                                switch ($(this).attr('class').split('network-')[1]) {
                                    case 'title' : {
                                        return saveTitle($(this));
                                    } break;
                                    case 'name' : {
                                        return saveName($(this));
                                    } break;
                                    case 'tooltip' : {
                                        return saveTooltip($(this));
                                    } break;
                                }
                            });

                            networkNavigation();
                        }
                    });

                    $networksDialog.dialog('close');
                    $('body').trigger('networksChanged');
                }),
                Close: (function btnClose() {
                    $networksDialog.dialog('close');
                })
            }
        });

        // Autosave
        $('body').on('networksChanged', function () {
            $('button#save').click();
            $networksList.sortable('option', 'update')(null);
        });

        // Checkboxes
        $pages.change(function () {
            if ($pages.val() !== null) {
                $showEverywhere.removeAttr('checked');
            }
        });

        $showEverywhere.bind('click', function () {
            if (this.checked) {
                $pages.find(':selected').removeAttr('selected');
                $pages.trigger('chosen:updated');
            } else {
                $showEverywhere.attr('checked', 'checked');
            }
        });

        $displayTotalShares.bind('click', function () {
            if (this.checked) {
                $previewButtons.removeClass('without-counter');
                $shortNumbers.removeAttr('disabled');
                $sharesRadios.removeAttr('disabled');
            } else {
                $previewButtons.addClass('without-counter');
                $previewButtons.find('.counter').text('5731');
                $shortNumbers.removeAttr('checked');
                $shortNumbers.attr('disabled', 'disabled');
                $sharesRadios.attr('disabled', 'disabled');
            }
        });

        $shortNumbers.bind('click', function () {
            if (this.checked) {
                $previewButtons.find('.counter').text('5.7k');
            } else {
                $previewButtons.find('.counter').text('5731');
            }
        });

        // Delete
        $('.button.delete').bind('click', function (e) {
            e.preventDefault();

            if (confirm('Are you sure?')) {
                $(this).html($('<i/>', { class: 'fa fa-fw fa-circle-o-notch fa-spin' }));
                $.post(this.href).done(function () {
                    window.location.href = $('#addProject_modal').parents('li')
                        .next()
                        .find('a')
                        .attr('href');
                });
            }
        });

        $('.select-all').on('click', function() {
            var $icon = $(this).find('i'),
                $networkCheckboxes = $('[name="networks"]');

            if($icon.hasClass('fa-check')) {
                $networkCheckboxes.attr('checked', true)
                    .iCheck('update');
                $icon.removeClass('fa-check').addClass('fa-remove');
            } else {
                $networkCheckboxes.attr('checked', false)
                    .iCheck('update');
                $icon.removeClass('fa-remove').addClass('fa-check');
            }
        });

        $adminNavButtons.on('click', function() {
            var $sections = $('.scroll');

            if(!$(this).hasClass('network-nav-item')) {
                $adminNavButtons.removeClass('active');
                $(this).addClass('active');

                $sections.hide()
                    .filter('[data-navigation="' + $(this).data('block') + '"]').show();
            }
        });

        $('[name="settings[overlay_with_shadow]"]').on('click', function() {
            var $container = $('.supsystic-social-sharing');

            if($(this).is(':checked')) {
                $container.attr('data-overlay', 'on');
            } else {
                $container.attr('data-overlay', '');
            }
        });

        $('div.supsystic-social-sharing .sharer-flat').on('mouseover', function() {
            if($('[name="settings[change_size]"]').is(':checked')) {
                $(this).css('width', buttonWidth - buttonWidth/4);
            }
        }).on('mouseleave', function() {
            if($('[name="settings[change_size]"]').is(':checked')) {
                $(this).css('width', '');
            }
        });

        $('[name="settings[buttons_size]"]').on('change', function() {
            $('.supsystic-social-sharing a').css('font-size', $(this).val() + 'em');
        }).trigger('change');

        $('[name="settings[spacing]"]').on('change', function() {
            if($(this).is(':checked')) {
                $('.supsystic-social-sharing a').css('margin-left', '20px');
            } else {
                $('.supsystic-social-sharing a').css('margin-left', '0');
            }
        }).trigger('change');

        $('[data-navigation="design"] .sharer-flat').on('click', function() {
            $(this).parent().find('[type="radio"]').attr('checked', true)
                .trigger('click');
            window.ppsCheckUpdateArea($('.supsystic-social-sharing'));
        });

        $('.location-tooltip').tooltipster({
            animation: 'slide',
            position: 'right',
            theme: 'tooltipster-shadow',
            contentAsHTML: true,
            maxWidth: '320',
            interactive: true,
        });

        $('.choose-effect-buttons').on('mouseover', function() {
            $(this).addClass('animated ' + $(this).data('animation'));
        });

        $('.choose-effect-buttons').bind("animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd", function() {
            $(this).removeClass('animated ' + $(this).data('animation'));
        });

        $('.choose-effect-icons').on('mouseover', function() {
            $(this).find('i').addClass('animated ' + $(this).data('animation'));
        });

        $('.choose-effect-icons').bind("animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd", function() {
            $(this).find('i').removeClass('animated ' + $(this).data('animation'));
        });

        $('[name="settings[where_to_show]"]').on('click', function() {
            if($(this).val() == 'sidebar') {
                $('#wts-sidebar-nav').iCheck('update')
                    .parent().show();
            } else {
                $('#wts-sidebar-nav').iCheck('update')
                    .parent().hide();
            }
            window.ppsCheckUpdateArea($(this).closest('.where-to-show'));
        });

        var saveTooltip = function($element) {
            var networkId = $element.data('id'),
                tooltip = $element.val();

            $.post($('form#networks').attr('action'), {
                'action': 'social-sharing',
                'route': {
                    'module': 'networks',
                    'action': 'saveTooltips'
                },
                'project_id': parseInt($('#networks [name="project_id"]').val()),
                'data': { 'id': networkId, 'value': tooltip }
            }).done(function(response) {
                console.log(response);
            });
        };

        var saveTitle = function($element) {
            var networkId = $element.data('id'),
                title = $element.val();

            $.post($('form#networks').attr('action'), {
                'action': 'social-sharing',
                'route': {
                    'module': 'networks',
                    'action': 'saveTitles'
                },
                'project_id': parseInt($('#networks [name="project_id"]').val()),
                'data': { 'id': networkId, 'value': title }
            }).done(function(response) {
                console.log(response);
            });
        };

        var saveName = function($element) {
            var networkId = $element.data('id'),
                name = $element.val();

            $.post($('form#networks').attr('action'), {
                'action': 'social-sharing',
                'route': {
                    'module': 'networks',
                    'action': 'saveNames'
                },
                'project_id': parseInt($('#networks [name="project_id"]').val()),
                'data': { 'id': networkId, 'value': name }
            }).done(function(response) {
                console.log(response);
            });
        };

        $('[name="networkTooltip"]').on('focusout', function() {
            saveTooltip($(this));
        });

        $('.network-title').on('focusout', function() {
            saveTitle($(this));
        });

        $('.network-name').on('focusout', function() {
            saveName($(this));
        });

        $('[name="settings[display_total_shares]"]').on('change', function() {
            if(this.checked) {
                $preview.find('.counter-wrap').show();
            } else {
                $preview.find('.counter-wrap').hide();
            }
        });

        $('.code').on('click focus', function() {
            $(this).select();
        });

        var networkNavigation = function() {
            var $buttons = $('.network-nav-item');

            $buttons.off('click');

            $buttons.on('click', function(e) {
                e.preventDefault();

                $(this).parent().find('a').removeClass('active');
                $(this).addClass('active');

                $('.information-container input').hide()
                    .filter('.network-' + $(this).data('type')).show();
            });
        };

        networkNavigation();


        $('#bd-shares-style').on('change', function() {

            $preview.filter('.supsystic-social-sharing-preview')
                .find('.counter-wrap').removeClass('standard arrowed')
                    .addClass($(this).val());
        });

        // Select popup on popup radio
        var $popupDialog = $('#selectPopupDialog').dialog({
            width: 400,
            modal: true,
            autoOpen: false,
            buttons: {
                Select: function () {
                    $('#popupId').val($popupDialog.find('select').val());
                    $(this).dialog('close');
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });

        $('#wts-popup').on('click', function () {
            $popupDialog.dialog('open');

            return false;
        });
    });

}(window.jQuery, window.supsystic.SocialSharing));