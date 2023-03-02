<?php
/**
 * Class for <select> elements
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

/**
 * Class representing a <select> element
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_Select extends HTML_QuickForm2_Element
{
    protected $persistent = true;

   /**
    * Values for the select element (i.e. values of the selected options)
    * @var  array
    */
    protected array $values = array();

   /**
    * Possible values for select elements
    *
    * A value is considered possible if it is present as a value attribute of
    * some option and that option is not disabled.
    * @var array
    */
    protected array $possibleValues = array();


   /**
    * Object containing options for the <select> element
    * @var  HTML_QuickForm2_Element_Select_OptionContainer
    */
    protected HTML_QuickForm2_Element_Select_OptionContainer $optionContainer;

   /**
    * Enable intrinsic validation by default
    * @var  array
    */
    protected $data = array('intrinsic_validation' => true);

   /**
    * Class constructor
    *
    * Select element can understand the following keys in $data parameter:
    *   - 'options': data to populate element's options with. Passed to
    *     {@link loadOptions()} method.
    *   - 'intrinsic_validation': setting this to false will disable
    *     that validation, {@link getValue()} will then return all submit
    *     values, not just those corresponding to options present in the
    *     element. May be useful in AJAX scenarios.
    *
    * @param string       $name       Element name
    * @param string|array $attributes Attributes (either a string or an array)
    * @param array        $data       Additional element data
    *
    * @throws   HTML_QuickForm2_InvalidArgumentException    if junk is given in $options
    */
    public function __construct($name = null, $attributes = null, array $data = array())
    {
        $options = $data['options'] ?? array();
        unset($data['options']);
        parent::__construct($name, $attributes, $data);
        $this->loadOptions($options);
        $this->initSelect();
    }

    /**
     * Overridable by extending classes to avoid having
     * to redeclare the constructor.
     *
     * @return void
     */
    protected function initSelect() : void
    {
    }

    public function getType() : string
    {
        return 'select';
    }

    public function __toString()
    {
        if ($this->frozen) {
            return $this->getFrozenHtml();
        }

        if (empty($this->attributes['multiple'])) {
            $attrString = $this->getAttributes(true);
        } else {
            $this->attributes['name'] .= '[]';
            $attrString = $this->getAttributes(true);
            $this->attributes['name']  = substr($this->attributes['name'], 0, -2);
        }

        $indent = $this->getIndent();

        return $indent . '<select' . $attrString . '>' .
               self::getOption('linebreak') .
               $this->optionContainer->__toString() .
               $indent . '</select>';
    }

    protected function getFrozenHtml() : string
    {
        if (null === ($value = $this->getValue())) {
            return '&nbsp;';
        }
        $valueHash = is_array($value)? array_flip($value): array($value => true);
        $options   = array();
        foreach ($this->optionContainer->getRecursiveIterator() as $child) {
            if (is_array($child) && isset($valueHash[$child['attr']['value']])
                && empty($child['attr']['disabled'])
            ) {
                $options[] = $child['text'];
            }
        }

        $html = implode('<br />', $options);
        if ($this->persistent) {
            $name = $this->attributes['name'] .
                    (empty($this->attributes['multiple'])? '': '[]');
            // Only use id attribute if doing single hidden input
            $idAttr = (1 === count($valueHash))? array('id' => $this->getId()): array();
            foreach ($valueHash as $key => $item) {
                $html .= '<input type="hidden"' . self::getAttributesString(array(
                             'name'  => $name,
                             'value' => $key
                         ) + $idAttr) . ' />';
            }
        }
        return $html;
    }

   /**
    * Returns the value of the <select> element
    *
    * Please note that the returned value may not necessarily be equal to that
    * passed to {@link setValue()}. It passes "intrinsic validation" confirming
    * that such value could possibly be submitted by this <select> element.
    * Specifically, this method will return null if the elements "disabled"
    * attribute is set, it will not return values if there are no options having
    * such a "value" attribute or if such options' "disabled" attribute is set.
    * It will also only return a scalar value for single selects, mimicking
    * the common browsers' behaviour.
    *
    * @return   mixed   "value" attribute of selected option in case of single
    *                   select, array of selected options' "value" attributes in
    *                   case of multiple selects, null if no options selected
    */
    public function getRawValue()
    {
        if (!empty($this->attributes['disabled']) || 0 === count($this->values)
            || ($this->data['intrinsic_validation']
                && (0 === count($this->optionContainer) || 0 === count($this->possibleValues)))
        ) {
            return null;
        }

        $values = array();
        foreach ($this->values as $value) {
            if (!$this->data['intrinsic_validation'] || !empty($this->possibleValues[$value])) {
                $values[] = $value;
            }
        }
        if (0 === count($values)) {
            return null;
        }

        if (!empty($this->attributes['multiple'])) {
            return $values;
        }

        if (1 === count($values)) {
            return $values[0];
        }

        // The <select> is not multiple, but several options are to be
        // selected. At least IE and Mozilla select the last selected
        // option in this case, we should do the same
        $lastValue = null;
        foreach ($this->optionContainer->getRecursiveIterator() as $child) {
            if (is_array($child) && in_array($child['attr']['value'], $values, true)) {
                $lastValue = $child['attr']['value'];
            }
        }
        return $lastValue;
    }

    public function setValue($value)
    {
        if (is_array($value)) {
            $this->values = array_values($value);
        } else {
            $this->values = array($value);
        }
        return $this;
    }

   /**
    * Loads <option>s (and <optgroup>s) for select element
    *
    * The method expects an array of options and optgroups:
    * <pre>
    * array(
    *     'option value 1' => 'option text 1',
    *     ...
    *     'option value N' => 'option text N',
    *     'optgroup label 1' => array(
    *         'option value' => 'option text',
    *         ...
    *     ),
    *     ...
    * )
    * </pre>
    * If value is a scalar, then array key is treated as "value" attribute of
    * <option> and value as this <option>'s text. If value is an array, then
    * key is treated as a "label" attribute of <optgroup> and value as an
    * array of <option>s for this <optgroup>.
    *
    * If you need to specify additional attributes for <option> and <optgroup>
    * tags, then you need to use {@link addOption()} and {@link addOptgroup()}
    * methods instead of this one.
    *
    * @param array $options
    *
    * @throws   HTML_QuickForm2_InvalidArgumentException    if junk is given in $options
    * @return   $this
    */
    public function loadOptions(array $options) : self
    {
        $this->possibleValues  = array();
        $this->optionContainer = new HTML_QuickForm2_Element_Select_OptionContainer(
            $this->values, $this->possibleValues
        );
        $this->loadOptionsFromArray($this->optionContainer, $options);
        return $this;
    }


    /**
     * Adds options from given array into given container
     *
     * @param HTML_QuickForm2_Element_Select_OptionContainer $container options will be
     *           added to this container
     * @param array $options options array
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    protected function loadOptionsFromArray(
        HTML_QuickForm2_Element_Select_OptionContainer $container, array $options
    ) : void {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $optgroup = $container->addOptgroup($key);
                $this->loadOptionsFromArray($optgroup, $value);
            } else {
                $container->addOption($value, $key);
            }
        }
    }


   /**
    * Adds a new option
    *
    * Please note that if you pass 'selected' attribute in the $attributes
    * parameter then this option's value will be added to <select>'s values.
    *
    * @param string|int|float $text Option text
    * @param string|int|float|NULL $value 'value' attribute for <option> tag
    * @param string|array|NULL $attributes Additional attributes for <option> tag
    *                     (either as a string or as an associative array)
    */
    public function addOption($text, $value, $attributes = null) : void
    {
        $this->optionContainer->addOption($text, $value, $attributes);
    }

    /**
     * Prepends an option to the beginning of the option collection.
     *
     * @param string|int|float $text
     * @param string|int|float|NULL $value
     * @param string|array|NULL $attributes
     */
    public function prependOption($text, $value, $attributes = null) : void
    {
        $this->optionContainer->prependOption($text, $value, $attributes);
    }

    /**
     * Adds a new optgroup
     *
     * @param string $label 'label' attribute for optgroup tag
     * @param string|array|NULL $attributes Additional attributes for <optgroup> tag
     *                     (either as a string or as an associative array)
     *
     * @return HTML_QuickForm2_Element_Select_Optgroup
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    public function addOptgroup(string $label, $attributes = null) : HTML_QuickForm2_Element_Select_Optgroup
    {
        return $this->optionContainer->addOptgroup($label, $attributes);
    }

    protected function updateValue() : void
    {
        if (!$this->getAttribute('multiple')) {
            parent::updateValue();
            return;
        }

        $name = $this->getName();

        foreach ($this->getDataSources() as $ds)
        {
            $value = $ds->getValue($name);

            if (
                $value !== null
                ||
                $ds instanceof HTML_QuickForm2_DataSource_Submit
                ||
                (
                    $ds instanceof HTML_QuickForm2_DataSource_NullAware
                    &&
                    $ds->hasValue($name)
                )
            ) {
                $this->setValue((array)$value);
                return;
            }
        }
    }

    /**
     * Get the select element's option container, e.g. for rendering purposes.
     *
     * @return HTML_QuickForm2_Element_Select_OptionContainer
     */
    public function getOptionContainer() : HTML_QuickForm2_Element_Select_OptionContainer
    {
        return $this->optionContainer;
    }
    
    public function countOptions(bool $recursive=true) : int
    {
        return $this->optionContainer->countOptions($recursive);
    }
}
