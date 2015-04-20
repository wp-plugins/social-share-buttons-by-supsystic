<?php


class SocialSharing_Shares_Controller extends SocialSharing_Core_BaseController
{
    /**
     * Saves share to the database.
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function saveAction(Rsc_Http_Request $request)
    {
        $projectId = $request->post->get('project_id');
        $networkId = $request->post->get('network_id');
        $postId = $request->post->get('post_id');

        /** @var SocialSharing_Shares_Model_Shares $shares */
        $shares = $this->modelsFactory->get('shares');

        try {
            $shares->add($projectId, $networkId, $postId);
        } catch (Exception $e) {
            return $this->ajaxError(
                $this->translate(
                    sprintf(
                        'Failed to add current share to the statistic: %s',
                        $e->getMessage()
                    )
                )
            );
        }

        return $this->ajaxSuccess();
    }

    public function statisticAction(Rsc_Http_Request $request)
    {
        $project = $this->modelsFactory->get('projects')->get(
            $request->query->get('project_id')
        );

        return $this->response('@shares/statistic.twig', array(
            'project' => $project
        ));
    }

    public function getTotalSharesAction(Rsc_Http_Request $request)
    {
        try {
            /** @var SocialSharing_Shares_Model_Shares $shares */
            $shares = $this->modelsFactory->get('shares');
            $stats = $shares->getProjectStats($request->post->get('project_id'));
        } catch (Exception $e) {
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess(array('stats' => $stats));
    }

    public function getTotalSharesByDaysAction(Rsc_Http_Request $request)
    {
        try {
            $days = $request->post->get('days', 30);
            $to = new DateTime();
            $from = new DateTime();

            if ($days < 1) {
                $days = 1;
            }

            $modifier = '-'.$days . ' days';
            $from->modify($modifier);

            /** @var SocialSharing_Shares_Model_Shares $shares */
            $shares = $this->modelsFactory->get('shares');
            $stats = $shares->getProjectStatsForPeriod(
                $request->post->get('project_id'),
                $from,
                $to
            );
        } catch (Exception $e) {
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess(array('stats' => $stats));
    }

    public function getPopularPagesByDaysAction(Rsc_Http_Request $request)
    {
        try {
            $days = $request->post->get('days', 30);
            $to = new DateTime();
            $from = new DateTime();

            if ($days < 1) {
                $days = 1;
            }

            $modifier = '-'.$days . ' days';
            $from->modify($modifier);

            /** @var SocialSharing_Shares_Model_Shares $shares */
            $shares = $this->modelsFactory->get('shares');
            $stats = $shares->getPopularPostsForPeriod(
                $request->post->get('project_id'),
                $from,
                $to
            );
        } catch (Exception $e) {
            return $this->ajaxError($e->getMessage());
        }

        if (is_array($stats) && count($stats) > 0) {
            foreach ($stats as $index => $row) {
                $post = get_post($row->post_id);
                $stats[$index]->post = $post;
            }
        }

        return $this->ajaxSuccess(array('stats' => $stats));
    }
}