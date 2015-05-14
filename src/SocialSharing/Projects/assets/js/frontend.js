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
                buttonChangeSize = $container.attr('data-change-size'),
                $navButton  = $container.find('.nav-button'),
                animationEndEvents = 'webkitAnimationEnd mozAnimationEnd ' +
                    'MSAnimationEnd oanimationend animationend',
                transitionHelper = {
                    'supsystic-social-sharing-right': {
                        'transition': 'translateX(160px)',
                        'display': 'block'
                    },
                    'supsystic-social-sharing-left': {
                        'transition': 'translateX(-160px)',
                        'display': 'block'
                    },
                    'supsystic-social-sharing-top': {
                        'transition': 'translateY(-160px)',
                        'display': 'inline-block'
                    },
                    'supsystic-social-sharing-bottom': {
                        'transition': 'translateY(160px)',
                        'display': 'inline-block'
                    }
                },
                buttonsTransition = null;

            var getAnimationClasses = function (animation) {
                return 'animated ' + animation;
            };

            var checkNavOrientation = function() {
                $.each(transitionHelper, function(index, value) {
                    if($.inArray(index, $container.attr('class').split(' ')) > -1) {
                        $container.find('.nav-button').css({
                            'display': value['display']
                        });

                        buttonsTransition = value['transition'];
                    }
                });
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

            if(buttonChangeSize == 'on') {
                var buttonWidht = $buttons.width();

                $buttons.filter('.sharer-flat-2').on('mouseover', function() {
                    $(this).css('width', buttonWidht - buttonWidht/4);
                }).on('mouseleave', function() {
                    $(this).css('width', buttonWidht);
                });
            }

            checkNavOrientation();
            $navButton.on('click', function() {
                if($(this).hasClass('hide')) {
                    $(this).css('transform', 'rotate(180deg)')
                        .removeClass('hide').addClass('show');

                    $container.find('a').css('transform', buttonsTransition);
                } else {
                    $(this).css('transform', 'rotate(0deg)')
                        .addClass('hide').removeClass('show');

                    $container.find('a').css('transform', 'translateX(0)');
                }
            });

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