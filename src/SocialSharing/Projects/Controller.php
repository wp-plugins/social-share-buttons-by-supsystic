<?php

/**
 * Class SocialSharing_Projects_Controller
 *
 * Projects controller.
 */
class SocialSharing_Projects_Controller extends SocialSharing_Core_BaseController
{

    /**
     * Shows list of the created projects.
     *
     * @param Rsc_Http_Request $request Http request
     * @return Rsc_Http_Response
     */
    public function indexAction(Rsc_Http_Request $request)
    {
        return $this->response('@projects/index.twig', array(
            'projects' => $this->modelsFactory->get('projects')->all()
        ));
    }

    /**
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function addAction(Rsc_Http_Request $request)
    {
        try {
            $insertId = $this->modelsFactory->get('projects')->create(
                $request->post->get('title', 'Untitled')
            );
        } catch (RuntimeException $e) {
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess(array(
            'redirect_url' => $this->generateUrl(
                'projects',
                'view',
                array(
                    'id' => $insertId
                )
            )
        ));
    }

    /**
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function saveAction(Rsc_Http_Request $request)
    {
        $id = $request->post->get('id');
        $settings = $request->post->get('settings');
        $projects = $this->modelsFactory->get('projects');

        $projects->save($id, $settings);

        return $this->ajaxSuccess(array('popup_id' => $settings['popup_id']));
    }

    /**
     * View specific project.
     *
     * @param Rsc_Http_Request $request Http request
     * @return Rsc_Http_Response Http response
     */
    public function viewAction(Rsc_Http_Request $request)
    {
        $projectId = (int)$request->query->get('id');

        $project = $this->modelsFactory->get('projects')->get($projectId);
        $networks = $this->modelsFactory->get('networks')->all();

        return $this->response(
            '@projects/view.twig',
            array(
                'project'         => $project,
                'networks'        => $networks,
                'posts'           => get_posts(array('posts_per_page' => -1)),
                'pages'           => get_pages(array('posts_per_page' => -1)),
                'popup_installed' => $this->getEnvironment()->getModule(
                    'popup'
                )->isInstalled(),
            )
        );
    }

    /**
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function deleteAction(Rsc_Http_Request $request)
    {
        $this->modelsFactory->get('projects')->delete($request->query->get('id'));

        return $this->redirect($this->generateUrl('projects', 'index'));
    }

    public function renameAction(Rsc_Http_Request $request)
    {
        try {
            $projects = $this->modelsFactory->get('projects');

            $projects->rename(
                $request->post->get('id'),
                $request->post->get('title')
            );
        } catch (Exception $e) {
            return $this->ajaxError($this->translate(sprintf('Failed to rename project: %s', $e->getMessage())));
        }

        return $this->ajaxSuccess();
    }

    public function cloneAction(Rsc_Http_Request $request)
    {
        $id = $request->post->get('id', $request->query->get('id'));

        try {
            $cloneId = $this->modelsFactory
                ->get('projects')
                ->makeClone($id);

            $prototype = $this->modelsFactory->get('projects')->get($id);
            $this->modelsFactory->get('projectNetworks', 'Networks')
                ->cloneNetworks($cloneId, $prototype);

            $redirectUri = $this->generateUrl(
                'projects',
                'view',
                array('id' => $cloneId)
            );

            if ($request->isXmlHttpRequest()) {
                return $this->response(
                    Rsc_Http_Response::AJAX,
                    array(
                        'location' => $redirectUri
                    )
                );
            }

            return $this->redirect($redirectUri);
        } catch (InvalidArgumentException $e) {
            throw $this->error(
                sprintf(
                    $this->translate('Unable to clone project: %s'),
                    $e->getMessage()
                )
            );
        } catch (RuntimeException $e) {
            throw $this->error(
                sprintf(
                    $this->translate(
                        'Unable to clone project due database error: %s'
                    ),
                    $e->getMessage()
                )
            );
        }
    }
}