<?php


abstract class SocialSharing_Projects_Sharer
{
    /**
     * @var SocialSharing_Projects_Project
     */
    private $project;

    /**
     * @var Rsc_Environment
     */
    private $environment;

    /**
     * @var SocialSharing_HtmlBuilder_Module
     */
    private $builder;

    /**
     * @var Rsc_Dispatcher
     */
    private $dispatcher;

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

        if ((!array_key_exists('action', $_GET) || $_GET['action'] !== 'getPreviewHtml') && $this->project->isShowOnPosts()) {
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

        return $container->build();
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