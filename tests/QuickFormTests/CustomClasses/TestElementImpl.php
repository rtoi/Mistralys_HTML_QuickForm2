<?php

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Element;

/**
 * A non-abstract subclass of Element
 *
 * Element class is still abstract, we should "implement" the remaining methods.
 * Note the default implementation of setValue() / getValue(), needed to test
 * setting the value from Data Source
 */
class TestElementImpl extends HTML_QuickForm2_Element
{
    protected $value;

    public function getType() : string
    {
        return 'concrete';
    }

    public function __toString()
    {
        return '';
    }

    public function getRawValue()
    {
        return $this->value;
    }

    public function setValue($value) : self
    {
        $this->value = $value;
        return $this;
    }
}
