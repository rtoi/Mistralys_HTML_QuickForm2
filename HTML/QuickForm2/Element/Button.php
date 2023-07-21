<?php
/**
 * Class for <button> elements
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

use HTML\QuickForm2\Interfaces\ButtonElementInterface;

/**
 * Class for <button> elements
 *
 * Note that this element was named 'xbutton' in previous version of QuickForm,
 * the name 'button' being used for current 'inputbutton' element.
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_Button extends HTML_QuickForm2_Element implements ButtonElementInterface
{
   /**
    * Contains options and data used for the element creation
    * - content: Content to be displayed between <button></button> tags
    * @var array<string,mixed>
    */
    protected $data = array('content' => '');

   /**
    * Element's submit value
    * @var  string|null
    */
    protected $submitValue = null;

    public function getType() : string
    {
        return (string)$this->getAttribute('type');
    }

    public function isFreezable(): bool
    {
        return false;
    }

   /**
    * Sets the contents of the button element
    *
    * @param string|int|float|Stringable|NULL $content Button content (HTML to add between <button></button> tags)
    *
    * @return $this
    */
    public function setContent($content) : self
    {
        $this->data['content'] = (string)$content;
        return $this;
    }
    
   /**
    * Sets the button's type attribute.
    * @param string $type
    * @return $this
    */
    public function setType(string $type) : self
    {
        $this->setAttribute('type', $type);
        return $this;
    }
    
   /**
    * Sets the button label. This is an alias for the {@link setContent()} method.
    * @param string|int|float|Stringable|NULL $label Can contain HTML.
    * @return $this
    */
    public function setLabel($label) : self
    {
        return $this->setContent($label);
    }

   /**
    * Button's value cannot be set via this method
    *
    * @param mixed $value Element's value, this parameter is ignored
    * @return $this
    */
    public function setValue($value) : self
    {
        return $this;
    }

   /**
    * Returns the element's value
    *
    * The value is only returned if the following is true
    *  - button has 'type' attribute set to 'submit' (or no 'type' attribute)
    *  - the form was submitted by clicking on this button
    *
    * This method returns the actual value submitted by the browser. Note that
    * different browsers submit different values!
    *
    * @return string|null
    */
    public function getRawValue()
    {
        if (
            (empty($this->attributes['type']) || 'submit' === $this->attributes['type'])
            &&
            !$this->getAttribute('disabled')
        ) {
            return $this->submitValue;
        }

        return null;
    }

    /**
     * Sets the <code>type</code> attribute to <code>submit</code.
     * @return $this
     */
    public function makeSubmit() : self
    {
        return $this->setType('submit');
    }

    /**
     * Sets the <code>type</code> attribute to <code>button</code.
     * @return $this
     */
    public function makeButton() : self
    {
        return $this->setType('button');
    }

    public function isSubmit() : bool
    {
        return $this->getType() === 'submit';
    }

    public function __toString()
    {
        return $this->getIndent() . '<button' . $this->getAttributes(true) .
               '>' . $this->data['content'] . '</button>';
    }

    protected function updateValue() : void
    {
        foreach ($this->getDataSources() as $ds) {
            if ($ds instanceof HTML_QuickForm2_DataSource_Submit
                && null !== ($value = $ds->getValue($this->getName()))
            ) {
                $this->submitValue = $value;
                return;
            }
        }
        $this->submitValue = null;
    }
}
