<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see HTML_QuickForm2_Renderer_Bootstrap3
 */

declare(strict_types=1);

use HTML\QuickForm2\Interfaces\ButtonElementInterface;

/**
 * Form renderer for Bootstrap 5.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @link https://getbootstrap.com/docs/5.3/forms/overview/
 */
class HTML_QuickForm2_Renderer_Bootstrap5 extends HTML_QuickForm2_Renderer_Default
{
    public const RENDERER_ID = 'bootstrap5';

    protected const TEMPLATE_BASE_ELEMENT = <<<'HTML'
<div class="mb-3">
    <qf:label>
        <label for="{id}" class="form-label">
            {label}
            <qf:required>
                <span class="required">*</span>
            </qf:required>
        </label>
    </qf:label>
    {element}
    <div class="form-text">{comment}</div>
    <qf:error>
        <span class="error">{error}<br /></span>
    </qf:error>
</div>
HTML;

    protected const TEMPLATE_CHECKABLE = <<<'HTML'
<div class="mb-3 form-check">
    {element}
    <qf:label>
        <label for="{id}" class="form-check-label">
            {label}
            <qf:required>
                <span class="required">*</span>
            </qf:required>
        </label>
    </qf:label>
    <div class="form-text">{comment}</div>
    <qf:error>
        <span class="error">{error}</span>
    </qf:error>
</div>
HTML;

    protected const TEMPLATE_DATE = <<<'HTML'
<div class="mb-3 form-group {class}" id="{id}">
    <qf:label>
        <label class="form-label">
            {label}
            <qf:required><span class="required">*</span></qf:required>
        </label>
    </qf:label>
    <div class="input-group">
        {content}
    </div>
    <div class="form-text">{comment}</div>
    <qf:error>
        <span class="error">{error}</span>
    </qf:error>
</div>
HTML;

    protected const TEMPLATE_QUICKFORM = <<<'HTML'
<div class="quickform">
    {errors}
    <form{attributes}>
        <div class="hiddens">
            {hidden}
        </div>
        {content}
        {submits}
    </form>
    <qf:reqnote>
        <div class="reqnote">{reqnote}</div>
    </qf:reqnote>
</div>
HTML;

    private array $submits = array();

    public function getID() : string
    {
        return self::RENDERER_ID;
    }

    protected function init() : void
    {
        $this->setTemplateForClass(
            HTML_QuickForm2_Element::class,
            self::TEMPLATE_BASE_ELEMENT
        );

        $this->setTemplateForClass(
            HTML_QuickForm2_Element_InputCheckable::class,
            self::TEMPLATE_CHECKABLE
        );

        $this->setTemplateForClass(
            HTML_QuickForm2_Element_Date::class,
            self::TEMPLATE_DATE
        );

        $this->setTemplateForClass(
            HTML_QuickForm2_Element_Button::class,
            '<div class="mb-3">{element}</div>'
        );

        $this->setTemplateForClass(
            HTML_QuickForm2::class,
            self::TEMPLATE_QUICKFORM
        );
    }

    public function renderCDNIncludes() : string
    {
        ob_start();
        ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <?php
        return (string)ob_get_clean();
    }

    public function renderElement(HTML_QuickForm2_Node $element) : void
    {
        // Buttons do not get a special class.
        if($element instanceof ButtonElementInterface)
        {
            $element->addClass('btn');

            // Add a default button class if none has been specified.
            if(stripos($element->getClass(), 'btn-') === false) {
                $element->addClass('btn-secondary');
            }

            if($element->isSubmit()) {
                $this->submits[] = (string)$element;
                return;
            }
        }
        elseif($element instanceof HTML_QuickForm2_Element_InputCheckable)
        {
            $element->addClass('form-check-input');
        }
        else
        {
            $element->addClass('form-control');
        }

        parent::renderElement($element);
    }

    public function startGroup(HTML_QuickForm2_Container_Group $group) : void
    {
        if($group instanceof HTML_QuickForm2_Element_Date)
        {
            $group->addClass('input-group');
        }

        parent::startGroup($group);
    }

    protected function resolveFormPlaceholders(HTML_QuickForm2_Node $form) : array
    {
        $placeholders = parent::resolveFormPlaceholders($form);

        $placeholders['{submits}'] = $this->renderSubmits();

        return $placeholders;
    }

    protected function renderSubmits() : string
    {
        if(empty($this->submits)) {
           return '';
        }

        return '<div class="form-submits">'.implode(' ', $this->submits).'</div>';
    }
}
