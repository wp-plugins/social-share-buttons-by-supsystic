(function ($, window, app) {

    $(document).ready(function () {
        $('.supsystic-social-sharing > a').on('click', function (e) {
            e.preventDefault();

            if (e.currentTarget.href.slice(-1) !== '#') {
                window.open(e.currentTarget.href, 'mw' + e.timeStamp, 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0');
            }
        });

        window.initSupsysticSocialSharing = function ($container) {
            if (!($container instanceof jQuery)) {
                $container = $($container);
            }
            var $buttons = $container.find('a'),
                animation = $container.attr('data-animation'),
                iconsAnimation = $container.attr('data-icons-animation'),
                buttonChangeSize = $container.attr('data-change-size'),
                $navButton = $container.find('.nav-button'),
                $printButton = $container.find('.print'),
                $bookmarkButton = $container.find('.bookmark'),
                $mailButton = $container.find('.mail'),
                animationEndEvents = 'webkitAnimationEnd mozAnimationEnd ' +
                    'MSAnimationEnd oanimationend animationend',
                transitionHelper = {
                    'supsystic-social-sharing-right':  {
                        'transition': 'translateX(160px)',
                        'display':    'block'
                    },
                    'supsystic-social-sharing-left':   {
                        'transition': 'translateX(-160px)',
                        'display':    'block'
                    },
                    'supsystic-social-sharing-top':    {
                        'transition': 'translateY(-160px)',
                        'display':    'inline-block'
                    },
                    'supsystic-social-sharing-bottom': {
                        'transition': 'translateY(160px)',
                        'display':    'inline-block'
                    }
                },
                buttonsTransition = null;

            var getAnimationClasses = function (animation) {
                return 'animated ' + animation;
            };

            var checkNavOrientation = function ($c) {
                $.each(transitionHelper, function (index, value) {
                    if ($.inArray(index, $c.attr('class').split(' ')) > -1) {
                        $c.find('.nav-button').css({
                            'display': value['display']
                        });

                        buttonsTransition = value['transition'];
                    }
                });
            };

            var initNetworksPopup = function () {
                var $networksContainer = $('.networks-list-container'),
                    $button = $('.list-button');

                $button.on('click', function () {
                    $networksContainer.removeClass('hidden')
                        .bPopup({
                            position: [0, 200]
                        });
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
				var pinterestBtn = $buttons.filter('.pinterest');
				if(pinterestBtn && pinterestBtn.size()) {
					var $img = sssFindMostImportantImg();
					if($img) {
						pinterestBtn.attr('href', pinterestBtn.attr('href')+ '&media='+ encodeURIComponent($img.attr('src')));
					}
				}
            }

            if (buttonChangeSize == 'on') {
                var buttonWidth = $('.sharer-flat').width();

                //$container.addClass('chage-size');

                $('div.supsystic-social-sharing[data-change-size="on"] .sharer-flat').on('mouseover', function () {
                    $(this).css('width', buttonWidth - buttonWidth / 4);
                }).on('mouseleave', function () {
                    $(this).css('width', '');
                });
            }

            checkNavOrientation($container);
            $navButton.on('click', function () {
                if ($(this).hasClass('hide')) {
                    $(this).removeClass('hide').addClass('show');

                    $container
                        .find('a').css('transform', buttonsTransition);

                    $container
                        .find('.list-button').css('transform', buttonsTransition);
                } else {
                    $(this).addClass('hide').removeClass('show');

                    $container.find('a').css('transform', 'translateX(0)');

                    $container
                        .find('.list-button').css('transform', 'translateX(0)');
                }
            });

            initNetworksPopup();

            $printButton.on('click', function () {
                window.print();
            });

            $bookmarkButton.on('click', function () {
                if (window.sidebar && window.sidebar.addPanel) { // Mozilla Firefox Bookmark
                    window.sidebar.addPanel(document.title, window.location.href, '');
                } else if (window.external && ('AddFavorite' in window.external)) { // IE Favorite
                    window.external.AddFavorite(location.href, document.title);
                } else if (window.opera && window.print) { // Opera Hotlist
                    this.title = document.title;
                    return true;
                } else { // webkit - safari/chrome
                    alert('Press ' + (navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Command/Cmd' : 'CTRL') + ' + D to bookmark this page.');
                }
            });

            $mailButton.on('click', function () {
                var url = '<a href="' + window.location.href + '">' + document.title + '</a>';
                window.open('mailto:adresse@example.com?subject=' + document.title + '&body=' + url);
            });

            $('a.tooltip').tooltipster({
                animation: 'swing',
                position:  'bottom',
                theme:     'tooltipster-shadow'
            });

            if ($container.hasClass('supsystic-social-sharing-mobile')) {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    $container.hide();
                } else {
                    $container.show();
                }
            } else {
                if (!$container.hasClass('supsystic-social-sharing-click')) {
                    $container.show();
                }
            }

            $container.addClass('supsystic-social-sharing-loaded');
        };

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

        // Init social sharing.
        $('.supsystic-social-sharing').each(function (index, el) {
            window.initSupsysticSocialSharing(el);
        });
    });

}(window.jQuery, window));
function sssFindMostImportantImg() {
	var $img = null;
	var findWhere = ['article', '.entry-content', 'body'];
	for(var i = 0; i < findWhere.length; i++) {
		$img = _sssFindImg( jQuery(findWhere[i]) );
		if($img)
			break;
	}
	return $img;
}
function _sssFindImg($el) {
	if($el && $el.size()) {
		var $img = null;
		$el.each(function(){
			$img = jQuery(this).find('img');
			if($img && $img.size()) {
				return false;
			}
		});
		return $img && $img.size() ? $img : false;
	}
	return false;
}