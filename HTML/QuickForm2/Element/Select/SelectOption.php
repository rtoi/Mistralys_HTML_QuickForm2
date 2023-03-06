<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Element\Select;

use ArrayAccess;
use BaseHTMLElement;
use Stringable;

/**
 * @implements ArrayAccess<string,mixed>
 */
class SelectOption implements ArrayAccess, Stringable
{
    /**
     * @var array<string,mixed>
     */
    private array $data = array(
        'text' => '',
        'attr' => array(
            'value' => ''
        )
    );

    public function __construct(string $text, array $attributes=array())
    {
        $this->setLabel($text);
        $this->setAttributes($attributes);
    }

    /**
     * @param string|int|float|Stringable|NULL $label
     * @return $this
     */
    public function setLabel($label) : self
    {
        $this->data['text'] = (string)$label;
        return $this;
    }

    public function getLabel() : string
    {
        return $this->data['text'];
    }

    public function getValue() : string
    {
        return $this->data['attr']['value'];
    }

    public function setAttributes(array $attributes) : self
    {
        foreach($attributes as $name => $value)
        {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    public function getAttributes() : array
    {
        return $this->data['attr'];
    }

    /**
     * @param string $name
     * @param string|int|float|Stringable|NULL $value
     * @return $this
     */
    public function setAttribute(string $name, $value) : self
    {
        $this->data['attr'][$name] = (string)$value;
        return $this;
    }

    public function render(bool $selected=false) : string
    {
        $attr = $this->getAttributes();

        if ($selected) {
            $attr['selected'] = 'selected';
        }

        return
            '<option' .  BaseHTMLElement::getAttributesString($attr) .'>' .
                $this->getLabel().
            '</option>';
    }

    public function __toString() : string
    {
        return $this->render();
    }

    // region: Array access

    public function offsetExists($offset) : bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value) : void
    {
        $this->data[(string)$offset] = $value;
    }

    public function offsetUnset($offset) : void
    {
        unset($this->data[$offset]);
    }

    // endregion
}
