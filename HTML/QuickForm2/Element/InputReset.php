<?php
/**
 * Class for <input type="reset" /> elements
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
 * Class for <input type="reset" /> elements
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_InputReset extends HTML_QuickForm2_Element_Input
{
    protected array $attributes = array('type' => 'reset');

    public function isFreezable(): bool
    {
        return false;
    }

   /**
    * Reset elements cannot have any submit values
    *
    * @param mixed $value Element's value, this parameter is ignored
    *
    * @return $this
    */
    public function setValue($value) : self
    {
        return $this;
    }

    public function setLabel($label) : HTML_QuickForm2_Node
    {
        return $this->setAttribute('value', $label);
    }

    /**
    * Reset elements cannot have any submit values
    *
    * This method always returns null
    *
    * @return    string|null
    */
    public function getRawValue()
    {
        return null;
    }
}
