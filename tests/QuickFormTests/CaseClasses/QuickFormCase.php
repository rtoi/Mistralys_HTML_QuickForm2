<?php

declare(strict_types=1);

namespace QuickFormTests\CaseClasses;

use HTML_QuickForm2_Rule_Required;
use PHPUnit\Framework\TestCase;

abstract class QuickFormCase extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        // Ensure the default message is empty
        HTML_QuickForm2_Rule_Required::setDefaultMessage('');
    }
}
