<?php
/**
 * @category  HTML
 * @package HTML_QuickForm2
 * @subpackage Elements
 * @see HTML_QuickForm2_Element_InputButton
 */

use HTML\QuickForm2\Interfaces\ButtonElementInterface;

/**
 * Class for <input type="button" /> elements
 *
 * @category HTML
 * @package HTML_QuickForm2
 * @subpackage Elements
 * @author Alexey Borzov <avb@php.net>
 * @author Bertrand Mansion <golgote@mamasam.com>
 */
class HTML_QuickForm2_Element_InputButton extends HTML_QuickForm2_Element_Input implements ButtonElementInterface
{
    protected array $attributes = array('type' => 'button');

    public function isFreezable(): bool
    {
        return false;
    }

   /**
    * Button elements cannot have any submit values
    *
    * @param mixed $value Element's value, this parameter is ignored
    *
    * @return $this
    */
    public function setValue($value) : self
    {
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label) : self
    {
        return $this->setAttribute('value', (string)$label);
    }

    /**
    * Button elements cannot have any submit values
    *
    * This method always returns null
    *
    * @return    string|null
    */
    public function getRawValue()
    {
        return null;
    }

    public function isSubmit() : bool
    {
        return true;
    }
}
