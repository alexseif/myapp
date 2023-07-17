<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig_Environment;

/**
 * A TWIG Extension which allows to show Controller and Action name in a TWIG view.
 *
 * The Controller/Action name will be shown in lowercase. For example: 'default' or 'index'
 *
 * @author alexseif
 */
class ControllerActionExtension extends AbstractExtension
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Twig_Environment
     */
    protected $environment;

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_controller_name', [$this, 'getControllerName']),
            new TwigFunction('get_action_name', [$this, 'getActionName']),
        ];
    }

    /**
     * Get current controller name.
     */
    public function getControllerName()
    {
        if (null !== $this->request) {
            $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
            $matches = [];
            $result = preg_match($pattern, $this->request->get('_controller'), $matches);
            if ($result) {
                return strtolower($matches[1]);
            }
        }
        return null;
    }

    /**
     * Get current action name.
     */
    public function getActionName()
    {
        if (null !== $this->request) {
            $pattern = '#::([a-zA-Z]*)Action#';
            $matches = [];
            $result = preg_match($pattern, $this->request->get('_controller'), $matches);
            if ($result) {
                return $matches[1];
            }
        }
        return null;
    }
}
