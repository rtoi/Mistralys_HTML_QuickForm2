<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Element\Select;

use ArrayAccess;
use BaseHTMLElement;
use Stringable;

/**
 * @implements ArrayAccess<string,string|array<string,string>>
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

    /**
     * @param string $name
     * @return string|null The attribute value, or NULL if it does not exist.
     */
    public function getAttribute(string $name) : ?string
    {
        return $this->data['attr'][$name] ?? null;
    }

    /**
     * Renders the option's <code>&lt;option&gt;</code> tag.
     *
     * @param bool $selected Whether to add the `selected` property.
     * @return string
     */
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

    /**
     * @param string $offset
     * @return string|array<string,string>
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param string $offset
     * @param string|array<string,string> $value
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        $this->data[(string)$offset] = $value;
    }

    /**
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset) : void
    {
        unset($this->data[$offset]);
    }

    // endregion
}
