<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

/**
 * Description of DayManager
 *
 * @author Alex Seif <me@alexseif.com>
 */
class DayManager
{

  public function isWorkDay()
  {
    $today = new \DateTime();
    if (in_array($today->format('N'), [5, 6])) { // 5 and 6 are weekend days
      return false;
    }
    return true;
  }

  public function isWorkingDay()
  {
    if ($this->isWorkDay()) {
      return 'Yes, it\'s a working day.';
    } else {
      return 'No, it\'s not a working day.';
    }
  }

}
