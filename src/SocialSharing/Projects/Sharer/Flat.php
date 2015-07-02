<?php


class SocialSharing_Projects_Sharer_Flat extends SocialSharing_Projects_Sharer
{

    /**
     * Builds the networks buttons.
     * @return string
     */
    protected function buildButtons($networks = null, &$classes = array())
    {
        // Current post
        $current = $this->isHome() ? null : get_post();
        // Buttons classes
        $classes = array('sharer-flat', 'sharer-'.$this->getProject()->get('design', 'flat-1'), $this->getProject()->get('grad', '') ? 'grad' : '');
        // Buttons
        $buttons = array();
        // Use short numbers or not
        $shortNumbers = false;
        $savedData = get_option('networks_tooltips_' . $this->getProject()->getId());
        $networkName = get_option('networks_titles_' . $this->getProject()->getId());

        if (!$this->getProject()->isDisplayTotalShares()) {
            $classes[] = 'without-counter';
        }

        if ($this->getProject()->isShortNumbers()) {
            $shortNumbers = true;
        }

        $networks = $networks ? $this->getEnvironment()->getModule('networks')->getController()->getModelsFactory()->get('networks')->all() : $this->getProject()->getNetworks();

        foreach ($networks as $network) {
            $finalClasses = array_merge($classes, array($network->class));
            $totalShares = $shortNumbers ? $this->humanizeTotalShares((isset($network->shares) ? $network->shares : 0)) : (isset($network->shares) ? $network->shares : 0);
            $buttonName = ((isset($networkName[$network->id]) && $networkName[$network->id]) ? $networkName[$network->id] : null);

            if(isset($savedData[$network->id]) && $savedData[$network->id]) {
                array_push($finalClasses, 'tooltip');
            }

            $button = $this->getBuilder()
                ->createElement(
                    'a',
                    array(
                        $this->getBuilder()->createAttribute(
                            'class',
                            $finalClasses
                        ),
                        $this->getBuilder()->createAttribute(
                            'href',
                            $this->replaceVars($network->url)
                        ),
                        $this->getBuilder()->createAttribute(
                            'target',
                            '_blank'
                        ),
                        $this->getBuilder()->createAttribute(
                            'data-nid',
                            $network->id
                        ),
                        $this->getBuilder()->createAttribute(
                            'data-pid',
                            $this->getProject()->getId()
                        ),
                        $this->getBuilder()->createAttribute(
                            'data-post-id',
                            $current ? $current->ID : null
                        ),
                        $this->getBuilder()->createAttribute(
                            'title',
                            isset($savedData[$network->id]) ? $savedData[$network->id] : null
                        ),
                        $this->getBuilder()->createAttribute(
                            'data-url',
                            admin_url('admin-ajax.php')
                        )
                    )
                )
                ->addElement(
                    $this->getBuilder()->createElement(
                        'i',
                        array(
                            $this->getBuilder()->createAttribute(
                                'class',
                                $this->getFontAwesomeIcon($network)
                            )
                        )
                    )->addElement(
                         $this->getBuilder()->createTextElement(
                              (($this->getProject()->get('design', 'flat-1') == 'flat-8' || $this->getProject()->get('design', 'flat-1') == 'flat-9')
                                  ? ($buttonName ? $buttonName : $network->class) : ''))
                    )
                )
                ->addElement(
                    $this->getBuilder()->createElement(
                        'div',
                        array(
                            $this->getBuilder()->createAttribute(
                                'class',
                                'counter-wrap ' . ($this->getProject()->get('shares_style', '') ? $this->getProject()->get('shares_style', '') : 'standard')
                            )
                        )
                    )->addElement(
                        $this->getBuilder()->createElement(
                            'span',
                            array(
                                $this->getBuilder()->createAttribute(
                                    'class',
                                    'counter'
                                )
                            )
                        )->addElement(
                            $this->getBuilder()->createTextElement($totalShares)
                        )
                    )
                )
            ;

            $buttons[] = $button;
        }

        return $buttons;
    }

    /**
     * Returns sharer unique name.
     * @return string
     */
    protected function getName()
    {
        return 'flat';
    }

    protected function getFontAwesomeIcon($network)
    {
        $classes = array('fa', 'fa-fw');

        switch ($network->class) {
            case 'facebook':
                $classes[] = 'fa-facebook';
                break;
            case 'twitter':
                $classes[] = 'fa-twitter';
                break;
            case 'googleplus':
                $classes[] = 'fa-google-plus';
                break;
            case 'vk':
                $classes[] = 'fa-vk';
                break;
            case 'like':
                $classes[] = 'fa-heart';
                break;
            case 'reddit':
                $classes[] = 'fa-reddit';
                break;
            case 'pinterest':
                $classes[] = 'fa-pinterest';
                break;
            case 'digg':
                $classes[] = 'fa-digg';
                break;
            case 'stumbleupon':
                $classes[] = 'fa-stumbleupon';
                break;
            case 'delicious':
                $classes[] = 'fa-delicious';
                break;
            case 'livejournal':
                $classes[] = 'fa-pencil';
                break;
            case 'odnoklassniki':
                $classes[] = 'fa-ok';
                break;
            case 'linkedin':
                $classes[] = 'fa-linkedin';
                break;
            case 'print':
                $classes[] = 'fa-print';
                break;
            case 'bookmark':
                $classes[] = 'fa-plus';
                break;
            case 'mail':
                $classes[] = 'fa-comment';
                break;
            case 'evernote':
                $classes[] = 'bd-evernote';
                break;
        }

        return $classes;
    }
}