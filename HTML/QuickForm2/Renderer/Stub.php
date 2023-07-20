<?php
/**
 * @category HTML
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see HTML_QuickForm2_Renderer_Stub
 */

declare(strict_types=1);

/**
 * A stub renderer to use with HTML_QuickForm2 when actual form output is
 * done manually.
 *
 * The rendering step is mandatory if the form uses client-side validation
 * or contains Javascript-backed elements. Using Array or Default renderer
 * will add unnecessary overhead if such a form will later be output by
 * e.g. echoing form elements in PHP-based template.
 *
 * This renderer does almost no form processing, serving as a container for
 * JavascriptBuilder instance. The only processing it does is grouping errors
 * and hidden elements if the relevant options are set to true:
 *
 * - {@see HTML_QuickForm2_Renderer::OPTION_GROUP_ERRORS}
 * - {@see HTML_QuickForm2_Renderer::OPTION_GROUP_HIDDENS}
 *
 * It also checks whether the form contains required elements (and thus
 * needs a required note).
 *
 * While almost everything in this class is defined as public, its properties
 * and those methods that are not published (i.e. not in array returned by
 * {@see self::exportMethods()}) will be available to renderer plugins only.
 *
 * The following methods are published:
 *
 *   - {@see self::getErrors()}
 *   - {@see self::getHidden()}
 *   - {@see self::hasRequired()}
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Alexey Borzov <avb@php.net>
 * @author Bertrand Mansion <golgote@mamasam.com>
 *@category HTML
 */
class HTML_QuickForm2_Renderer_Stub extends HTML_QuickForm2_Renderer
{
    public const RENDERER_ID = 'stub';

   /**
    * Form errors if {@see HTML_QuickForm2_Renderer::OPTION_GROUP_ERRORS} option is true.
    * @var string[]
    */
    public array $errors = array();

   /**
    * Hidden elements if {@see HTML_QuickForm2_Renderer::OPTION_GROUP_HIDDENS} option is true
    * @var string[]
    */
    public array $hidden = array();

   /**
    * Whether the form contains required elements
    * @var bool
    */
    public bool $required = false;

    public function getID() : string
    {
        return self::RENDERER_ID;
    }

    protected function exportMethods() : array
    {
        return array(
            array(self::class, 'getErrors')[1],
            array(self::class, 'getHidden')[1],
            array(self::class, 'hasRequired')[1]
        );
    }

   /**
    * Returns validation errors if {@see HTML_QuickForm2_Renderer::OPTION_GROUP_ERRORS} option is true.
    *
    * @return string[]
    */
    public function getErrors() : array
    {
        return $this->errors;
    }

   /**
    * Returns hidden elements' HTML if {@see HTML_QuickForm2_Renderer::OPTION_GROUP_HIDDENS} option is true.
    *
    * @return string[]
    */
    public function getHidden() : array
    {
        return $this->hidden;
    }

   /**
    * Checks whether form contains required elements
    *
    * @return bool
    */
    public function hasRequired() : bool
    {
        return $this->required;
    }

    /**
     * @return $this
     */
    public function reset() : self
    {
        $this->errors = array();
        $this->hidden = array();
        $this->required = false;

        return $this;
    }

    /**
     * Renders a generic element
     *
     * @param HTML_QuickForm2_Node $element Element being rendered
     */
    public function renderElement(HTML_QuickForm2_Node $element): void
    {
        if ($element->isRequired()) {
            $this->required = true;
        }

        if ($this->isGroupErrorsEnabled() && ($error = $element->getError())) {
            $this->errors[$element->getId()] = $error;
        }
    }

    /**
     * Renders a hidden element
     *
     * @param HTML_QuickForm2_Node $element Hidden element being rendered
     */
    public function renderHidden(HTML_QuickForm2_Node $element): void
    {
        if ($this->isGroupHiddensEnabled()) {
            $this->hidden[] = (string)$element;
        }
    }

    /**
     * Starts rendering a form, called before processing contained elements
     *
     * @param HTML_QuickForm2_Node $form Form being rendered
     */
    public function startForm(HTML_QuickForm2_Node $form): void
    {
        $this->reset();
    }

    /**
     * Finishes rendering a form, called after processing contained elements
     *
     * @param HTML_QuickForm2_Node $form Form being rendered
     */
    public function finishForm(HTML_QuickForm2_Node $form): void
    {
        $this->renderElement($form);
    }

    /**
     * Starts rendering a generic container, called before processing contained elements
     *
     * @param HTML_QuickForm2_Node $container Container being rendered
     */
    public function startContainer(HTML_QuickForm2_Node $container): void
    {
    }

    /**
     * Finishes rendering a generic container, called after processing contained elements
     *
     * @param HTML_QuickForm2_Node $container Container being rendered
     */
    public function finishContainer(HTML_QuickForm2_Node $container): void
    {
        $this->renderElement($container);
    }

    /**
     * Starts rendering a group, called before processing grouped elements
     *
     * @param HTML_QuickForm2_Container_Group $group Group being rendered
     */
    public function startGroup(HTML_QuickForm2_Container_Group $group) : void
    {
    }

    /**
     * Finishes rendering a group, called after processing grouped elements
     *
     * @param HTML_QuickForm2_Container_Group $group Group being rendered
     */
    public function finishGroup(HTML_QuickForm2_Container_Group $group) : void
    {
        $this->renderElement($group);
    }
}
