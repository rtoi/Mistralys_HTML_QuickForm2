<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Traits
 * @see \HTML\QuickForm2\Traits\ContainerElementMethodsTrait
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Traits;

use HTML\QuickForm2\ElementFactory;
use HTML_QuickForm2_Container_Fieldset;
use HTML_QuickForm2_Container_Group;
use HTML_QuickForm2_Container_Repeat;
use HTML_QuickForm2_Element_Button;
use HTML_QuickForm2_Element_Date;
use HTML_QuickForm2_Element_Hierselect;
use HTML_QuickForm2_Element_InputButton;
use HTML_QuickForm2_Element_InputCheckbox;
use HTML_QuickForm2_Element_InputFile;
use HTML_QuickForm2_Element_InputHidden;
use HTML_QuickForm2_Element_InputImage;
use HTML_QuickForm2_Element_InputPassword;
use HTML_QuickForm2_Element_InputRadio;
use HTML_QuickForm2_Element_InputReset;
use HTML_QuickForm2_Element_InputSubmit;
use HTML_QuickForm2_Element_InputText;
use HTML_QuickForm2_Element_Script;
use HTML_QuickForm2_Element_Select;
use HTML_QuickForm2_Element_Static;
use HTML_QuickForm2_Element_Textarea;
use Stringable;

/**
 * Trait containing the <code>addXXX()</code> methods used
 * to add known element types to a container. Should always
 * be used in conjunction with {@see ContainerElementMethodsInterface}.
 *
 * @package HTML_QuickForm2
 * @subpackage Traits
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see ContainerElementMethodsInterface
 */
trait ContainerElementMethodsTrait
{
    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputText
     */
    public function addText(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputText
    {
        $el = ElementFactory::text($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Textarea
     */
    public function addTextarea(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Textarea
    {
        $el = ElementFactory::textarea($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Button
     */
    public function addButton(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Button
    {
        $el = ElementFactory::button($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Select
     */
    public function addSelect(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Select
    {
        $el = ElementFactory::select($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Date
     */
    public function addDate(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Date
    {
        $el = ElementFactory::date($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputFile
     */
    public function addFile(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputFile
    {
        $el = ElementFactory::file($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputHidden
     */
    public function addHidden(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputHidden
    {
        $el = ElementFactory::hidden($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Static
     */
    public function addStatic(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Static
    {
        $el = ElementFactory::static($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Container_Fieldset
     */
    public function addFieldset(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Container_Fieldset
    {
        $el = ElementFactory::fieldset($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Container_Group
     */
    public function addGroup(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Container_Group
    {
        $el = ElementFactory::group($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Container_Repeat
     */
    public function addRepeat(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Container_Repeat
    {
        $el = ElementFactory::repeat($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputCheckbox
     */
    public function addCheckbox(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputCheckbox
    {
        $el = ElementFactory::checkbox($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputRadio
     */
    public function addRadio(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputRadio
    {
        $el = ElementFactory::radio($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputSubmit
     */
    public function addSubmit(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputSubmit
    {
        $el = ElementFactory::submit($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputPassword
     */
    public function addPassword(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputPassword
    {
        $el = ElementFactory::password($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Script
     */
    public function addScript(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Script
    {
        $el = ElementFactory::script($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputReset
     */
    public function addReset(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputReset
    {
        $el = ElementFactory::reset($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Hierselect
     */
    public function addHierselect(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Hierselect
    {
        $el = ElementFactory::hierselect($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputButton
     */
    public function addInputButton(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputButton
    {
        $el = ElementFactory::inputButton($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputImage
     */
    public function addImage(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputImage
    {
        $el = ElementFactory::image($name, $attributes, $data);
        $this->appendChild($el);
        return $el;
    }
}
