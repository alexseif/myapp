<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;

/**
 * A TWIG Extension which allows to show Controller and Action name in a TWIG view.
 * 
 * The Controller/Action name will be shown in lowercase. For example: 'default' or 'index'
 *
 * @author alexseif
 */
class ControllerActionExtension extends \Twig_Extension
{

  /**
   * @var Request 
   */
  protected $request;

  /**
   * @var \Twig_Environment
   */
  protected $environment;

  public function setRequest(Request $request = null)
  {
    $this->request = $request;
  }


  public function getFunctions()
  {
    return array(
//      'get_controller_name' => new \Twig_Function_Method($this, 'getControllerName'),
      new \Twig_SimpleFunction('get_controller_name', array($this, 'getControllerName')),
//      'get_action_name' => new \Twig_Function_Method($this, 'getActionName'),
      new \Twig_SimpleFunction('get_action_name', array($this, 'getActionName')),
    );
  }

  /**
   * Get current controller name
   */
  public function getControllerName()
  {
    if (null !== $this->request) {
      $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
      $matches = array();
      $result = preg_match($pattern, $this->request->get('_controller'), $matches);
      if ($result)
        return strtolower($matches[1]);
      return null;
    }
  }

  /**
   * Get current action name
   */
  public function getActionName()
  {
    if (null !== $this->request) {
      $pattern = "#::([a-zA-Z]*)Action#";
      $matches = array();
      $result = preg_match($pattern, $this->request->get('_controller'), $matches);
      if ($result)
        return $matches[1];
      return null;
    }
  }

}
