<?php

/**
 * Class SocialSharing_Projects_Sharer_Zocial
 */
class SocialSharing_Projects_Sharer_Zocial extends SocialSharing_Projects_Sharer
{

    /**
     * Builds the networks buttons.
     * @return string
     */
    protected function buildButtons()
    {
        // Current post
        $current = get_post();
        // Buttons classes
        $classes = array('zocial');
        // HTML string
        $buttons = '';
        // Use short numbers or not
        $shortNumbers = false;

        if (!$this->getProject()->isDisplayTotalShares()) {
            $classes[] = 'icon';
        }

        if ($this->getProject()->isShortNumbers()) {
            $shortNumbers = true;
        }

        foreach ($this->getProject()->getNetworks() as $network) {
            $finalClasses = array_merge($classes, array($network->class));

            $totalShares = $shortNumbers ? $this->humanizeTotalShares($network->total_shares) : $network->total_shares;

            $buttons .= '<a href="' . $this->replaceVars(
                    $network->url,
                    $current
                ) . '" ' . $this->getClassString(
                    $finalClasses
                ) . '" title="Share it on ' . $network->name . '" target="_blank" data-nid="' . $network->id . '" data-url="' . admin_url(
                    'admin-ajax.php'
                ) . '">' . $totalShares . '</a>';
        }

        return $buttons;
    }

    /**
     * Returns sharer unique name.
     * @return string
     */
    protected function getName()
    {
        return 'zocial';
    }
}