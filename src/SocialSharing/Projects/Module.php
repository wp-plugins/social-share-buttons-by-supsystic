<?php

/**
 * Class SocialSharing_Projects_Module
 *
 * Allows to manage sharing projects in the plugin.
 */
class SocialSharing_Projects_Module extends SocialSharing_Core_BaseModule
{
    /**
     * Module initialization.
     */
    public function onInit()
    {
        parent::onInit();

        $dispatcher = $this->getEnvironment()->getDispatcher();
        $dispatcher->on('after_ui_loaded', array($this, 'onUiLoaded'));
        $dispatcher->on('after_modules_loaded', array($this, 'doFrontendStuff'));

        $projects = $this->getController()->getModelsFactory()->get('projects');

        $dispatcher->on('project_get', array($projects, 'filterGetProject'));

        add_shortcode('supsystic-social-sharing', array($this, 'doShortcode'));
    }

    /**
     * Fires on after module 'UI' loaded.
     * Loads module assets.
     * @param SocialSharing_Ui_Module $ui UI Module
     */
    public function onUiLoaded(SocialSharing_Ui_Module $ui)
    {
        $environment = $this->getEnvironment();
        $version = $environment->getConfig()->get('plugin_version');
        $hookName = 'admin_enqueue_scripts';

        $ui->addAsset($ui->create('style', 'sss-base-admin')
            ->setModuleSource($this, 'css/base.css')
            ->setHookName($hookName)
        );

        $ui->addAsset($ui->create('style', 'sss-base')
            ->setModuleSource($this, 'css/base.css')
            ->setHookName('wp_enqueue_scripts')
        );

        $ui->addAsset($ui->create('script', 'jquery'));

        $ui->addAsset(
            $ui->create('script', 'sss-frontend')
                ->setModuleSource($this, 'js/frontend.js')
                ->setHookName('wp_enqueue_scripts')
                ->addDependency('jquery')
        );

        $ui->addAsset(
            $ui->create('script', 'sss-jquery-mouseWheel')
                ->setExternalSource(
                    'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.12/jquery.mousewheel.js'
                )
                ->setHookName($hookName)
                ->setVersion('3.1.12')
        );

        $ui->addAsset(
            $ui->create('script', 'sss-scroll-controller')
                ->setModuleSource($this, 'js/scroll.js')
                ->setHookName($hookName)
                ->setVersion($version)
        );

        $ui->addAsset(
            $ui->create('script', 'sss-networks-controller')
                ->setModuleSource($this, 'js/networks.js')
                ->setHookName($hookName)
                ->setVersion($version)
        );

        $ui->addAsset(
            $ui->create('style', 'sss-projects-styles')
                ->setModuleSource($this, 'css/projects.css')
                ->setHookName($hookName)
                ->setVersion($version)
        );

        if ($environment->isModule('projects', 'index')) {
            $ui->addAsset($ui->create('script', 'jquery-ui-dialog'));

            $ui->addAsset(
                $ui->create('script', 'sss-projects-index')
                    ->setHookName($hookName)
                    ->setModuleSource($this, 'js/index.js')
                    ->setVersion($version)
                    ->addDependency('jquery-ui-dialog')
            );
        }

        // Load only on on admin projects/view or /add
        if ($environment->isModule('projects')
            && ($environment->isAction('view') || $environment->isAction('add'))
        ) {
            $ui->addAsset(
                $ui->create('script', 'jquery-ui-dialog')
            );

            $ui->addAsset(
                $ui->create('script', 'jquery-ui-sortable')
            );

            $ui->addAsset(
                $ui->create('script', 'sss-projects-edit')
                    ->setModuleSource($this, 'js/projects.edit.js')
                    ->setHookName($hookName)
                    ->setVersion($version)
                    ->addDependency('jquery-ui-dialog')
                    ->addDependency('jquery-ui-sortable')
            );
        }

    }

    public function doFrontendStuff()
    {
        $projects = $this->getController()
            ->getModelsFactory()
            ->get('projects', $this)
            ->all();

        if (!is_array($projects) || count($projects) === 0) {
            return;
        }

        foreach ($projects as $project) {
            $this->handleProject($project);
        }
    }

    public function handleProject($project)
    {
        $project = new SocialSharing_Projects_Project((array)$project);
        $sharer = new SocialSharing_Projects_Sharer_Flat(
            $project,
            $this->getEnvironment()
        );

        if ($project->isShowAtShortcode() || $project->isShowAt('popup')) {
            return $sharer->build();
        }

        $sharer->activate();

        return null;
    }

    public function doShortcode($attributes)
    {
        $isDebug = defined('WP_DEBUG') && WP_DEBUG;

        if (!array_key_exists('id', $attributes)) {
            if ($isDebug) {
                throw new Exception('ID is not specified.');
            }

            return;
        }

        $project = $this->getController()
            ->getModelsFactory()
            ->get('projects', $this)
            ->get($attributes['id']);

        if (!$project) {
            if ($isDebug) {
                throw new Exception('Project not found.');
            }

            return;
        }

        return $this->handleProject($project);
    }
}