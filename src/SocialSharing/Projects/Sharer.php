<?php

abstract class SocialSharing_Projects_Sharer
{
    /**
     * @var SocialSharing_Projects_Project
     */
    protected $project;

    /**
     * @var Rsc_Environment
     */
    protected $environment;

    /**
     * @var SocialSharing_HtmlBuilder_Module
     */
    protected $builder;

    /**
     * @var Rsc_Dispatcher
     */
    protected $dispatcher;

    /**
     * Sharer constructor.
     * @param SocialSharing_Projects_Project $project
     * @param Rsc_Environment $environment
     */
    public function __construct(SocialSharing_Projects_Project $project, Rsc_Environment $environment)
    {
        $this->project = $project;
        $this->environment = $environment;
        $this->builder = $environment->getModule('HtmlBuilder');
        $this->dispatcher = $environment->getDispatcher();
    }

    /**
     * Returns Project.
     * @return SocialSharing_Projects_Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Sets Project.
     * @param SocialSharing_Projects_Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return SocialSharing_HtmlBuilder_Module
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * Returns Dispatcher.
     * @return Rsc_Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Sets Dispatcher.
     * @param Rsc_Dispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function activate()
    {
        if ($this->project->isShowAtWidget()) {
            $this->action('widgets_init', 'applyWidget');
        }

        if (!is_admin()) {
            if ($this->project->isShowAtSidebar()) {
                $this->action('wp_footer', 'applySidebar');
            }

            if ($this->project->isShowAtContent()) {
                $this->filter('the_content', 'applyContent');
            }
        }
    }

    public function applySidebar()
    {
        echo $this->build();
    }

    public function applyWidget()
    {
        global $wp_widget_factory;

        $className = 'SocialSharing_Projects_Widget';
        $widget = $className.'_'.$this->project->getId();

        $wp_widget_factory->widgets[$widget] = new $className($this);
    }

    public function applyContent($content)
    {
        $content = sprintf('%1$s%2$s%3$s',
            ('above' === $this->project->getExtra() || 'above_below' === $this->project->getExtra())
                ? $this->build()
                : '',
            $content,
            ('below' === $this->project->getExtra() || 'above_below' === $this->project->getExtra())
                ? $this->build()
                : ''
        );

        return $content;
    }

    public function build()
    {
        $this->project = $this->dispatcher->apply('before_build', array($this->project));

        $classes = $this->getBaseClasses();
        $buttons = $this->buildButtons();

        $sidebarClasses = array('supsystic-social-sharing-right', 'supsystic-social-sharing-left', 'supsystic-social-sharing-top', 'supsystic-social-sharing-bottom');

        if ((!array_key_exists('action', $_GET) || $_GET['action'] !== 'getPreviewHtml') && $this->project->isShowOnPosts() && !$this->project->isShowAt('popup')) {
            $current = get_post();

            if ($current === null) {
                return '';
            }
			
            if ($current->post_type === 'post'
                && (!$this->project->isShowOnAllPosts()
                    && !$this->project->isShowOnSpecificPost($current->ID))
            ) {
                return '';
            }

            if ($current->post_type === 'page'
                && (!$this->project->isShowOnAllPages()
                    && !$this->project->isShowOnSpecificPost($current->ID))
            ) {
                return '';
            }
			/*For other post types that can't be selected in our list, and if buttons should not be visible on all posts/pages - buttons should be hidden, right?*/
			if(!$this->project->isShowOnAllPosts() 
				&& !$this->project->isShowOnAllPages() 
				&& !in_array($current->post_type, array('post', 'page'))
			) {
				return '';
			}
        }
        $container = $this->getBuilder()->createElement(
            'div',
            array(
                $this->getBuilder()->createAttribute('class', $classes),
                $this->getBuilder()->createAttribute(
                    'data-animation',
                    $this->getProject()->get('buttons_animation', 'no-animation')
                ),
                $this->getBuilder()->createAttribute(
                    'data-icons-animation',
                    $this->getProject()->get('icons_animation', 'no-animation')
                ),
                $this->getBuilder()->createAttribute(
                    'data-overlay',
                    $this->getProject()->get('overlay_with_shadow', false)
                ),
                $this->getBuilder()->createAttribute(
                    'data-change-size',
                    $this->getProject()->get('change_size', false)
                ),
                $this->getBuilder()->createAttribute(
                    'data-buttons-size',
                    $this->getProject()->get('buttons_size', 'normal')
                ),
                $this->getBuilder()->createAttribute(
                    'style',
                    sprintf(
                        'font-size: %sem; display: none;',
                        $this->getProject()->get('buttons_size', 1)
                    )
                )
            )
        );

        if (count($buttons) > 0) {
            foreach ($buttons as $button) {
                $container->addElement($button);
            }
        }

        $settings = $this->project->getSettings();
        $listClasses = array();
        $buttons = $this->buildButtons('all', $listClasses);

        if(isset($settings['show_more']) && $settings['show_more'] == 'on') {
            $this->showMore($container, $buttons, $listClasses);
        }

        foreach($classes as $class) {
            if(in_array($class, $sidebarClasses) && isset($settings['sidebar_navigation']) && $settings['sidebar_navigation'] == 'on') {
                $navButton = $this->getBuilder()->createElement('div', array($this->getBuilder()->createAttribute('class', 'nav-button hide ' . $settings['where_to_show_extra'])));
                $container->addElement($navButton);
            }
        }

        $this->dispatcher->dispatch('before_html_build');

        return $container->build();
    }

    protected function showMore($container, $buttons, $listClasses) {
        $networksList = $this->getBuilder()->createElement('div',
            array($this->getBuilder()->createAttribute('class', 'networks-list-container supsystic-social-sharing hidden'))
        );

        if (count($buttons) > 0) {
            foreach ($buttons as $button) {
                $networksList->addElement($button);
            }
        }

        $listButtonClasses = 'list-button ' . implode(' ', $listClasses);
        $listButton = $this->getBuilder()->createElement('div', array(
            $this->getBuilder()->createAttribute('class', $listButtonClasses))
        );

        $container->addElement($networksList);
        $container->addElement($listButton);
    }

    protected function filter($hook, $method)
    {
        add_filter($hook, array($this, $method));
    }

    protected function action($hook, $method)
    {
        add_action($hook, array($this, $method));
    }

    protected function getBaseClasses()
    {
        $classes = array(
            'supsystic-social-sharing',
            'supsystic-social-sharing-package-'.$this->getName()
        );

        if ($this->project->isHiddenOnMobile()) {
            $classes[] = 'supsystic-social-sharing-mobile';
        }

        if ($this->project->isShowOnClick()) {
            $classes[] = 'supsystic-social-sharing-click';
        }

        if ($this->project->get('spacing', 'off') === 'on') {
            $classes[] = 'supsystic-social-sharing-spacing';
        }

        if ($this->project->isShowAtSidebar()) {
            $classes[] = 'supsystic-social-sharing-fixed';

            $position = $this->project->getExtra() ? $this->project->getExtra() : 'left';
            $classes[] = 'supsystic-social-sharing-'.$position;

            if (is_admin_bar_showing()) {
                $classes[] = 'supsystic-social-sharing-adminbar';
            }
        } else {
            $classes[] = 'supsystic-social-sharing-content';
        }

        return $classes;
    }

    protected function getClassString(array $classes = array())
    {
        if (count($classes) === 0) {
            return '';
        }

        return 'class="'.implode(' ', $classes).'"';
    }

    protected function isHome()
    {
        $schema = is_ssl() ? 'https://' : 'http://';
        $currentUrl = strtolower(trailingslashit($schema . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']));
        $baseUrl = strtolower(trailingslashit(get_bloginfo('wpurl')));

        return $currentUrl === $baseUrl;
    }

    /**
     * Replaces variables from the network URLs.
     * @param string $url
     * @return string
     */
    protected function replaceVars($url)
    {
        $isHome = $this->isHome();

        $replace = array(
            '{url}'   => $isHome ? urlencode(get_bloginfo('wpurl')) : urlencode(get_permalink()),
            '{title}' => $isHome ? get_bloginfo('name') : get_the_title()
        );

        return strtr($url, $replace);
    }

    /**
     * Converts 1000 to 1k, etc.
     * @param int $value
     * @return string
     */
    protected function humanizeTotalShares($value)
    {
        if ($value < 1000) {
            return $value;
        }

        $divider = 1000;
        $letter = 'k';

        if ($value >= 1000000) {
            $divider = 1000000;
            $letter = 'm';
        }

        $value = number_format($value / $divider, 1);

        return $value . $letter;
    }

    public static function create($type)
    {
        switch (strtolower($type)) {
            case 'flat-1':

        }
    }

    /**
     * Builds the networks buttons.
     * @return array
     */
    abstract protected function buildButtons();

    /**
     * Returns sharer unique name.
     * @return string
     */
    abstract protected function getName();
}