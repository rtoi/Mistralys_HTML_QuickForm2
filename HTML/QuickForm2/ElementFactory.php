<?php

declare(strict_types=1);

namespace HTML\QuickForm2;

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
use HTML_QuickForm2_InvalidArgumentException;
use Stringable;

class ElementFactory
{
    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputText
     */
    public static function text(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputText
    {
        return new HTML_QuickForm2_Element_InputText($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Static
     */
    public static function static(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Static
    {
        return new HTML_QuickForm2_Element_Static($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Date
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    public static function date(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Date
    {
        return new HTML_QuickForm2_Element_Date($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Textarea
     */
    public static function textarea(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Textarea
    {
        return new HTML_QuickForm2_Element_Textarea($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Button
     */
    public static function button(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Button
    {
        return new HTML_QuickForm2_Element_Button($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Select
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    public static function select(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Select
    {
        return new HTML_QuickForm2_Element_Select($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputHidden
     */
    public static function hidden(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputHidden
    {
        return new HTML_QuickForm2_Element_InputHidden($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputFile
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    public static function file(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputFile
    {
        return new HTML_QuickForm2_Element_InputFile($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Container_Fieldset
     */
    public static function fieldset(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Container_Fieldset
    {
        return new HTML_QuickForm2_Container_Fieldset($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputCheckbox
     */
    public static function checkbox(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputCheckbox
    {
        return new HTML_QuickForm2_Element_InputCheckbox($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputRadio
     */
    public static function radio(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputRadio
    {
        return new HTML_QuickForm2_Element_InputRadio($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Container_Group
     */
    public static function group(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Container_Group
    {
        return new HTML_QuickForm2_Container_Group($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputImage
     */
    public static function image(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputImage
    {
        return new HTML_QuickForm2_Element_InputImage($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputSubmit
     */
    public static function submit(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputSubmit
    {
        return new HTML_QuickForm2_Element_InputSubmit($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputPassword
     */
    public static function password(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputPassword
    {
        return new HTML_QuickForm2_Element_InputPassword($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputReset
     */
    public static function reset(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputReset
    {
        return new HTML_QuickForm2_Element_InputReset($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Script
     */
    public static function script(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Script
    {
        return new HTML_QuickForm2_Element_Script($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_Hierselect
     */
    public static function hierselect(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_Hierselect
    {
        return new HTML_QuickForm2_Element_Hierselect($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Container_Repeat
     */
    public static function repeat(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Container_Repeat
    {
        return new HTML_QuickForm2_Container_Repeat($name, $attributes, $data);
    }

    /**
     * @param string|null $name
     * @param array<string,string|int|float|Stringable|NULL>|string|null $attributes
     * @param array<string,mixed> $data
     * @return HTML_QuickForm2_Element_InputButton
     */
    public static function inputButton(?string $name=null, $attributes=null, array $data=array()) : HTML_QuickForm2_Element_InputButton
    {
        return new HTML_QuickForm2_Element_InputButton($name, $attributes, $data);
    }
}
