<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Traits;

interface RuntimePropertiesInterface
{
    /**
     * @param string $name
     * @param mixed|NULL $value
     * @return $this
     */
    public function setRuntimeProperty(string $name, $value) : self;

    /**
     * @param string $name
     * @param mixed|NULL $default
     * @return mixed|NULL
     */
    public function getRuntimeProperty(string $name, $default = null);
}
