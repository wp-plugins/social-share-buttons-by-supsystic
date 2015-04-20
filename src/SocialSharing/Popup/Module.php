<?php

/**
 * Class SocialSharing_Popup_Module
 */
class SocialSharing_Popup_Module extends SocialSharing_Core_Module
{
    /**
     * @var string
     */
    private $frameClass;

    /**
     * @var int
     */
    private $templateId;

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        parent::onInit();
        $this->frameClass = 'framePps';
        $this->templateId = 10;

//        dump($this->getModel()->getById(38)['params']);
//        dump($this->getDefaultPopupSettings());
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return class_exists('framePps', false);
    }

    /**
     * Converts current settings to the Supsystic Popup format.
     * @param int $id
     * @param array $settings
     * @return array
     */
    public function getPopupSettings($id, array $settings)
    {
        $popup = $this->getDefaultPopupSettings();

        $popup['id'] = $settings['popup_id'];
        $popup['params']['tpl']['txt_0'] = '[supsystic-social-sharing id="'.$id.'"]';

        if ($settings['when_show'] === 'click') {
            $popup['params']['main']['show_on'] = 'click_on_page';
        }

        if (isset($settings['show_on_posts']) && count($settings['show_on_posts']) > 0) {
            $popup['params']['main']['show_pages'] = 'show_on_pages';
            $popup['show_pages_list'] = array();

            // Popup hasn't options like "All posts" or "All pages"
            // So we select all posts or pages manually.
            if (in_array(-1, $settings['show_on_posts'], false)) {
                $popup['show_pages_list'] = array_merge(
                    $popup['show_pages_list'],
                    $this->extractPostIdentifiers(
                        get_posts(array('posts_per_page' => -1))
                    )
                );
            }

            if (in_array(-2, $settings['show_on_posts'], false)) {
                $popup['show_pages_list'] = array_merge(
                    $popup['show_pages_list'],
                    $this->extractPostIdentifiers(
                        get_pages(array('posts_per_page' => -1))
                    )
                );
            }

            $popup['show_pages_list'] = array_merge(
                $popup['show_pages_list'],
                $settings['show_on_posts']
            );
        }

        return $popup;
    }

    /**
     * Call method from the Supsystic Popup frame class.
     * @param string $method Method name.
     * @param array $arguments An array of the arguments.
     * @return mixed
     */
    public function call($method, array $arguments = array())
    {
        if (!method_exists($this->getInstance(), $method)) {
            throw new BadMethodCallException(
                sprintf(
                    $this->getEnvironment()->translate(
                        'Call to undefined method %1$s for %2$s.'
                    ),
                    $method,
                    $this->frameClass
                )
            );
        }

        return call_user_func_array(
            array($this->getInstance(), $method),
            $arguments
        );
    }

    /**
     * Returns popup module from the Supsystic Popup.
     * @return popupPps
     */
    public function getModule()
    {
        return $this->call('getModule', array('popup'));
    }

    /**
     * Returns popup model from the Supsystic Popup.
     * @return popupModelPps
     */
    public function getModel()
    {
        return $this->getModule()->getModel();
    }

    /**
     * Returns FrameClass.
     * @return string
     */
    public function getFrameClass()
    {
        return $this->frameClass;
    }

    /**
     * Sets FrameClass.
     * @param string $frameClass
     */
    public function setFrameClass($frameClass)
    {
        $this->frameClass = $frameClass;
    }

    /**
     * Returns TemplateId.
     * @return int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Sets TemplateId.
     * @param int $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    protected function getInstance()
    {
        if (!$this->isInstalled()) {
            throw new RuntimeException(
                'Failed to get Popup instance. Plugin not activated.'
            );
        }

        return call_user_func_array(array($this->frameClass, '_'), array());
    }

    protected function getDefaultPopupSettings()
    {
        return array(
            'params' =>
                array(
                    'main'       =>
                        array(
                            'show_on'                           => 'page_load',
                            'show_on_page_load_delay'           => '',
                            'show_on_scroll_window_delay'       => '0',
                            'show_on_scroll_window_perc_scroll' => '0',
                            'close_on'                          => 'user_close',
                            'show_to'                           => 'everyone',
                            'show_pages'                        => 'all',
                        ),
                    'tpl'        =>
                        array(
                            'width'                           => '400',
                            'width_measure'                   => 'px',
                            'bg_overlay_opacity'              => '0.5',
                            'bg_type_0'                       => 'color',
                            'bg_img_0'                        => '',
                            'bg_color_0'                      => '#cccccc',
                            'bg_type_1'                       => 'color',
                            'bg_img_1'                        => '',
                            'bg_color_1'                      => '#ff0000',
                            'font_label'                      => 'default',
                            'font_txt'                        => 'default',
                            'font_footer'                     => 'default',
                            'close_btn'                       => 'sqr_close',
                            'bullets'                         => 'lists_green',
                            'sub_dest'                        => 'wordpress',
                            'sub_wp_create_user_role'         => 'subscriber',
                            'sub_aweber_listname'             => '',
                            'sub_mailchimp_api_key'           => '',
                            'sub_txt_confirm_sent'            => 'Confirmation link was sent to your email address. Check your email!',
                            'sub_txt_success'                 => 'Thank you for subscribe!',
                            'sub_txt_invalid_email'           => 'Empty or invalid email',
                            'sub_redirect_url'                => '',
                            'sub_txt_confirm_mail_subject'    => 'Confirm subscription on [sitename]',
                            'sub_txt_confirm_mail_from'       => 'admin@example.com',
                            'sub_txt_confirm_mail_message'    => 'You subscribed on site <a href="[siteurl]">[sitename]</a>. Follow <a href="[confirm_link]">this link</a> to complete your subscription. If you did not subscribe here - just ignore this message.',
                            'sub_txt_subscriber_mail_subject' => '[sitename] Your username and password',
                            'sub_txt_subscriber_mail_from'    => 'admin@example.com',
                            'sub_txt_subscriber_mail_message' => 'Username: [user_login]<br />Password: [password]<br />[login_url]',
                            'sub_btn_label'                   => '√',
                            'enb_sm_facebook'                 => '1',
                            'enb_sm_googleplus'               => '1',
                            'enb_sm_twitter'                  => '1',
                            'sm_design'                       => 'boxy',
                            'anim_key'                        => 'none',
                            'anim_duration'                   => '',
                            'layered_pos'                     => '',
                            'label'                           => '',
                            'enb_txt_0'                       => '1',
                            'foot_note'                       => '',
                            'txt_0'                           => '',
                        ),
                    'opts_attrs' =>
                        array(
                            'bg_number'        => '2',
                            'txt_block_number' => '1',
                        ),
                )
        );
    }

    protected function extractPostIdentifiers(array $posts, $property = 'ID')
    {
        $identifiers = array();

        if (count($posts) > 0) {
            foreach ($posts as $post) {
                if (is_object($post) && property_exists($post, $property)) {
                    $identifiers[] = $post->{$property};
                } elseif (is_array($post) && array_key_exists($property, $post)) {
                    $identifiers[] = $post[$property];
                }
            }
        }

        return $identifiers;
    }
}