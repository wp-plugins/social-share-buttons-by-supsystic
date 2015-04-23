<?php

/**
 * Class SocialSharing_Updater_Module
 *
 * Updates the database schema when user downloads the new version
 * of the plugin from the WordPress.org.
 */
class SocialSharing_Updater_Module extends SocialSharing_Core_BaseModule
{
    const CURRENT_REVISION_KEY = '_social_sharing_rev';

    /**
     * @var SocialSharing_Updater_UpdatesLoader
     */
    private $updatesLoader;

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        parent::onInit();

        $revision = $this->getCurrentRevision();
        $installed = $this->getInstalledRevision();

        if (!$installed) {
            $this->setInstalledRevision($revision);
        }

        if ($revision > $installed) {
            $updatesLoader = $this->getUpdatesLoader();
            $prefix = $this->getPrefix();
            $db = $this->getDatabase();

            for ($i = $installed; $i <= $revision; $i++) {
                if (!$queries = $updatesLoader->load($i)) {
                    continue;
                }

                foreach ((array)$queries as $query) {
                    $query = str_replace('%prefix%', $prefix, $query);
                    $db->query($query);
                }
            }

            $this->setInstalledRevision($revision);
        }
    }

    /**
     * Returns current plugin revision.
     * @return int
     */
    public function getCurrentRevision()
    {
        return (int)$this->getEnvironment()->getConfig()->get('revision');
    }

    /**
     * Returns revision of the installed plugin.
     * @return int|null
     */
    public function getInstalledRevision()
    {
        return (int)get_option(self::CURRENT_REVISION_KEY, null);
    }

    /**
     * Sets revision of the installed plugin.
     * @param int $revision
     * @return SocialSharing_Updater_Module
     */
    public function setInstalledRevision($revision)
    {
        update_option(self::CURRENT_REVISION_KEY, (int)$revision);

        return $this;
    }

    public function getUpdatesLoader()
    {
        if (!$this->updatesLoader) {
            $this->updatesLoader = new SocialSharing_Updater_UpdatesLoader(
                $this->getLocation()
            );
        }

        return $this->updatesLoader;
    }

    /**
     * @return wpdb
     */
    public function getDatabase()
    {
        global $wpdb;

        return $wpdb;
    }

    public function getPrefix()
    {
        return $this->getDatabase()->prefix . $this->getEnvironment(
        )->getConfig()->get('db_prefix');
    }
}