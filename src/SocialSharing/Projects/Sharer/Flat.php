<?php


class SocialSharing_Projects_Sharer_Flat extends SocialSharing_Projects_Sharer
{

    /**
     * Builds the networks buttons.
     * @return string
     */
    protected function buildButtons()
    {
        // Current post
        $current = $this->isHome() ? null : get_post();
        // Buttons classes
        $classes = array('sharer-flat', 'sharer-'.$this->getProject()->get('design', 'flat-1'));
        // Buttons
        $buttons = array();
        // Use short numbers or not
        $shortNumbers = false;

        if (!$this->getProject()->isDisplayTotalShares()) {
            $classes[] = 'without-counter';
        }

        if ($this->getProject()->isShortNumbers()) {
            $shortNumbers = true;
        }

        foreach ($this->getProject()->getNetworks() as $network) {
            $finalClasses = array_merge($classes, array($network->class));
            $totalShares = $shortNumbers ? $this->humanizeTotalShares($network->shares) : $network->shares;

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
                    )
                )
                ->addElement(
                    $this->getBuilder()->createElement(
                        'div',
                        array(
                            $this->getBuilder()->createAttribute(
                                'class',
                                'counter-wrap'
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
                );

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
        }

        return $classes;
    }
}