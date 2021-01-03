<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Model;

/**
 * Description of Item
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Type
{

  /**
   *
   * @var string
   */
  protected $name;

  function __construct($name)
  {
    $this->name = $name;
  }

  function getName()
  {
    return $this->name;
  }

  function setName($name)
  {
    $this->name = $name;
  }

  function getTypeCount()
  {
    return 0;
  }

}
