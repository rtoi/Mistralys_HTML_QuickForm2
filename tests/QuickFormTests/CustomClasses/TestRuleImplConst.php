<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Rule;

/**
 * The non-abstract subclass of Rule
 */
class TestRuleImplConst extends HTML_QuickForm2_Rule
{
    protected function validateOwner()
    {
        // It just returns whatever value was passed to setConfig()
        return $this->config;
    }
}
