<?php
/**
 * Interface for data sources used by HTML_QuickForm2 objects
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
 * Interface for data sources used by HTML_QuickForm2 objects
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
interface HTML_QuickForm2_DataSource
{
    public function getInstanceID() : int;

   /**
    * Returns value for the element with the given name
    *
    * If data source doesn't have a requested value it should return null
    *
    * @param string $name Element's name
    *
    * @return   mixed   Element's value
    */
    public function getValue(string $name);

    /**
     * @return array<string,mixed>
     */
    public function getValues() : array;

    /**
     * @param array<string,mixed> $values
     * @return $this
     */
    public function setValues(array $values): self;

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setValue(string $name, $value) : self;
}
