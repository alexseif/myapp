<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

use AppBundle\Logic\Node;

/**
 * Description of NodeManager
 *
 * @author Alex Seif <me@alexseif.com>
 */
class NodeManager
{

  protected $routine;
  protected $nodes;

  public function addNode($name, $duration, $strict, $busy, $start)
  {
    $this->nodes[] = new Node($name, $duration, $strict, $busy, $start);
  }

}
