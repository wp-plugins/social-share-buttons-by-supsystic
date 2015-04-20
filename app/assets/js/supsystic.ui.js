/*
 * Main UI file.
 *
 * Here we activate and configure all scripts or
 * jQuery plugins required for UI.
 *
 */
(function ($, window, vendor, undefined) {

    $(document).ready(function () {

        /* Bootstrap Tooltips */
        $('body').tooltip({
            selector: '.supsystic-plugin [data-toggle="tooltip"]',
            container: 'body'
        });
    });

}(jQuery, window, 'supsystic'));