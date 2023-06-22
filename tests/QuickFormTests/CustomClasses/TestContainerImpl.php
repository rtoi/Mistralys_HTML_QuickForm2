<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Container;

/**
 * A non-abstract subclass of Container
 *
 * Container class is still abstract, we should "implement" the remaining methods
 * and also make validate() public to be able to test it.
 */
class TestContainerImpl extends HTML_QuickForm2_Container
{
    public function getType()
    {
        return 'concrete';
    }

    public function setValue($value)
    {
        return '';
    }

    public function __toString()
    {
        return '';
    }

    public function validate()
    {
        return parent::validate();
    }
}
