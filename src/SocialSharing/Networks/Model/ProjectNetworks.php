<?php

/**
 * Class SocialSharing_Networks_Model_ProjectNetworks
 *
 * Model for many-to-many relations for the projects and the networks.
 */
class SocialSharing_Networks_Model_ProjectNetworks extends SocialSharing_Core_BaseModel
{

    /**
     * Removes all networks from the specified project
     * @param int $projectId
     * @return bool
     */
    public function drop($projectId)
    {
        $query = $this->getQueryBuilder()
            ->deleteFrom($this->getTable())
            ->where('project_id', '=', (int)$projectId);

        return $this->db->query($query->build()) ? true : false;
    }

    /**
     * Adds new network to the specified project
     * @param int $projectId
     * @param int $networkId
     * @return bool
     * @throws Exception
     */
    public function add($projectId, $networkId)
    {
        $query = $this->getQueryBuilder()
            ->insertInto($this->getTable())
            ->fields(array('project_id', 'network_id'))
            ->values(array((int)$projectId, (int)$networkId));

        return $this->db->query($query->build()) ? true : false;
    }

    public function cloneNetworks($id, $prototype)
    {
        if (count($prototype->networks) === 0) {
            return;
        }

        foreach ($prototype->networks as $network) {
            $this->add($id, $network->id);
        }
    }


    /**
     * @param int $projectId
     * @param int $networkId
     * @param int $position
     */
    public function updateNetworkPosition($projectId, $networkId, $position)
    {
        $query = $this->getQueryBuilder()
            ->update($this->getTable())
            ->where('project_id', '=', (int)$projectId)
            ->andWhere('network_id', '=', (int)$networkId)
            ->set('position', (int)$position);

        $this->db->query($query->build());
        if ($this->db->last_error) {
            throw new RuntimeException($this->db->last_error);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($tableName = null)
    {
        if (null === $tableName) {
            $tableName = 'project_networks';
        }

        return parent::getTable($tableName);
    }
}