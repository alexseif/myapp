<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Model;

/**
 * Description of ActionItem.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ActionItem
{

    protected $id;

    protected $type;

    protected $title;

    protected $label;

    protected $duration;

    protected $priority;

    protected $urgency;

    public function __construct(
      $id,
      $type,
      $title,
      $duration,
      $label = null,
      $priority = null,
      $urgency = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->label = $label;
        $this->duration = $duration;
        $this->priority = $priority;
        $this->urgency = $urgency;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getUrgency()
    {
        return $this->urgency;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setLabel($label): void
    {
        $this->label = $label;
    }

    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    public function setPriority($priority): void
    {
        $this->priority = $priority;
    }

    public function setUrgency($urgency): void
    {
        $this->urgency = $urgency;
    }

}
