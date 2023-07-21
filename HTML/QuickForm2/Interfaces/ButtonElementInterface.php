<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Interfaces
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Interfaces;

/**
 * Interface for all elements that render as buttons
 * ({@see \HTML_QuickForm2_Element_InputButton} and
 * {@see \HTML_QuickForm2_Element_Button}).
 *
 * @package HTML_QuickForm2
 * @subpackage Interfaces
 */
interface ButtonElementInterface
{
    public function setLabel($label) : self;
    public function isSubmit() : bool;
}
