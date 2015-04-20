<?php

/**
 * Class SocialSharing_Core_BaseController
 */
class SocialSharing_Core_BaseController extends Rsc_Mvc_Controller
{
    /**
     * @var SocialSharing_Core_ModelsFactory
     */
    protected $modelsFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Rsc_Environment $environment,
        Rsc_Http_Request $request
    ) {
        parent::__construct(
            $environment,
            $request
        );

        $this->modelsFactory = new SocialSharing_Core_ModelsFactory(
            $environment
        );
    }

    /**
     * @return SocialSharing_Core_ModelsFactory
     */
    public function getModelsFactory()
    {
        return $this->modelsFactory;
    }

    public function translate($string)
    {
        return $this->getEnvironment()->translate($string);
    }

    /**
     * @param string $message
     * @return ErrorException
     */
    public function error($message = null)
    {
        if (!$message) {
            $message = $this->translate('An error has occurred');
        }

        return new ErrorException($message);
    }

    public function ajaxSuccess(array $data = array())
    {
        return $this->response(
            Rsc_Http_Response::AJAX,
            array_merge(array('success' => true), $data)
        );
    }

    public function ajaxError($message, array $data = array())
    {
        return $this->response(
            Rsc_Http_Response::AJAX,
            array_merge(array('success' => false, 'message' => $message), $data)
        );
    }
}