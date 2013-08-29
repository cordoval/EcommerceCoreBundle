<?php

namespace Ecommerce\Bundle\CoreBundle\Model;

class FormGroup
{
    protected $name;
    protected $label;
    protected $priority;


    function __construct($name, $label, $priority = 8)
    {
        $this->name     = strtolower($name);
        $this->label    = (string)$label;
        $this->priority = (int)$priority;
    }


    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
