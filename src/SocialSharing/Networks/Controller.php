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
     */
    public function incrementAction(Rsc_Http_Request $request)
    {
        $id = $request->post->get('id');
        $this->modelsFactory->get('networks')->incrementTotalShares($id);

        return $this->response(Rsc_Http_Response::AJAX, array('ty' => 'np'));
    }
}