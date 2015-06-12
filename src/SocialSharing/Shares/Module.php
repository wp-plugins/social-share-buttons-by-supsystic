<?php

/**
 * Class SocialSharing_Shares_Module
 *
 * Sharing module.
 *
 * Handles AJAX requests and clicks for sharing buttons.
 * Builds the statistics for the projects.
 */
class SocialSharing_Shares_Module extends SocialSharing_Core_BaseModule
{
    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        parent::onInit();

        $dispatcher = $this->getEnvironment()->getDispatcher();
        $dispatcher->on('after_ui_loaded', array($this, 'loadShareScripts'));
        $dispatcher->on('after_ui_loaded', array($this, 'loadChartScripts'));
        $dispatcher->on('before_build', array($this, 'filterAddProjectShares'));

        $shareRequestHandler = array($this, 'handleShareRequest');
        $viewRequestHandler = array($this, 'handleViewRequest');
        add_action('wp_ajax_social-sharing-share', $shareRequestHandler);
        add_action('wp_ajax_social-sharing-view', $viewRequestHandler);
        add_action('wp_ajax_nopriv_social-sharing-share', $shareRequestHandler);
    }

    /**
     * Handles the AJAX request for share buttons.
     * @return Rsc_Http_Response
     */
    public function handleShareRequest()
    {
        $request = $this->getRequest();
        $controller = $this->getController();
        $action = 'saveAction';
        $callableHandler = array($controller, $action);

        return call_user_func_array($callableHandler, array($request));
    }

    /**
     * Handles the AJAX request for view buttons.
     * @return Rsc_Http_Response
     */
    public function handleViewRequest()
    {
        $request = $this->getRequest();
        $controller = $this->getController();
        $action = 'saveViewsAction';
        $callableHandler = array($controller, $action);

        return call_user_func_array($callableHandler, array($request));
    }

    /**
     * Filters the each project and adds the number of the shares to each network.
     * @param SocialSharing_Projects_Project $project
     * @return SocialSharing_Projects_Project
     */
    public function filterAddProjectShares(SocialSharing_Projects_Project $project)
    {
        if (count($project->getNetworks()) === 0) {
            return $project;
        }

        $networks = array();

        /** @var SocialSharing_Shares_Model_Shares $shares */
        $shares = $this->getModelsFactory()->get('shares');
        foreach ($project->getNetworks() as $index => $network) {
            if (!in_array(
                    $project->get('shares'),
                    array('plugin', 'project', 'post'),
                    false
                )
            ) {
                $network->shares = 0;
            } elseif ($project->get('shares') === 'plugin') {
                $network->shares = $shares->getNetworkShares($network->id);
            } elseif ($project->get('shares') === 'project') {
                $network->shares = $shares->getProjectNetworkShares(
                    $project->getId(),
                    $network->id
                );
            } elseif ($project->get('shares') === 'post') {
//                $postId = !is_home() ? get_the_ID() : null;

                $schema = is_ssl() ? 'https://' : 'http://';
                $currentUrl = strtolower(trailingslashit($schema . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']));
                $baseUrl = strtolower(trailingslashit(get_bloginfo('wpurl')));

                $postId = $currentUrl === $baseUrl ? null : get_the_ID();

                $network->shares = $shares->getProjectPageShares(
                    $project->getId(),
                    $network->id,
                    $postId
                );
            } else {
                $network->shares = 0;
            }

            $networks[$index] = $network;
        }

        $project->setNetworks($networks);

        return $project;
    }

    /**
     * Loads scripts that handles clicks for the share buttons.
     * @param SocialSharing_Ui_Module $ui
     */
    public function loadShareScripts(SocialSharing_Ui_Module $ui)
    {
        $version = $this->getEnvironment()->getConfig()->get('plugin_version');

        $ui->addAsset($ui->create('script', 'jquery'));
        $ui->addAsset(
            $ui->create('script', 'social-sharing-share')
                ->addDependency('jquery')
                ->setHookName('wp_enqueue_scripts')
                ->setModuleSource($this, 'js/share.js')
                ->setVersion($version)
        );
    }

    /**
     * Loads backend scripts to build the charts for the statistic page.
     * @param SocialSharing_Ui_Module $ui
     */
    public function loadChartScripts(SocialSharing_Ui_Module $ui)
    {
        $hookName = 'admin_enqueue_scripts';

        $ui->addAsset(
            $ui->create('script', 'sss-chartjs')
                ->setHookName($hookName)
                ->setModuleSource($this, 'js/Chart.min.js')
                ->setVersion('master')
        );

        $ui->addAsset(
            $ui->create('script', 'sss-shares-statistic')
                ->setHookName($hookName)
                ->setModuleSource($this, 'js/shares.statistic.js')
        );

        $ui->addAsset(
            $ui->create('style', 'sss-shares-statistic')
                ->setHookName($hookName)
                ->setModuleSource($this, 'css/shares.statistic.css')
        );

        if (!$this->getEnvironment()->isModule('shares', 'statistic')) {
            return;
        }

        $ui->addAsset(
            $ui->create('script', 'jquery')
                ->setHookName($hookName)
        );
    }
}