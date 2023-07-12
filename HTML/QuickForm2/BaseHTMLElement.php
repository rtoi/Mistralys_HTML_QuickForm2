<?php
/**
 * HTML_Common2: port of HTML_Common package to PHP5
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2004-2022, Alexey Borzov <avb@php.net>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category HTML
 * @package  HTML_Common2
 * @author   Alexey Borzov <avb@php.net>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link     https://pear.php.net/package/HTML_Common2
 */

/**
 * Base class for HTML classes
 *
 * Implements methods for working with HTML attributes, parsing and generating
 * attribute strings. Port of HTML_Common class for PHP4 originally written by
 * Adam Daniel with contributions from numerous other developers.
 *
 * @category   HTML
 * @package    HTML_Common2
 * @author     Alexey Borzov <avb@php.net>
 * @license    https://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: @package_version@
 * @link       https://pear.php.net/package/HTML_Common2
 * @implements ArrayAccess<string|NULL,string>
 */
abstract class BaseHTMLElement implements ArrayAccess
{
    /**
     * Constant for predefined 'charset' option
     */
    public const OPTION_CHARSET   = 'charset';

    /**
     * Constant for predefined 'indent' option
     */
    public const OPTION_INDENT    = 'indent';

    /**
     * Constant for predefined 'linebreak' option
     */
    public const OPTION_LINEBREAK = 'linebreak';

    /**
     * Line break for Windows platform
     */
    public const LINEBREAK_WIN  = "\15\12";

    /**
     * Line break for Unix platform
     */
    public const LINEBREAK_UNIX = "\12";

    /**
     * Line break for Mac platform
     */
    public const LINEBREAK_MAC  = "\15";

    /**
     * Indentation level of the element
     *
     * @var int
     */
    private int $_indentLevel = 0;

    /**
     * Comment associated with the element
     *
     * @var string|null
     */
    private ?string $_comment = null;

    /**
     * Global options for all elements generated by subclasses of HTML_Common2
     *
     * Preset options are
     * - 'charset': charset parameter used in htmlspecialchars() calls,
     *   defaults to 'ISO-8859-1'
     * - 'indent': string used to indent HTML elements, defaults to "\11"
     * - 'linebreak': string used to indicate linebreak, defaults to "\12"
     *
     * @var array<string,mixed>
     */
    private static array $_options = [
        self::OPTION_CHARSET   => 'ISO-8859-1',
        self::OPTION_INDENT    => "\11",
        self::OPTION_LINEBREAK => self::LINEBREAK_UNIX
    ];

    /**
     * Mapping "platform name" => "linebreak symbol(s)"
     *
     * @var array<string,string>
     */
    private static array $_linebreaks = [
        'win'  => self::LINEBREAK_WIN,
        'unix' => self::LINEBREAK_UNIX,
        'mac'  => self::LINEBREAK_MAC
    ];

    /**
     * Class constructor, sets default attributes
     *
     * @param array<string,string|int|float|Stringable|NULL>|string|NULL $attributes Array of attribute 'name' => 'value' pairs
     *                                 or HTML attribute string
     */
    public function __construct($attributes = null)
    {
        $this->mergeAttributes($attributes);
    }

    /**
     * Sets global option(s)
     *
     * @param string|array<string,mixed> $nameOrOptions Option name or array
     *                                    ('option name' => 'option value')
     * @param mixed        $value         Option value,
     *                                    if first argument is not an array
     *
     * @return void
     */
    public static function setOption($nameOrOptions, $value = null) : void
    {
        if (is_array($nameOrOptions)) {
            foreach ($nameOrOptions as $k => $v) {
                self::setOption($k, $v);
            }
        } else {
            if (self::OPTION_LINEBREAK === $nameOrOptions
                && isset(self::$_linebreaks[$value])
            ) {
                $value = self::$_linebreaks[$value];
            }
            self::$_options[$nameOrOptions] = $value;
        }
    }

    /**
     * Returns global option(s)
     *
     * @param string|NULL $name Option name, or NULL to return all options.
     *
     * @return mixed|array<string,mixed>|NULL Option value, null if option does not exist,
     *               array of all options if $name is not given
     */
    public static function getOption(?string $name = null)
    {
        if (null === $name) {
            return self::$_options;
        }

        return self::$_options[$name] ?? null;
    }

    // region: Attribute handling

    /**
     * Associative array of attributes
     *
     * @var array<string,string>
     */
    protected array $attributes = [];

    /**
     * Changes to attributes in this list will be announced via onAttributeChange()
     * method rather than performed by HTML_Common2 class itself
     *
     * @var string[]
     * @see onAttributeChange()
     */
    protected array $watchedAttributes = [];

    /**
     * Called if trying to change an attribute with name in $watchedAttributes
     *
     * This method is called for each attribute whose name is in the
     * $watchedAttributes array and which is being changed by setAttribute(),
     * setAttributes() or mergeAttributes() or removed via removeAttribute().
     * Note that the operation for the attribute is not carried on after calling
     * this method, it is the responsibility of this method to change or remove
     * (or not) the attribute.
     *
     * @param string      $name  Attribute name
     * @param string|int|float|Stringable|null $value Attribute value, null if attribute is being removed
     *
     * @return void
     */
    protected function onAttributeChange(string $name, $value = null) : void
    {
    }

    /**
     * Parses the HTML attributes given as string
     *
     * @param string $attrString HTML attribute string
     *
     * @return array<string,string> An associative array of attributes
     */
    protected static function parseAttributes(string $attrString) : array
    {
        $attributes = [];
        $matchCount = preg_match_all(
            "/(([A-Za-z_:]|[^\\x00-\\x7F])([A-Za-z0-9_:.-]|[^\\x00-\\x7F])*)"
            . "([ \\n\\t\\r]+)?"
            . "(=([ \\n\\t\\r]+)?(\"[^\"]*\"|'[^']*'|[^ \\n\\t\\r]*))?/",
            $attrString,
            $regs
        );
        for ($i = 0; $i < (int)$matchCount; $i++) {
            $name  = trim($regs[1][$i]);
            $check = trim($regs[0][$i]);
            $value = trim($regs[7][$i]);
            if ($name === $check) {
                $attributes[strtolower($name)] = strtolower($name);
            } else {
                if (!empty($value) && ($value[0] === '\'' || $value[0] === '"')) {
                    $value = substr($value, 1, -1);
                }
                $attributes[strtolower($name)] = $value;
            }
        }
        return $attributes;
    }

    /**
     * Creates a valid attribute array from either a string or an array
     *
     * @param string|array<int|string,string|int|float|Stringable|NULL>|NULL $attributes Array of attributes or HTML attribute string
     *
     * @return array<string,string> An associative array of attributes
     */
    protected static function prepareAttributes($attributes) : array
    {
        if($attributes === null) {
            return array();
        }

        if (is_string($attributes)) {
            return self::parseAttributes($attributes);
        }

        $prepared = [];

        foreach ($attributes as $key => $value) {
            if (is_int($key)) {
                $key = strtolower($value);
                $prepared[$key] = $key;
            } else {
                $prepared[strtolower($key)] = (string)$value;
            }
        }

        return $prepared;
    }

    /**
     * Removes an attribute from an attribute array
     *
     * @param array<string,string> $attributes Attribute array
     * @param string $name Name of attribute to remove
     * @return void
     */
    protected static function removeAttributeArray(array &$attributes, string $name) : void
    {
        unset($attributes[strtolower($name)]);
    }

    /**
     * Creates HTML attribute string from array
     *
     * @param array<string,string> $attributes Attribute array
     * @return string Attribute string
     */
    public static function getAttributesString(array $attributes) : string
    {
        $str = '';
        $charset = self::getOption(self::OPTION_CHARSET);

        foreach ($attributes as $key => $value) {
            $str .=
                ' ' .
                $key .
                '="' .
                htmlspecialchars($value, ENT_QUOTES, $charset) .
                '"';
        }

        return $str;
    }

    /**
     * Sets the value of the attribute
     *
     * @param string $name  Attribute name
     * @param string|int|float|Stringable|NULL $value Attribute value (will be set to $name if omitted)
     *
     * @return $this
     */
    public function setAttribute(string $name, $value = null) : self
    {
        $name = strtolower($name);
        if (is_null($value)) {
            $value = $name;
        }

        $value = (string)$value;

        if (in_array($name, $this->watchedAttributes, true)) {
            $this->onAttributeChange($name, $value);
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    /**
     * Returns the value of an attribute
     *
     * @param string $name Attribute name
     *
     * @return string|null Attribute value, null if attribute does not exist
     */
    public function getAttribute($name)
    {
        $name = strtolower($name);
        return $this->attributes[$name] ?? null;
    }

    /**
     * Sets the attributes
     *
     * @param string|array<string|int,string|int|float|Stringable|NULL>|NULL $attributes Array of 'name' => 'value' pairs
     *                                      or HTML attribute string
     *
     * @return $this
     */
    public function setAttributes($attributes) : self
    {
        $attributes = self::prepareAttributes($attributes);
        $watched    = [];
        foreach ($this->watchedAttributes as $watchedKey) {
            if (isset($attributes[$watchedKey])) {
                $this->setAttribute($watchedKey, $attributes[$watchedKey]);
                unset($attributes[$watchedKey]);
            } else {
                $this->removeAttribute($watchedKey);
            }
            if (isset($this->attributes[$watchedKey])) {
                $watched[$watchedKey] = $this->attributes[$watchedKey];
            }
        }
        $this->attributes = array_merge($watched, $attributes);
        return $this;
    }

    /**
     * Returns the attribute array or string
     *
     * @param bool $asString Whether to return attributes as string
     *
     * @return       array|string
     * @psalm-return ($asString is true ? string : array<string, string>)
     */
    public function getAttributes($asString = false)
    {
        if ($asString) {
            return self::getAttributesString($this->attributes);
        } else {
            return $this->attributes;
        }
    }

    /**
     * Merges the existing attributes with the new ones
     *
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes Array of 'name' => 'value' pairs
     *                                      or HTML attribute string
     *
     * @return $this
     */
    public function mergeAttributes($attributes) : self
    {
        $attributes = self::prepareAttributes($attributes);
        foreach ($this->watchedAttributes as $watchedKey) {
            if (isset($attributes[$watchedKey])) {
                $this->onAttributeChange($watchedKey, $attributes[$watchedKey]);
                unset($attributes[$watchedKey]);
            }
        }
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Removes an attribute
     *
     * @param string $attribute Name of attribute to remove
     *
     * @return $this
     */
    public function removeAttribute(string $attribute) : self
    {
        if (in_array(strtolower($attribute), $this->watchedAttributes, true)) {
            $this->onAttributeChange(strtolower($attribute), null);
        } else {
            self::removeAttributeArray($this->attributes, $attribute);
        }

        return $this;
    }

    // endregion

    /**
     * Sets the indentation level
     *
     * @param int $level Indentation level
     *
     * @return $this
     */
    public function setIndentLevel($level)
    {
        $level = intval($level);
        if (0 <= $level) {
            $this->_indentLevel = $level;
        }
        return $this;
    }

    /**
     * Gets the indentation level
     *
     * @return int
     */
    public function getIndentLevel()
    {
        return $this->_indentLevel;
    }

    /**
     * Returns the string to indent the element
     *
     * @return string
     */
    protected function getIndent()
    {
        return str_repeat(
            self::getOption(self::OPTION_INDENT),
            $this->getIndentLevel()
        );
    }

    /**
     * Sets the comment for the element
     *
     * @param string|int|float|Stringable|null $comment String to output as HTML comment
     *
     * @return $this
     */
    public function setComment($comment) : self
    {
        if($comment !== null) {
            $comment = (string)$comment;
        }

        if($comment !== '') {
            $this->_comment = $comment;
        }

        return $this;
    }

    /**
     * Appends text to the element's comment. Automatically
     * adds a space character if needed.
     *
     * @param string|int|float|Stringable|null $comment
     * @return $this
     */
    public function appendComment($comment) : self
    {
        $comment = (string)$comment;

        if($comment === '') {
            return $this;
        }

        if(!empty($this->_comment)) {
            $comment = ' '.$comment;
        }

        $this->_comment .= $comment;

        return $this;
    }

    /**
     * Returns the comment associated with the element
     *
     * @return string|null
     */
    public function getComment() : ?string
    {
        return $this->_comment;
    }

    /**
     * Checks whether the element has given CSS class
     *
     * @param string $class CSS Class name
     *
     * @return bool
     */
    public function hasClass(string $class) : bool
    {
        $regex = '/(^|\s)' . preg_quote($class, '/') . '(\s|$)/';
        return (bool)preg_match($regex, $this->getAttribute('class'));
    }

    /**
     * Adds the given CSS class(es) to the element
     *
     * @param string|string[] $class Class name, multiple class names separated by
     *                            whitespace, array of class names
     *
     * @return $this
     */
    public function addClass($class) : self
    {
        if (!is_array($class)) {
            $class = preg_split('/\s+/', $class, -1, PREG_SPLIT_NO_EMPTY);
        }
        $curClass = null !== ($classAttr = $this->getAttribute('class'))
                    ? preg_split('/\s+/', $classAttr, -1, PREG_SPLIT_NO_EMPTY)
                    : [];

        foreach ($class as $c) {
            if (!in_array($c, $curClass, true)) {
                $curClass[] = $c;
            }
        }
        $this->setAttribute('class', implode(' ', $curClass));

        return $this;
    }

    /**
     * Removes the given CSS class(es) from the element
     *
     * @param string|string[] $class Class name, multiple class names separated by
     *                            whitespace, array of class names
     *
     * @return $this
     */
    public function removeClass($class) : self
    {
        if (!is_array($class)) {
            $class = preg_split('/\s+/', $class, -1, PREG_SPLIT_NO_EMPTY);
        }
        if (null === ($classAttr = $this->getAttribute('class'))) {
            $curClass = [];
        } else {
            $curClass = array_diff(
                preg_split('/\s+/', $classAttr, -1, PREG_SPLIT_NO_EMPTY),
                $class
            );
        }

        if ([] === $curClass) {
            $this->removeAttribute('class');
        } else {
            $this->setAttribute('class', implode(' ', $curClass));
        }
        return $this;
    }

    /**
     * Returns the HTML representation of the element
     *
     * This magic method allows using the instances of HTML_Common2 in string
     * contexts
     *
     * @return string
     */
    abstract public function __toString();

    // region: Array access

    #[ReturnTypeWillChange]
    /**
     * Whether an offset (HTML attribute) exists
     *
     * @param string $offset An offset to check for.
     *
     * @return boolean Returns true on success or false on failure.
     * @link   http://php.net/manual/en/arrayaccess.offsetexists.php
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->attributes[strtolower($offset)]);
    }

    #[ReturnTypeWillChange]
    /**
     * Returns the value at specified offset (i.e. attribute name)
     *
     * @param string $offset The offset to retrieve.
     *
     * @return string|null
     * @link   http://php.net/manual/en/arrayaccess.offsetget.php
     * @see    getAttribute()
     */
    public function offsetGet($offset) : ?string
    {
        return $this->getAttribute($offset);
    }

    #[ReturnTypeWillChange]
    /**
     * Assigns a value to the specified offset (i.e. attribute name)
     *
     * @param string|NULL $offset The offset to assign the value to
     * @param string $value  The value to set
     *
     * @return void
     * @link   http://php.net/manual/en/arrayaccess.offsetset.php
     * @see    setAttribute()
     */
    public function offsetSet($offset, $value) : void
    {
        if (null !== $offset) {
            $this->setAttribute($offset, $value);
        } else {
            // handles $foo[] = 'disabled';
            $this->setAttribute($value);
        }
    }

    #[ReturnTypeWillChange]
    /**
     * Unsets an offset (i.e. removes an attribute)
     *
     * @param string $offset The offset to unset
     *
     * @return void
     * @link   http://php.net/manual/en/arrayaccess.offsetunset.php
     * @see    removeAttribute()
     */
    public function offsetUnset($offset) : void
    {
        $this->removeAttribute($offset);
    }

    // endregion

    /**
     * Sets the <code>style</code> attribute.
     *
     * @param string $style
     * @return $this
     */
    public function setStyle(string $style) : self
    {
        return $this->setAttribute('style', $style);
    }

    public function getStyle() : ?string
    {
        return $this->getAttribute('style');
    }
}
