(function ($, app) {

    $(document).ready(function () {

        var scroll,
            $networksList,
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
            $design = $('input[name="settings[design]"]'),
            $animation = $('#ba-button-animation'),
            $iconAnimation = $('#ba-icons-animation');

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
            var type = $design.filter(':checked').val(),
                $preview = $('.animation-preview');

            $preview.removeClass('sharer-flat-1 sharer-flat-2 sharer-flat-3 sharer-flat-4');
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

            $.post(
                $('form#networks').attr('action'),
                $('form#networks').serialize()
            ).done(function () {$.post($('form#settings').attr('action'), $('form#settings').serialize()).done(function (r) {
                    $(e.currentTarget).html(oldHtml);
                    if ('popup_id' in r) $('input[name="settings[popup_id]"]').val(r.popup_id);
                }) });
        });

        // Extra "Where to show" fields
        $wtsList.find('li > label > input').bind('click', function () {
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
        });

        $wtsExtras.find('input').bind('click', function (e) {
            $(e.currentTarget).attr('checked', 'checked');
        });

        // Initialize horizontal scroll
        scroll = new app.ScrollController('.scrollable-content', {blockWidth: 380});
        scroll.init();

        // Networks list
        $networksList = $('.networks');
        $networksList.find('.network > .delete').bind('click', onRemoveNetwork);
        $networksList.sortable({
            sort: function (e, ui) {
                ui.item.css({
                    backgroundColor: '#eee'
                });
            },
            stop: function (e, ui) {

                ui.item.css({
                    backgroundColor: '#fff'
                });

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
            buttons: {
                Select: (function btnSelect() {
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
                        );

                        if (!$networksList.has('#network' + network.id).length) {
                            $networksList.append($networkContainer);
                        }

                        $networksDialog.dialog('close');
                    });
                }),
                Close: (function btnClose() {
                    $networksDialog.dialog('close');
                })
            }
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
    });

}(window.jQuery, window.supsystic.SocialSharing));