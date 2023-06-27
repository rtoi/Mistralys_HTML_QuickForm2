<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Element;

/**
 * A non-abstract subclass of Element
 *
 * Element class is still abstract, we should "implement" the remaining methods.
 * We need working setValue() / getValue() to test getValue() of Container
 */
class TestElementImpl2 extends HTML_QuickForm2_Element
{
    /**
     * @var mixed
     */
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
