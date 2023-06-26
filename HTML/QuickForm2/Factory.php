<?php
/**
 * Static Factory class for HTML_QuickForm2 package
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
 * Static factory class
 *
 * The class handles instantiation of Element and Rule objects as well as
 * registering of new Element and Rule classes.
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Factory
{
    public const ERROR_ELEMENT_TYPE_UNKNOWN = 139901;

    private const RULE_MERGE_CALLBACK_TEMPLATE = array(HTML_QuickForm2_Rule::class, 'mergeConfig');

    /**
    * List of element types known to Factory
    * @var array<string,array{0:string,1:string|NULL}>
    */
    protected static array $elementTypes = array(
        'button'        => HTML_QuickForm2_Element_Button::class,
        'checkbox'      => HTML_QuickForm2_Element_InputCheckbox::class,
        'date'          => HTML_QuickForm2_Element_Date::class,
        'fieldset'      => HTML_QuickForm2_Container_Fieldset::class,
        'group'         => HTML_QuickForm2_Container_Group::class,
        'file'          => HTML_QuickForm2_Element_InputFile::class,
        'hidden'        => HTML_QuickForm2_Element_InputHidden::class,
        'hierselect'    => HTML_QuickForm2_Element_Hierselect::class,
        'image'         => HTML_QuickForm2_Element_InputImage::class,
        'inputbutton'   => HTML_QuickForm2_Element_InputButton::class,
        'password'      => HTML_QuickForm2_Element_InputPassword::class,
        'radio'         => HTML_QuickForm2_Element_InputRadio::class,
        'repeat'        => HTML_QuickForm2_Container_Repeat::class,
        'reset'         => HTML_QuickForm2_Element_InputReset::class,
        'script'        => HTML_QuickForm2_Element_Script::class,
        'select'        => HTML_QuickForm2_Element_Select::class,
        'static'        => HTML_QuickForm2_Element_Static::class,
        'submit'        => HTML_QuickForm2_Element_InputSubmit::class,
        'text'          => HTML_QuickForm2_Element_InputText::class,
        'textarea'      => HTML_QuickForm2_Element_Textarea::class
    );

   /**
    * List of registered rules
    * @var array<string,array{0:string,1:array|NULL}>
    */
    protected static array $registeredRules = array(
        'nonempty'      => array(HTML_QuickForm2_Rule_Nonempty::class, null),
        'empty'         => array(HTML_QuickForm2_Rule_Empty::class, null),
        'required'      => array(HTML_QuickForm2_Rule_Required::class, null),
        'compare'       => array(HTML_QuickForm2_Rule_Compare::class, null),
        'eq'            => array(HTML_QuickForm2_Rule_Compare::class, array('operator' => '===')),
        'neq'           => array(HTML_QuickForm2_Rule_Compare::class, array('operator' => '!==')),
        'lt'            => array(HTML_QuickForm2_Rule_Compare::class, array('operator' => '<')),
        'lte'           => array(HTML_QuickForm2_Rule_Compare::class, array('operator' => '<=')),
        'gt'            => array(HTML_QuickForm2_Rule_Compare::class, array('operator' => '>')),
        'gte'           => array(HTML_QuickForm2_Rule_Compare::class, array('operator' => '>=')),
        'regex'         => array(HTML_QuickForm2_Rule_Regex::class, null),
        'callback'      => array(HTML_QuickForm2_Rule_Callback::class, null),
        'length'        => array(HTML_QuickForm2_Rule_Length::class, null),
        'minlength'     => array(HTML_QuickForm2_Rule_Length::class, array('max' => 0)),
        'maxlength'     => array(HTML_QuickForm2_Rule_Length::class, array('min' => 0)),
        'maxfilesize'   => array(HTML_QuickForm2_Rule_MaxFileSize::class, null),
        'mimetype'      => array(HTML_QuickForm2_Rule_MimeType::class, null),
        'each'          => array(HTML_QuickForm2_Rule_Each::class, null),
        'notcallback'   => array(HTML_QuickForm2_Rule_NotCallback::class, null),
        'notregex'      => array(HTML_QuickForm2_Rule_NotRegex::class, null),
        'email'         => array(HTML_QuickForm2_Rule_Email::class, null)
    );

   /**
    * Registers a new element type
    *
    * @param string $type Type name (treated case-insensitively)
    * @param class-string $className Class name
    */
    public static function registerElement(string $type, string $className) : void
    {
        self::$elementTypes[strtolower($type)] = $className;
    }

   /**
    * Checks whether an element type is known to factory
    *
    * @param string $type Type name (treated case-insensitively)
    *
    * @return bool
    */
    public static function isElementRegistered(string $type) : bool
    {
        return isset(self::$elementTypes[strtolower($type)]);
    }


   /**
    * Creates a new element object of the given type
    *
    * @param string $type Type name (treated case-insensitively)
    * @param string|NULL $name Element name (passed to element's constructor)
    * @param string|array|NULL $attributes Element attributes (passed to element's constructor)
    * @param array<mixed> $data Element-specific data (passed to element's constructor)
    *
    * @return   HTML_QuickForm2_Node     A created element
    * @throws   HTML_QuickForm2_InvalidArgumentException If type name is unknown {@see self::ERROR_ELEMENT_TYPE_UNKNOWN}
    * @throws   HTML_QuickForm2_NotFoundException If class for the element can
    *           not be found and/or loaded from file
    */
    public static function createElement(
        string $type, ?string $name = null, $attributes = null, array $data = array()
    ) : HTML_QuickForm2_Node
    {
        $className = self::getElementClassByType($type);

        return HTML_QuickForm2_Loader::requireObjectInstanceOf(
            HTML_QuickForm2_Node::class,
            new $className($name, $attributes, $data)
        );
    }

    /**
     * @param string $type
     * @return class-string
     * @throws HTML_QuickForm2_InvalidArgumentException
     * @throws HTML_QuickForm2_NotFoundException
     */
    public static function getElementClassByType(string $type) : string
    {
        $type = strtolower($type);

        if(isset(self::$elementTypes[$type]))
        {
            return HTML_QuickForm2_Loader::requireClassExists(self::$elementTypes[$type]);
        }
        
        throw new HTML_QuickForm2_InvalidArgumentException(
            sprintf(
                "Element type '%s' is not known",
                $type
            ),
            self::ERROR_ELEMENT_TYPE_UNKNOWN
        );
    }

   /**
    * Registers a new rule type
    *
    * @param string $type        Rule type name (treated case-insensitively)
    * @param string $className   Class name
    * @param string $includeFile DEPRECATED
    * @param mixed  $config      Configuration data for rules of the given type
    */
    public static function registerRule(
        string $type, string $className, ?string $includeFile = null, $config = null
    ) : void
    {
        self::$registeredRules[strtolower($type)] = array($className, $config);
    }

   /**
    * Checks whether a rule type is known to Factory
    *
    * @param string $type Rule type name (treated case-insensitively)
    *
    * @return   bool
    */
    public static function isRuleRegistered(string $type) : bool
    {
        return isset(self::$registeredRules[strtolower($type)]);
    }

   /**
    * Creates a new Rule of the given type
    *
    * @param string $type Rule type name (treated case-insensitively)
    * @param HTML_QuickForm2_Node $owner Element to validate by the rule
    * @param string|Stringable|NULL $message Message to display if validation fails
    * @param mixed $config Configuration data for the rule
    *
    * @return HTML_QuickForm2_Rule
    *
    * @throws HTML_QuickForm2_InvalidArgumentException If rule type is unknown
    * @throws HTML_QuickForm2_NotFoundException If class for the rule can't be found and/or loaded from file
    */
    public static function createRule(
        string $type, HTML_QuickForm2_Node $owner, $message = null, $config = null
    ) : HTML_QuickForm2_Rule
    {
        $def = self::getRuleDefByType($type);
        $className = $def['class'];

        // Merge the configs using the rule's mergeConfig() method.
        if ($def['config'] !== null) {
            $config = $def['configCallback']($config, $def['config']);
        }

        return new $className($owner, (string)$message, $config);
    }

    /**
     * @param string $type
     * @return array{class:class-string,config:mixed|NULL,configCallback:callable}
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    public static function getRuleDefByType(string $type) : array
    {
        $type = strtolower($type);

        if (isset(self::$registeredRules[$type]))
        {
            $callback = self::RULE_MERGE_CALLBACK_TEMPLATE;
            $callback[0] = self::$registeredRules[$type][0];

            return array(
                'class' => HTML_QuickForm2_Loader::requireClassExists(self::$registeredRules[$type][0]),
                'config' => self::$registeredRules[$type][1],
                'configCallback' => $callback
            );
        }

        throw new HTML_QuickForm2_InvalidArgumentException(
            "Rule '$type' is not known"
        );
    }
}
