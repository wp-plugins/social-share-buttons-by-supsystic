<?php

    $path = dirname(dirname(dirname(dirname(__FILE__))));
    $url = plugin_dir_url($path) . 'app/assets/img';

    return array(
        'sidebar-tooltip' => '<p>Adds buttons to the selected side of the page.</p>',
        'content-tooltip' => '<p>Buttons will appear at the post or page content.</p>',
        'popup-tooltip' => '<p>You need to install <a href="http://supsystic.com/plugins/popup-plugin/" target="_blank">Popup by Supsystic</a> to use this feature.</p>',
        'widget-tooltip' => '<p>Creates a widget of the current project at Appearance > Widgets and allows you to use project at theme\'s widgets areas.</p>',
        'shortcode-tooltip' => '<p>Allows you to insert project shortcode and show buttons where you want.</p>',
		'spacing-tooltip' => "<p>Adds space between the buttons</p><img src=" . $url ."/tooltips-images/distance-between-buttons.jpg />",
		'buttons-size' => "<p>Choose the size for social buttons. This option allows you to accent the attention for as much as you want</p><img src=" . $url ."/tooltips-images/buttons-size.jpg />",
		'display-counters' => "<p>Displays counters of social shares on buttons</p><img src=" . $url . "/tooltips-images/display-counters.jpg />",
		'use-short-numbers' => "<p>Rounds up big numbers of counters and displays the short numbers. Available only with enabled \"Display counters\" option.</p><img src=" . $url . "/tooltips-images/display-short-numbers.jpg />",
        'enable-grad-mode' => "<p>Gradient mode creates smooth transitions from the one color to another. <a href=\"http://supsystic.com/plugins/social-share-plugin/\" target='_blank'>Available in PRO</a></p>
                            <a href=\"http://supsystic.com/plugins/social-share-plugin/\" target='_blank'><img src=" . $url ."/tooltips-images/gradient-mode-pro.jpg /></a>",
        'grad-mode' => "<p>Gradient mode creates smooth transitions from the one color to another</p>
                            <a href=\"http://supsystic.com/plugins/social-share-plugin/\" target='_blank'><img src=" . $url ."/tooltips-images/gradient-mode.jpg /></a>",
        'nav-button' => '<p>Show/hide navigation button for sidebar mode. Allows user to show or hide share buttons</p>',
        'all-button' => '<p>Allows user to display all networks in popup</p>',
        'content-lock' => '<p>Allows you to lock content elements by class name before user share this page with any network</p>',
        'page-load' => '<p>Show Social Buttons when page loads</p>',
        'user-click' => '<p>Show Social Buttons when user clicks on page</p>'
    );