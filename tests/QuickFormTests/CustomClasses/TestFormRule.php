<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Rule;

class TestFormRule extends HTML_QuickForm2_Rule
{
    protected function validateOwner() : bool
    {
        return false;
    }

    protected function setOwnerError() : void
    {
        $this->getOwner()->getElementById('foo')->setError('an error message');
    }
}
