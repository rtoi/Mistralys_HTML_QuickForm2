<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Node;
use HTML_QuickForm2_Rule;

/**
 * Class used to test creation of rules via HTML_QuickForm2_Factory::createRule()
 */
class FakeRule extends HTML_QuickForm2_Rule
{
    public ?HTML_QuickForm2_Node $owner = null;

    protected function validateOwner() : bool
    {
        return true;
    }
}
