(function ($, window) {

    $(document).ready(function () {
        $('.supsystic-social-sharing > a').on('click', function (e) {
            e.preventDefault();

            if (e.currentTarget.href.slice(-1) !== '#') {
                window.open(e.currentTarget.href, 'mw' + e.timeStamp, 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0');
            }
        });

        $('.supsystic-social-sharing').each(function (index, container) {
            var $container = $(container),
                $buttons = $container.find('a'),
                animation = $container.attr('data-animation'),
                iconsAnimation = $container.attr('data-icons-animation'),
                animationEndEvents = 'webkitAnimationEnd mozAnimationEnd ' +
                    'MSAnimationEnd oanimationend animationend';

            var getAnimationClasses = function (animation) {
                return 'animated ' + animation;
            };

            if ($buttons.length) {
                $buttons.hover(function () {
                    $(this).addClass(getAnimationClasses(animation))
                        .one(animationEndEvents, function () {
                            $(this).removeClass(getAnimationClasses(animation));
                        });
                    $(this).find('i.fa').addClass(getAnimationClasses(iconsAnimation))
                        .one(animationEndEvents, function () {
                            $(this).removeClass(getAnimationClasses(iconsAnimation));
                        });
                });
            }
        });

        var onResize = function () {
            $('.supsystic-social-sharing-left, .supsystic-social-sharing-right').each(function (index, container) {
                var $container = $(container),
                    outerheight = $container.outerHeight(true),
                    totalHeighht = $(window).height();

                $container.animate({top: totalHeighht / 2 - outerheight / 2}, 200);
            });
        };

        onResize.call();
        $(window).on('resize', onResize);

        $(document).on('click', function () {
            $('.supsystic-social-sharing-click')
                .show();
        });


        $('.supsystic-social-sharing').each(function () {
            var $el = $(this);

            if ($el.hasClass('supsystic-social-sharing-mobile')) {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    $el.hide();
                } else {
                    $el.show();
                }
            } else {
                if (!$el.hasClass('supsystic-social-sharing-click')) {
                    $el.show();
                }
            }
        });
    });

}(window.jQuery, window));