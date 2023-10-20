<?php
/**
 * Class for static elements that only contain text or markup
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
 * Class for static elements that only contain text or markup
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_Static extends HTML_QuickForm2_Element
{
    public const ERROR_CANNOT_USE_TAG_NAME = 146001;

    /**
    * Name of the tag to wrap around static element's content
    * @var string|NULL
    */
    protected ?string $tagName = null;

   /**
    * Whether to output closing tag when $tagName is set and element's content is empty
    * @var bool
    */
    protected bool $forceClosingTag = true;

   /**
    * Contains options and data used for the element creation
    * - content: Content of the static element
    * @var array{content:string|NULL}
    */
    protected $data = array('content' => '');

   /**
    * Class constructor
    *
    * Static element can understand the following keys in $data parameter:
    *   - 'content': content of the static element, e.g. text or markup
    *   - 'tagName': name of the tag to wrap around content, e.g. 'div'.
    *     Using tag names corresponding to form elements will cause an Exception
    *   - 'forceClosingTag': whether to output closing tag in case of empty
    *     content, &lt;foo&gt;&lt;/foo&gt; vs. &lt;foo /&gt;
    *
    * @param string       $name       Element name
    * @param string|array $attributes Attributes (either a string or an array)
    * @param array        $data       Additional element data
    */
    public function __construct($name = null, $attributes = null, array $data = array())
    {
        if (!empty($data['tagName'])) {
            $this->setTagName(
                $data['tagName'],
                !array_key_exists('forceClosingTag', $data) || $data['forceClosingTag']
            );
        }
        unset($data['tagName'], $data['forceClosingTag']);
        parent::__construct($name, $attributes, $data);
    }

   /**
    * Intercepts setting 'name' and 'id' attributes
    *
    * Overrides parent method to allow removal of 'name' attribute on Static
    * elements
    *
    * @param string $name Attribute name
    * @param string|NULL $value Attribute value, null if attribute is being removed
    *
    * @throws   HTML_QuickForm2_InvalidArgumentException    if trying to
    *                                   remove a required attribute
    */
    protected function onAttributeChange(string $name, $value = null) : void
    {
        if ('name' === $name && null === $value) {
            unset($this->attributes['name']);
        } else {
            parent::onAttributeChange($name, $value);
        }
    }

   /**
    * Sets the element's name
    *
    * Passing null here will remove the name attribute
    *
    * @param string|null $name
    *
    * @return   HTML_QuickForm2_Element_Static
    */
    public function setName(?string $name) : self
    {
        if (null !== $name) {
            return parent::setName($name);
        }

        return $this->removeAttribute('name');
    }

    public function getType() : string
    {
        return 'static';
    }

    public function isFreezable(): bool
    {
        return false;
    }

   /**
    * Sets the contents of the static element
    *
    * @param string|NULL $content Static content
    *
    * @return $this
    */
    public function setContent(?string $content) : self
    {
        $this->data['content'] = $content;
        return $this;
    }

   /**
    * Returns the contents of the static element
    *
    * @return string|NULL
    */
    public function getContent() : ?string
    {
        return $this->data['content'];
    }

   /**
    * Static element's content can also be set via this method
    *
    * @param mixed $value
    *
    * @return $this
    */
    public function setValue($value) : self
    {
        if($value !== null) {
            $value = (string)$value;
        }

        $this->setContent($value);
        return $this;
    }

   /**
    * Static elements have no value
    *
    * @return    null
    */
    public function getRawValue()
    {
        return null;
    }

    public function __toString()
    {
        $prefix = $this->getIndent();
        if ($comment = $this->getComment()) {
            $prefix .= '<!-- ' . $comment . ' -->'
                       . BaseHTMLElement::getOption('linebreak') . $this->getIndent();
        }

        if (!$this->tagName) {
            return $prefix . $this->getContent();
        }

        if ('' !== $this->getContent()) {
            return $prefix . '<' . $this->tagName . $this->getAttributes(true)
                   . '>' . $this->getContent() . '</' . $this->tagName . '>';
        }

        return $prefix . '<' . $this->tagName . $this->getAttributes(true)
               . ($this->forceClosingTag ? '></' . $this->tagName . '>' : ' />');
    }

    public function getJavascriptValue(bool $inContainer = false) : string
    {
        return '';
    }

    public function getJavascriptTriggers() : array
    {
        return array();
    }

   /**
    * Called when the element needs to update its value from form's data sources
    *
    * Static elements content can be updated with default form values.
    */
    protected function updateValue() : void
    {
        $name = $this->getName();

        $ds = $this->resolveDataSourceByName($name);

        if($ds) {
            $this->setContent($ds->getValue($name));
        }
    }

   /**
    * Sets the name of the HTML tag to wrap around static element's content
    *
    * @param string|NULL $name Tag name
    * @param bool $forceClosing Whether to output closing tag in case of empty contents
    *
    * @throws HTML_QuickForm2_InvalidArgumentException when trying to set a tag
    *       name corresponding to a form element
    * @return $this
    */
    public function setTagName(?string $name, bool $forceClosing = true) : self
    {
        // Prevent people shooting themselves in the proverbial foot
        if (in_array(
            strtolower($name),
            array('form', 'fieldset', 'button', 'input', 'select', 'textarea'))
        ) {
            throw new HTML_QuickForm2_InvalidArgumentException(
                sprintf(
                "Do not use tag name '%s' with Static element, use proper element class",
                    $name
                ),
                self::ERROR_CANNOT_USE_TAG_NAME
            );
        }

        $this->tagName = $name;
        $this->forceClosingTag = $forceClosing;

        return $this;
    }
}
