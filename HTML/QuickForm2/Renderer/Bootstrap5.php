<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see HTML_QuickForm2_Renderer_Bootstrap3
 */

declare(strict_types=1);

use HTML\QuickForm2\Interfaces\ButtonElementInterface;
use HTML\QuickForm2\Renderer\Bootstrap5\ElementTemplates;

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

    private array $submits = array();

    public function getID(): string
    {
        return self::RENDERER_ID;
    }

    protected function init(): void
    {
        $this->setTemplateForClass(
            HTML_QuickForm2_Element::class,
            ElementTemplates::TEMPLATE_BASE_ELEMENT
        );

        $this->setTemplateForClass(
            HTML_QuickForm2_Element_InputCheckable::class,
            ElementTemplates::TEMPLATE_CHECKABLE
        );

        $this->setTemplateForClass(
            HTML_QuickForm2_Element_Date::class,
            ElementTemplates::TEMPLATE_DATE
        );

        $this->setTemplateForClass(
            HTML_QuickForm2_Element_Button::class,
            '<div class="mb-3">{element}</div>'
        );

        $this->setTemplateForClass(
            HTML_QuickForm2::class,
            ElementTemplates::TEMPLATE_QUICKFORM
        );
    }

    public function renderCDNIncludes(): string
    {
        return ElementTemplates::TEMPLATE_CDN_INCLUDES;
    }

    public function renderElement(HTML_QuickForm2_Node $element): void
    {
        // Buttons do not get a special class.
        if ($element instanceof ButtonElementInterface) {
            $element->addClass('btn');

            // Add a default button class if none has been specified.
            if (stripos($element->getClass(), 'btn-') === false) {
                $element->addClass('btn-secondary');
            }

            if ($element->isSubmit()) {
                $this->submits[] = (string)$element;
                return;
            }
        } elseif ($element instanceof HTML_QuickForm2_Element_InputCheckable) {
            $element->addClass('form-check-input');
        } else {
            $element->addClass('form-control');
        }

        parent::renderElement($element);
    }

    public function startGroup(HTML_QuickForm2_Container_Group $group): void
    {
        if ($group instanceof HTML_QuickForm2_Element_Date) {
            $group->addClass('input-group');
        }

        parent::startGroup($group);
    }

    protected function resolveFormPlaceholders(HTML_QuickForm2_Node $form): array
    {
        $placeholders = parent::resolveFormPlaceholders($form);

        $placeholders['{submits}'] = $this->renderSubmits();

        return $placeholders;
    }

    protected function renderSubmits(): string
    {
        if (empty($this->submits)) {
            return '';
        }

        return '<div class="form-submits">{submits}' . implode(' ', $this->submits) . '</div>';
    }
}
