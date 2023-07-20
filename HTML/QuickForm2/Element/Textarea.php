<?php
/**
 * Class for <textarea> elements
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
 * Class for <textarea> elements
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_Textarea extends HTML_QuickForm2_Element
{
    protected bool $persistent = true;

   /**
    * Value for textarea field
    * @var  string
    */
    protected $value = null;

    public function getType() : string
    {
        return 'textarea';
    }

    public function setValue($value) : self
    {
        $this->value = $value;
        return $this;
    }

    public function getRawValue()
    {
        return empty($this->attributes['disabled'])? $this->value: null;
    }

    public function __toString()
    {
        if ($this->frozen) {
            return $this->getFrozenHtml();
        }

        return
            $this->getIndent() .
            '<textarea' . $this->getAttributes(true) .
               '>' .
                preg_replace(
                    "/(\r\n|\n|\r)/",
                    '&#010;',
                    htmlspecialchars(
                        (string)$this->value, ENT_QUOTES, self::getOption('charset'))
                ) .
            '</textarea>';
    }

    public function getFrozenHtml() : string
    {
        $value = htmlspecialchars($this->value, ENT_QUOTES, self::getOption('charset'));
        if ('off' === $this->getAttribute('wrap')) {
            $html = $this->getIndent() . '<pre>' . $value .
                    '</pre>' . self::getOption('linebreak');
        } else {
            $html = nl2br($value) . self::getOption('linebreak');
        }
        return $html . $this->getPersistentContent();
    }

   /**
    * Sets the columns attribute of the textarea.
    * @param int $cols
    * @return $this
    */
    public function setColumns(int $cols) : self
    {
        return $this->setAttribute('cols', $cols);
    }

   /**
    * Sets the rows attribute of the textarea.
    * @param int $rows
    * @return $this
    */
    public function setRows(int $rows) : self
    {
        return $this->setAttribute('rows', $rows);
    }
    
   /**
    * Adds a filter for the "trim" function.
    * @return $this
    */
    public function addFilterTrim() : self
    {
        return $this->addFilter('trim');
    }
}
