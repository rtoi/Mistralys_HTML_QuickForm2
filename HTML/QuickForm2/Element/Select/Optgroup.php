<?php
/**
 * Class representing an <optgroup> tag
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

use HTML\QuickForm2\Traits\RuntimePropertiesInterface;
use HTML\QuickForm2\Traits\RuntimePropertiesTrait;

/**
 * Class representing an <optgroup> tag
 *
 * Do not instantiate this class yourself, use
 * {@link HTML_QuickForm2_Element_Select::addOptgroup()} method
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 * @internal
 */
class HTML_QuickForm2_Element_Select_Optgroup
    extends HTML_QuickForm2_Element_Select_OptionContainer
    implements RuntimePropertiesInterface
{
    use RuntimePropertiesTrait;

   /**
    * Class constructor
    *
    * @param array        &$values         Reference to values of parent <select> element
    * @param array        &$possibleValues Reference to possible values of parent <select> element
    * @param string       $label           'label' attribute for optgroup tag
    * @param string|array $attributes      Additional attributes for <optgroup> tag
    *                                      (either as a string or as an associative array)
    */
    public function __construct(&$values, &$possibleValues, $label, $attributes = null)
    {
        parent::__construct($values, $possibleValues);
        $this->setAttributes($attributes);
        $this->attributes['label'] = (string)$label;
    }
    
   /**
    * Retrieves the optgroup's label.
    * @return string
    */
    public function getLabel() : string
    {
        return $this->attributes['label'];
    }

    public function __toString() : string
    {
        $indent    = $this->getIndent();
        $linebreak = self::getOption('linebreak');
        return $indent . '<optgroup' . $this->getAttributes(true) . '>' .
               $linebreak . parent::__toString() . $indent . '</optgroup>' . $linebreak;
    }
}
