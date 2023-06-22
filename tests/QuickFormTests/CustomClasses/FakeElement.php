<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

/**
 * Class used to test creation of elements via HTML_QuickForm2_Factory::createElement()
 */
class FakeElement
{
    public ?string $name;
    public ?array $data;
    public ?string $attributes;

    public function __construct(?string $name = null, ?string $attributes = null, ?array $data = null)
    {
        $this->name = $name;
        $this->data = $data;
        $this->attributes = $attributes;
    }
}
