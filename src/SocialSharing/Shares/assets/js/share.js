(function ($) {

    $(document).ready(function () {

        var selector = '.supsystic-social-sharing > a',
            $buttons = $(selector);

        $('.supsystic-social-sharing').each(function() {
            if($(this).find('a').length && !$(this).hasClass('networks-list-container')) {
                saveViews($(this).find('a:first'));
            }
        });

        $buttons.on('click', function (e) {
            var $button = $(this),
                projectId = parseInt($button.data('pid')),
                networkId = parseInt($button.data('nid')),
                postId = parseInt($button.data('post-id')),
                data = {},
                url = $button.data('url');

            data.action = 'social-sharing-share';
            data.project_id = projectId;
            data.network_id = networkId;
            data.post_id = isNaN(postId) ? null : postId;

            $.post(url, data).done(function () {
                $button.find('.counter').text(function (index, text) {
                    if (isNaN(text)) {
                        return text;
                    }

                    return parseInt(text) + 1;
                });
            });

            e.preventDefault();
        });
    });

    function saveViews($button) {
		// This global variable can be set from outside plugin to ignore saving statistics.
		// Used for now in popupControllerPps::_generateSocSharingAssetsForPreview() to avoid send statistic on PopUp preview in admin area
		if(typeof(sssIgnoreSaveStatistics) !== 'undefined' && sssIgnoreSaveStatistics)
			return;
        var projectId = parseInt($button.data('pid')),
            postId = parseInt($button.data('post-id')),
            data = {},
            url = $button.data('url');

        data.action = 'social-sharing-view';
        data.project_id = projectId;
        data.post_id = isNaN(postId) ? null : postId;

        if(data.project_id) {
            $.post(url, data);
        }
    }

}(jQuery));