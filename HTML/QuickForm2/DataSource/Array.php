<?php
/**
 * Array-based data source for HTML_QuickForm2 objects
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
 * Array-based data source for HTML_QuickForm2 objects
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_DataSource_Array implements HTML_QuickForm2_DataSource_NullAware
{
    /**
     * Array containing elements' values
     * @var array<string,mixed>
     */
    protected array $values;

    private static int $instanceCounter = 0;

    /**
     * Class constructor, initializes the values array
     *
     * @param array<string,mixed> $values Array containing the elements' values
     */
    public function __construct(array $values = array())
    {
        self::$instanceCounter++;

        $this->instanceID = self::$instanceCounter;
        $this->values = $values;
    }

    public function getInstanceID(): int
    {
        return $this->instanceID;
    }

    public function getValue(?string $name)
    {
        if ($name === null || $name === '' || empty($this->values)) {
            return null;
        }

        if (strpos((string)$name, '[')) {
            $tokens = explode('[', str_replace(']', '', $name));
            $value = $this->values;
            do {
                $token = array_shift($tokens);
                if (!is_array($value) || !isset($value[$token])) {
                    return null;
                }
                $value = $value[$token];
            } while (!empty($tokens));
            return $value;
        }

        return $this->values[$name] ?? null;
    }

    public function hasValue(?string $name) : bool
    {
        if ($name === null || $name === '' || empty($this->values)) {
            return false;
        }

        if (!strpos($name, '[')) {
            return array_key_exists($name, $this->values);
        }

        $tokens = explode('[', str_replace(']', '', $name));
        $value  = $this->values;
        do {
            $token = array_shift($tokens);
            if (!is_array($value) || !array_key_exists($token, $value)) {
                return false;
            }
            $value = $value[$token];
        } while (!empty($tokens));

        return true;
    }

    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setValue(string $name, $value) : self
    {
        $this->values[$name] = $value;
        return $this;
    }

    /**
     * Sets the values by merging them with the existing
     * values, if any.
     *
     * @param array<string,mixed> $values
     */
    public function setValues(array $values): self
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }
}
