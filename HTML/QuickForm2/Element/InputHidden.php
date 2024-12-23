<?php
/**
 * Class for <input type="hidden" /> elements
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
 * Class for <input type="hidden" /> elements
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_InputHidden extends HTML_QuickForm2_Element_Input
{
    public const ERROR_HIDDEN_CANNOT_HAVE_VALIDATION = 140001;

    protected array $attributes = array('type' => 'hidden');

    public function isFreezable(): bool
    {
        return false;
    }

    /**
     * Disallows setting an error message on hidden elements
     *
     * @param string|NULL $error
     *
     * @return HTML_QuickForm2_Element_InputHidden
     * @throws HTML_QuickForm2_InvalidArgumentException if $error is not empty
     */
    public function setError(?string $error = null) : self
    {
        if (!empty($error)) {
            throw new HTML_QuickForm2_InvalidArgumentException(
                "Hidden elements cannot have validation errors",
                self::ERROR_HIDDEN_CANNOT_HAVE_VALIDATION
            );
        }

        return parent::setError($error);
    }

    public function render(HTML_QuickForm2_Renderer $renderer) : HTML_QuickForm2_Renderer
    {
        $renderer->renderHidden($this);
        $this->renderClientRules($renderer->getJavascriptBuilder());
        return $renderer;
    }
}
