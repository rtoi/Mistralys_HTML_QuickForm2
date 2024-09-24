<?php
/**
 * Base class for <input> elements
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to BSD 3-Clause License that is bundled
 * with this package in the file LICENSE and available at the URL
 * https://raw.githubusercontent.com/pear/HTML_QuickForm2/trunk/docs/LICENSE
 *
 * @category  HTML
 * @package   HTML_QuickForm2
 * @author    Alexey Borzov <avb@php.net>
 * @author    Bertrand Mansion <golgote@mamasam.com>
 * @copyright 2006-2020 Alexey Borzov <avb@php.net>, Bertrand Mansion <golgote@mamasam.com>
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      https://pear.php.net/package/HTML_QuickForm2
 */

/**
 * Base class for <input> elements
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_Input extends HTML_QuickForm2_Element
{
   /**
    * 'type' attribute should not be changeable
    * @var string[]
    */
    protected array $watchedAttributes = array('id', 'name', 'type');

    protected function onAttributeChange(string $name, $value = null) : void
    {
        if ('type' === $name) {
            throw HTML_QuickForm2::exceptionAttributeReadonly($name);
        }

        parent::onAttributeChange($name, $value);
    }

    public function getType() : string
    {
        return $this->attributes['type'];
    }

    public function setValue($value) : self
    {
        return $this->setAttribute('value', (string)$value);
    }

    public function getRawValue()
    {
        return $this->getAttribute('disabled')? null: $this->getAttribute('value');
    }

    public function __toString()
    {
        if ($this->frozen) {
            return $this->getFrozenHtml();
        } else {
            return '<input' . $this->getAttributes(true) . '>';
        }
    }

   /**
    * Returns the field's value without HTML tags
    * @return string
    */
    protected function getFrozenHtml()
    {
        $value = $this->getAttribute('value');
        return (('' != $value)? htmlspecialchars($value, ENT_QUOTES, self::getOption('charset')): '&nbsp;') .
               $this->getPersistentContent();
    }
}
?>
