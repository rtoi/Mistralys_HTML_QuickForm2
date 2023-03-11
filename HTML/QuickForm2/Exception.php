<?php
/**
 * Exception classes for HTML_QuickForm2
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
 * Base exception class for all QuickForm exceptions.
 *
 * @package HTML_QuickForm2
 */
class HTML_QuickForm2_Exception extends Exception
{
    /**
     * @param string|Stringable|NULL $message
     * @param int|null $code
     * @param Throwable|null $previous
     */
    public function __construct($message, ?int $code=null, ?Throwable $previous=null)
    {
        parent::__construct((string)$message, (int)$code, $previous);
    }
}
