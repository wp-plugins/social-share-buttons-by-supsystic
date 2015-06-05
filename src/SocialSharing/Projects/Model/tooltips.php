<?php

    $path = dirname(dirname(dirname(dirname(__FILE__))));
    $url = plugin_dir_url($path) . 'app/assets/img';

    return array(
        'sidebar-tooltip' => 'Sidebar tooltip image',
        'content-tooltip' => 'Content tooltip image',
        'popup-tooltip' => 'Popup tooltip image',
        'widget-tooltip' => 'Widget tooltip image',
        'shortcode-tooltip' => 'Shortcode tooltip image',
		'spacing-tooltip' => "<img src=" . $url ."/tooltips-images/distance-between-buttons.jpg />",
		'buttons-size' => "<img src=" . $url ."/tooltips-images/buttons-size.jpg />",
		'display-counters' => "<img src=" . $url . "/tooltips-images/display-counters.jpg />",
		'use-short-numbers' => "<img src=" . $url . "/tooltips-images/display-short-numbers.jpg />",
    );