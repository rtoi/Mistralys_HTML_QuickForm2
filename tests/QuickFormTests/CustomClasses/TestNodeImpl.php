<?php

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Node;
use HTML_QuickForm2_Renderer;

/**
 * A non-abstract subclass of Node
 *
 * We can't instantiate the class directly and thus need to "implement" its
 * abstract methods. And also make validate() public to be able to test.
 */
class TestNodeImpl extends HTML_QuickForm2_Node
{
    public function getType() : string
    {
        return 'concrete';
    }

    public function getRawValue()
    {
        return '';
    }

    public function setValue($value) : self
    {
        return $this;
    }

    public function __toString()
    {
        return '';
    }

    public function getName() : string
    {
        return '';
    }

    public function setName(?string $name) : self
    {
        return $this;
    }

    protected function updateValue() : void
    {
    }

    public function validate() : bool
    {
        return parent::validate();
    }

    public function getJavascriptValue($inContainer = false)
    {
        return '';
    }

    public function getJavascriptTriggers()
    {
        return array();
    }

    public function render(HTML_QuickForm2_Renderer $renderer) : HTML_QuickForm2_Renderer
    {
        return $renderer;
    }
}
