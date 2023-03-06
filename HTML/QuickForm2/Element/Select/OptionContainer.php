<?php
/**
 * Collection of <option>s and <optgroup>s
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

declare(strict_types=1);

use HTML\QuickForm2\Element\Select\SelectOption;

/**
 * Collection of <option>s and <optgroup>s
 *
 * This class handles the output of <option> tags. The class is not intended to
 * be used directly.
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
class HTML_QuickForm2_Element_Select_OptionContainer extends BaseHTMLElement
    implements IteratorAggregate, Countable
{
    public const ERROR_INVALID_OPTGROUP_CLASS = 131001;
    public const ERROR_INVALID_OPTION_CLASS = 131002;

    /**
    * List of options and optgroups in this container
    *
    * Options are stored as arrays (for performance reasons), optgroups as
    * instances of Optgroup class.
    *
    * @var array
    */
    protected array $options = array();

   /**
    * Reference to parent <select>'s values
    * @var array
    */
    protected array $values;

   /**
    * Reference to parent <select>'s possible values
    * @var array
    */
    protected array $possibleValues;

    /**
     * @var class-string
     */
    protected string $optGroupClass = HTML_QuickForm2_Element_Select_Optgroup::class;

    /**
     * @var class-string
     */
    protected string $optionClass = SelectOption::class;

   /**
    * Class constructor
    *
    * @param array &$values         Reference to values of parent <select> element
    * @param array &$possibleValues Reference to possible values of parent <select> element
    */
    public function __construct(array &$values, array &$possibleValues)
    {
        $this->values         =& $values;
        $this->possibleValues =& $possibleValues;

        parent::__construct();
    }

    /**
     * Sets a custom class to use for any option groups added
     * to the element. Must extend {@see HTML_QuickForm2_Element_Select_Optgroup}.
     *
     * @param class-string $class
     * @return $this
     */
    public function setOptGroupClass(string $class) : self
    {
        $this->optGroupClass = $class;
        return $this;
    }

    /**
     * Sets a custom class to use for any options added to the
     * element. Must extend {@see SelectOption}.
     *
     * @param class-string $class
     * @return $this
     */
    public function setOptionClass(string $class) : self
    {
        $this->optionClass = $class;
        return $this;
    }

    /**
     * Adds a new option
     *
     * Please note that if you pass 'selected' attribute in the $attributes
     * parameter then this option's value will be added to <select>'s values.
     *
     * @param string|int|float|Stringable $text Option text
     * @param string|int|float|Stringable|NULL $value 'value' attribute for <option> tag
     * @param string|array<string,string>|NULL $attributes Additional attributes for <option> tag
     *                     (either as a string or as an associative array)
     * @return SelectOption
     * @throws HTML_QuickForm2_InvalidArgumentException {@see self::ERROR_INVALID_OPTION_CLASS}
     */
    public function addOption($text, $value, $attributes = null) : SelectOption
    {
        $text = (string)$text;
        $value = (string)$value;

        if (null === $attributes)
        {
            $attributes = array('value' => $value);
        }
        else
        {
            $attributes = self::prepareAttributes($attributes);

            if (isset($attributes['selected']))
            {
                // the 'selected' attribute will be set in __toString()
                unset($attributes['selected']);

                if (!in_array($value, $this->values, true)) {
                    $this->values[] = $value;
                }
            }

            $attributes['value'] = $value;
        }

        if (!isset($attributes['disabled'])) {
            $this->possibleValues[$value] = true;
        }

        $class = $this->optionClass;
        $option = new $class($text, $attributes);

        if(!$option instanceof SelectOption)
        {
            throw new HTML_QuickForm2_InvalidArgumentException(
                'Invalid custom select option class.',
                self::ERROR_INVALID_OPTION_CLASS
            );
        }

        $this->options[] = $option;

        return $option;
    }

    /**
     * Like addOption, but prepends the option to the beginning of the stack.
     *
     * @param string|int|float $text
     * @param string|int|float|NULL $value
     * @param array<string,string>|string|NULL $attributes
     * @return SelectOption
     * @throws HTML_QuickForm2_InvalidArgumentException {@see self::ERROR_INVALID_OPTION_CLASS}
     */
    public function prependOption($text, $value, $attributes = null) : SelectOption
    {
        // let the original method do its thing
        $option = $this->addOption($text, $value, $attributes);
        
        // and now remove it from the end and prepend it to the collection
        $last = array_pop($this->options);
        array_unshift($this->options, $last);

        return $option;
    }

    /**
     * Adds a new optgroup. The optgroup class can be customized
     * by setting a custom class via {@see self::setOptGroupClass()}.
     *
     * @param string $label 'label' attribute for optgroup tag
     * @param string|array|NULL $attributes Additional attributes for <optgroup> tag
     *                     (either as a string or as an associative array)
     *
     * @return HTML_QuickForm2_Element_Select_Optgroup
     * @throws HTML_QuickForm2_InvalidArgumentException {@see self::ERROR_INVALID_OPTGROUP_CLASS}
     */
    public function addOptgroup(string $label, $attributes = null) : HTML_QuickForm2_Element_Select_Optgroup
    {
        $class = $this->optGroupClass;

        $optgroup = new $class(
            $this->values, $this->possibleValues, $label, $attributes
        );

        if($optgroup instanceof HTML_QuickForm2_Element_Select_Optgroup)
        {
            $this->options[] = $optgroup;
            return $optgroup;
        }

        throw new HTML_QuickForm2_InvalidArgumentException(
            'Invalid option group class',
            self::ERROR_INVALID_OPTGROUP_CLASS
        );
    }

   /**
    * Returns an array of contained options
    *
    * @return array<int,SelectOption|HTML_QuickForm2_Element_Select_Optgroup>
    */
    public function getOptions() : array
    {
        return $this->options;
    }

    public function __toString() : string
    {
        $indentLvl = $this->getIndentLevel();
        $indent    = $this->getIndent() . self::getOption('indent');
        $linebreak = self::getOption('linebreak');
        $html      = '';
        $strValues = array_map('strval', $this->values);

        foreach ($this->options as $option)
        {
            if ($option instanceof SelectOption)
            {
                $selected = in_array($option->getValue(), $strValues, true);
                $html .= $indent.$option->render($selected).$linebreak;
            }
            elseif ($option instanceof self)
            {
                $option->setIndentLevel($indentLvl + 1);
                $html .= $option->__toString();
            }
        }

        return $html;
    }

   /**
    * Returns an iterator over contained elements
    *
    * @return HTML_QuickForm2_Element_Select_OptionIterator
    */
    public function getIterator() : HTML_QuickForm2_Element_Select_OptionIterator
    {
        return new HTML_QuickForm2_Element_Select_OptionIterator($this->options);
    }

   /**
    * Returns a recursive iterator over contained elements
    *
    * @return   RecursiveIteratorIterator
    */
    public function getRecursiveIterator() : RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new HTML_QuickForm2_Element_Select_OptionIterator($this->options),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

   /**
    * Returns the number of options in the container
    *
    * @return   int
    */
    public function count() : int
    {
        return count($this->options);
    }
    
    /**
     * Counts all options in the select, ignoring optgroups.
     * If the recursive flag is true, this will count all
     * options in optgroups as well.
     *
     * @param bool $recursive
     * @return int
     */
    public function countOptions(bool $recursive=true) : int
    {
        $count = 0;
        
        foreach($this->options as $option)
        {
            if($option instanceof HTML_QuickForm2_Element_Select_Optgroup && $recursive)
            {
                $count += $option->countOptions($recursive);
                continue;
            }
            
            $count++;
        }
        
        return $count;
    }

    /**
     * Attempts to retrieve an option definition by the option value.
     * @param string $value
     * @return SelectOption|null
     */
    public function getOptionByValue(string $value) : ?SelectOption
    {
        foreach($this->options as $option)
        {
            if($option instanceof HTML_QuickForm2_Element_Select_Optgroup)
            {
                $selected = $option->getOptionByValue($value);
                if($selected !== null) {
                    return $selected;
                }

                continue;
            }

            if($option['attr']['value'] === $value) {
                return $option;
            }
        }

        return null;
    }
}
