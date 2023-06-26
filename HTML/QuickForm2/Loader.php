<?php
/**
 * Class with static methods for loading classes and files
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
 * Class with static methods for loading classes and files
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Loader
{
    public const ERROR_OBJECT_NOT_INSTANCE_OF = 139801;
    public const ERROR_THROWABLE_GIVEN_AS_OBJECT = 13902;
    public const ERROR_CLASS_DOES_NOT_EXIST = 13903;

    /**
    * Tries to load a given class
    *
    * If no $includeFile was provided, $className will be used with underscores
    * replaced with path separators and '.php' extension appended
    *
    * @param string $className   Class name to load
    * @param string $includeFile Name of the file (supposedly) containing the given class
    * @param bool   $autoload    Whether we should try autoloading
    * @deprecated
    */
    public static function loadClass($className, $includeFile = null, $autoload = false) : void
    {
        // Replaced by autoloading
    }

   /**
    * Checks whether the file exists in the include path
    *
    * @param string $fileName file name
    *
    * @return   bool
    */
    public static function fileExists($fileName) : bool
    {
        $fp = @fopen($fileName, 'r', true);
        if (is_resource($fp)) {
            fclose($fp);
            return true;
        }
        return false;
    }

   /**
    * Loading of HTML_QuickForm2_* classes suitable for SPL autoload mechanism
    *
    * This method will only try to load a class if its name starts with
    * HTML_QuickForm2. Register with the following:
    * <code>
    * spl_autoload_register(array('HTML_QuickForm2_Loader', 'autoload'));
    * </code>
    *
    * @param string $class Class name
    *
    * @return   bool    Whether class loaded successfully
    * @deprecated
    */
    public static function autoload($class) : bool
    {
        return true;
    }

    /**
     * If the target object is not an instance of the target class
     * or interface, throws an exception.
     *
     * NOTE: If an exception is passed as object, an exception is
     * thrown with the error code {@see ClassHelper::ERROR_THROWABLE_GIVEN_AS_OBJECT},
     * and the original exception as previous exception.
     *
     * @template ClassInstanceType
     * @param class-string<ClassInstanceType> $class
     * @param object $object
     * @param int $errorCode Default is {@see self::ERROR_OBJECT_NOT_INSTANCE_OF}
     * @return ClassInstanceType
     *
     * @throws HTML_QuickForm2_InvalidArgumentException {@see self::ERROR_THROWABLE_GIVEN_AS_OBJECT}
     * @throws HTML_QuickForm2_NotFoundException {@see self::ERROR_OBJECT_NOT_INSTANCE_OF}
     */
    public static function requireObjectInstanceOf(string $class, object $object, int $errorCode=0)
    {
        if($errorCode === 0) {
            $errorCode = self::ERROR_OBJECT_NOT_INSTANCE_OF;
        }

        if($object instanceof Throwable)
        {
            throw new HTML_QuickForm2_InvalidArgumentException(
                $class,
                self::ERROR_THROWABLE_GIVEN_AS_OBJECT,
                $object
            );
        }

        if(!class_exists($class) && !interface_exists($class) && !trait_exists($class))
        {
            throw new HTML_QuickForm2_NotFoundException(
                sprintf(
                    'Target class, trait or interface [%s] does not exist.',
                    $class
                ),
                $errorCode
            );
        }

        if(is_a($object, $class, true))
        {
            return $object;
        }

        throw new HTML_QuickForm2_InvalidArgumentException(
            sprintf(
                'The target object [%s] is not an instance of [%s].',
                get_class($object),
                $class
            ),
            $errorCode
        );
    }

    /**
     * Throws an exception if the target class can not be found.
     *
     * @param class-string $className
     * @return class-string
     * @throws HTML_QuickForm2_NotFoundException {@see self::ERROR_CLASS_DOES_NOT_EXIST}
     */
    public static function requireClassExists(string $className) : string
    {
        if(class_exists($className))
        {
            return $className;
        }

        throw new HTML_QuickForm2_NotFoundException(
            sprintf('The class [%s] does not exist.', $className),
            self::ERROR_CLASS_DOES_NOT_EXIST
        );
    }
}
