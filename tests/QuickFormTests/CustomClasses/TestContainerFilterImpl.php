<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Container;

class TestContainerFilterImpl extends HTML_QuickForm2_Container
{
    public function getType() : string
    {
        return 'concrete';
    }

    public function setValue($value) : self
    {
        return $this;
    }

    public function __toString() : string
    {
        return '';
    }
}
