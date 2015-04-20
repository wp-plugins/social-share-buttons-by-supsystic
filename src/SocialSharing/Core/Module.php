<?php

/**
 * Class SocialSharing_Core_Module
 */
class SocialSharing_Core_Module extends SocialSharing_Core_BaseModule
{
    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        parent::onInit();

        $dispatcher = $this->getEnvironment()->getDispatcher();
        $dispatcher->on('after_ui_loaded', array($this, 'loadScripts'), -5);
        $dispatcher->on('after_modules_loaded', array($this, 'disablePromo'), -5);
    }

    /**
     * Loads plugin core js.
     * @param SocialSharing_Ui_Module $ui
     */
    public function loadScripts(SocialSharing_Ui_Module $ui)
    {
        $core = new SocialSharing_Ui_Script();
        $core->setHandle('social-sharing-core-js')
            ->setModuleSource($this, 'js/core.js')
            ->setHookName('admin_enqueue_scripts');

        $ui->addAsset($core);
    }

    /**
     * Disable build-in promo module.
     */
    public function disablePromo()
    {
        $pluginName = $this->getEnvironment()->getPluginName();
        update_option($pluginName.'_promo_shown', 1);
    }
}