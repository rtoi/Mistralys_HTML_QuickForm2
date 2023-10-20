<?php
/**
 * Data source for HTML_QuickForm2 objects based on superglobal arrays
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
 * Data source for HTML_QuickForm2 objects based on superglobal arrays
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_DataSource_SuperGlobal
    extends HTML_QuickForm2_DataSource_Array
    implements HTML_QuickForm2_DataSource_Submit
{
   /**
    * Information on file uploads (from $_FILES)
    * @var array
    */
    protected $files = array();

   /**
     * Keys present in the $_FILES array
     */
    private static array $_fileKeys = array('name', 'type', 'size', 'tmp_name', 'error');

   /**
    * Class constructor, initializes the internal arrays from super globals.
    *
    * @param string $requestMethod  Request method (GET or POST)
    */
    public function __construct($requestMethod = 'POST')
    {
        if ('GET' === strtoupper($requestMethod)) {
            parent::__construct($_GET);
        } else {
            parent::__construct($_POST);
            $this->files = $_FILES;
        }
    }

    public function getUpload(string $name) : ?array
    {
        if (empty($this->files)) {
            return null;
        }
        
        $pos = strpos($name, '[');
        
        if (false !== $pos) {
            $tokens = explode('[', str_replace(']', '', $name));
            $base   = array_shift($tokens);
            $value  = array();
            if (!isset($this->files[$base]['name'])) {
                return null;
            }
            foreach (self::$_fileKeys as $key) {
                $value[$key] = $this->files[$base][$key];
            }

            do {
                $token = array_shift($tokens);
                if (!isset($value['name'][$token])) {
                    return null;
                }
                foreach (self::$_fileKeys as $key) {
                    $value[$key] = $value[$key][$token];
                }
            } while (!empty($tokens));
            return $value;
        } elseif (isset($this->files[$name])) {
            return $this->files[$name];
        } else {
            return null;
        }
    }
}
