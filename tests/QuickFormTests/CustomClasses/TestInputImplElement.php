<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Element_Input;

/**
 * We need to set the element's type
 *
 * @see \QuickFormTests\Element\InputTest
 */
class TestInputImplElement extends HTML_QuickForm2_Element_Input
{
    public function __construct($name = null, $attributes = null, array $data = array())
    {
        parent::__construct($name, $attributes, $data);
        $this->attributes['type'] = 'concrete';
    }
}
