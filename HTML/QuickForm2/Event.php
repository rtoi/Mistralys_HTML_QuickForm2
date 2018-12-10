<?php

abstract class HTML_QuickForm2_Event
{
    protected $args;
    
    public function __construct(array $args=array())
    {
        $this->args = $args;
    }
}
