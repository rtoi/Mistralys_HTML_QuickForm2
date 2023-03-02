<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Traits;

trait RuntimePropertiesTrait
{
    /**
     * Stores custom runtime properties for the element.
     *
     * @var array<string,mixed>
     * @see setRuntimeProperty()
     * @see getRuntimeProperty()
     */
    protected array $runtimeProperties = array();

    /**
     * Sets a runtime property for the node, which can be retrieved
     * again anytime. It is not used in the element in any way, but
     * can be helpful attaching related data to an element.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setRuntimeProperty(string $name, $value) : self
    {
        $this->runtimeProperties[$name] = $value;

        return $this;
    }

    /**
     * Retrieves the value of a previously set runtime property.
     * If it does not exist, returns the default value which can
     * optionally be specified as well.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getRuntimeProperty(string $name, $default = null)
    {
        return $this->runtimeProperties[$name] ?? $default;
    }
}
