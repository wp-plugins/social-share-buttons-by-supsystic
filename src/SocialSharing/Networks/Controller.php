<?php

/**
 * Class SocialSharing_Networks_Controller
 */
class SocialSharing_Networks_Controller extends SocialSharing_Core_BaseController
{
    /**
     * Returns list of the all networks.
     * @return Rsc_Http_Response
     */
    public function allAction()
    {
        $networks = $this->modelsFactory->get('networks')->all();

        return $this->response(
            Rsc_Http_Response::AJAX,
            $networks
        );
    }

    /**
     * Adds networks to the project
     * @param Rsc_Http_Request $request
     */
    public function addToProjectAction(Rsc_Http_Request $request)
    {
        $networks = $request->post->get('networks');
        $projectId = $request->post->get('project_id');

        $this->modelsFactory->get('ProjectNetworks', 'Networks')
            ->drop($projectId);

        foreach ((array)$networks as $networkId) {
            $this->modelsFactory->get('ProjectNetworks', 'Networks')
                ->add($projectId, $networkId);
        }
    }

    /**
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function incrementAction(Rsc_Http_Request $request)
    {
        $id = $request->post->get('id');
        $this->modelsFactory->get('networks')->incrementTotalShares($id);

        return $this->response(Rsc_Http_Response::AJAX, array('ty' => 'np'));
    }

    public function saveTooltipsAction(Rsc_Http_Request $request) {
        $projectId = $request->post->get('project_id');
        $data = $request->post->get('data');

        if(!$savedData = get_option('networks_tooltips_' . $projectId)) {
            update_option('networks_tooltips_' . $projectId, array($data['id'] => $data['value']));
        } else {
            $savedData[$data['id']] = $data['value'];
            update_option('networks_tooltips_' . $projectId, $savedData);
        }

        return $this->ajaxSuccess();
    }

    public function saveTitlesAction(Rsc_Http_Request $request) {
        $projectId = $request->post->get('project_id');
        $data = $request->post->get('data');

        if(!$savedData = get_option('networks_titles_' . $projectId)) {
            update_option('networks_titles_' . $projectId, array($data['id'] => $data['value']));
        } else {
            $savedData[$data['id']] = $data['value'];
            update_option('networks_titles_' . $projectId, $savedData);
        }

        return $this->ajaxSuccess();
    }

    public function saveNamesAction(Rsc_Http_Request $request) {
        $projectId = $request->post->get('project_id');
        $data = $request->post->get('data');

        if(!$savedData = get_option('networks_names_' . $projectId)) {
            update_option('networks_names_' . $projectId, array($data['id'] => $data['value']));
        } else {
            $savedData[$data['id']] = $data['value'];
            update_option('networks_names_' . $projectId, $savedData);
        }

        return $this->ajaxSuccess();
    }

    public function updateSortingAction(Rsc_Http_Request $request)
    {
        $projectId = $request->post->get('project_id');
        $positions = $request->post->get('positions');
        /** @var SocialSharing_Networks_Model_ProjectNetworks $projectNetworks */
        $projectNetworks = $this->modelsFactory->get('projectNetworks', 'networks');

        if (!is_array($positions) || count($positions) === 0) {
            // Returns here success to prevent errors on frontend.
            // Its not error, just empty array
            return $this->ajaxSuccess(array(
                'notice' => 'empty'
            ));
        }

        foreach ($positions as $data) {
            try {
                $projectNetworks->updateNetworkPosition(
                    $projectId,
                    $data['network'],
                    $data['position']
                );
            } catch (Exception $e) {
                return $this->ajaxError($e->getMessage());
            }
        }

        return $this->ajaxSuccess();
    }
}