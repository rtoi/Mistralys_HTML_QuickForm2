<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see HTML_QuickForm2_Renderer_Callback
 * @category HTML
 */

declare(strict_types=1);

/**
 * Callback renderer for QuickForm2
 *
 * This renderer uses PHP callbacks to render form elements
 *
 * While almost everything in this class is defined as public, its properties
 * and those methods that are not published (i.e. not in array returned by
 * exportMethods()) will be available to renderer plugins only.
 *
 * The following methods are published:
 *   - {@link setCallbackForClass()}
 *   - {@link setCallbackForId()}
 *   - {@link setErrorGroupCallback()}
 *   - {@link setElementCallbackForGroupClass()}
 *   - {@link setElementCallbackForGroupId()}
 *   - {@link setHiddenGroupCallback()}
 *   - {@link setRequiredNoteCallback()}
 *   - {@link setLabelCallback()}
 *
 * Using a callback to render a Submit button and a Cancel link:
 *
 * <code>
 * function renderSubmitCancel($renderer, $submit) {
 *   $data = $submit->getData();
 *   $url = !empty($data['cancel']) ? $data['cancel'] : '/';
 *   return '<div>'.$submit.' or <a href="'.$url.'">Cancel</a></div>';
 * }
 * $renderer = HTML_QuickForm2_Renderer::createCallback();
 * $renderer->setCallbackForId($submit->getId(), 'renderSubmitCancel');
 * </code>
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Alexey Borzov <avb@php.net>
 * @author Bertrand Mansion <golgote@mamasam.com>
 * @category HTML
 */
class HTML_QuickForm2_Renderer_Callback extends HTML_QuickForm2_Renderer
{
    public const RENDERER_ID = 'callback';

    /**
     * Whether the form contains required elements
     * @var  bool
     */
    public bool $hasRequired = false;

    /**
     * HTML generated for the form
     * @var  array
     */
    public array $html = array(array());

    /**
     * HTML for hidden elements if 'group_hiddens' option is on
     * @var  string
     */
    public string $hiddenHtml = '';

    /**
     * HTML for hidden elements if 'group_hiddens' option is on
     * @var  HTML_QuickForm2_Node[]
     */
    public array $hidden = array();

    /**
     * Array of validation errors if 'group_errors' option is on
     * @var string[]
     */
    public array $errors = array();

    /**
     * Callback used to render errors if 'group_errors' is on
     * @var  mixed
     */
    public $errorGroupCallback = array(HTML_QuickForm2_Renderer_Callback::class, '_renderErrorsGroup');

    /**
     * Callback used to render hidden elements
     * @var  mixed
     */
    public $hiddenGroupCallback = array(HTML_QuickForm2_Renderer_Callback::class, '_renderHiddenGroup');

    /**
     * Callback used to render required note
     * @var  mixed
     */
    public $requiredNoteCallback = array(HTML_QuickForm2_Renderer_Callback::class, '_renderRequiredNote');

    /**
     * Callback used to render labels
     * @var  mixed
     */
    public $labelCallback = array(HTML_QuickForm2_Renderer_Callback::class, '_renderLabel');

    /**
     * Array of callbacks defined using an element or container ID
     * @var array<string,callable|NULL>
     */
    public array $callbacksForId = array();

    /**
     * Array of callbacks defined using an element class
     * @var array<string,callable|NULL>
     */
    public array $callbacksForClass = array(
        'html_quickform2' => array(self::class, '_renderForm'),
        'html_quickform2_element' => array(self::class, '_renderElement'),
        'html_quickform2_element_inputhidden' => array(self::class, '_renderHidden'),
        'html_quickform2_container' => array(self::class, '_renderContainer'),
        'html_quickform2_container_group' => array(self::class, '_renderGroup'),
        'html_quickform2_container_fieldset' => array(self::class, '_renderFieldset'),
        'html_quickform2_container_repeat' => array(self::class, '_renderRepeat')
    );

    /**
     * Array of callbacks defined using a group ID
     * @var array<string,array<string,callable|NULL>>
     */
    public array $elementCallbacksForGroupId = array();

    /**
     * Array of callbacks defined using a group class
     * @var array<string,array<string,callable|NULL>>
     */
    public array $elementCallbacksForGroupClass = array(
        'html_quickform2_container' => array(
            'html_quickform2_element' => array(HTML_QuickForm2_Renderer_Callback::class, '_renderGroupedElement')
        )
    );

    /**
     * Array containing IDs of the groups being rendered
     * @var array<int,string|false|NULL>
     */
    public array $groupId = array();

    public function getID() : string
    {
        return self::RENDERER_ID;
    }

    protected function exportMethods() : array
    {
        return array(
            array(self::class, 'setCallbackForClass')[1],
            array(self::class, 'setCallbackForId')[1],
            array(self::class, 'setErrorGroupCallback')[1],
            array(self::class, 'setElementCallbackForGroupClass')[1],
            array(self::class, 'setElementCallbackForGroupId')[1],
            array(self::class, 'setHiddenGroupCallback')[1],
            array(self::class, 'setRequiredNoteCallback')[1],
            array(self::class, 'setLabelCallback')[1]
        );
    }

    public static function _renderForm(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2                   $form
    ) : string
    {
        $break = BaseHTMLElement::getOption('linebreak');
        $html[] = '<div class="quickform">' .
            call_user_func($renderer->errorGroupCallback, $renderer, $form) .
            '<form' . $form->getAttributes(true) . '><div>' .
            call_user_func($renderer->hiddenGroupCallback, $renderer, $form);
        $html[] = implode($break, array_pop($renderer->html));
        $html[] = '</div></form>';
        $html[] = call_user_func($renderer->requiredNoteCallback, $renderer, $form);
        $script = $renderer->getJavascriptBuilder()->getFormJavascript($form->getId());
        if (!empty($script))
        {
            $html[] = $script;
        }
        $html[] = '</div>';
        return implode($break, $html) . $break;
    }

    public static function _renderElement(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2_Element           $element
    ) : string
    {
        $html[] = '<div class="row">';
        $html[] = $renderer->renderLabel($element);
        $error = $element->getError();
        if ($error)
        {
            $html[] = '<div class="element error">';
            if ($renderer->isGroupErrorsEnabled())
            {
                $renderer->errors[] = $error;
            }
            else
            {
                $html[] = '<span class="error">' . $error . '</span><br />';
            }
        }
        else
        {
            $html[] = '<div class="element">';
        }
        $html[] = $element->__toString();
        $html[] = '</div>';
        $html[] = '</div>';
        return implode("", $html);
    }

    public static function _renderGroupedElement(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2_Element           $element
    ) : string
    {
        return (string)$element;
    }

    public static function _renderErrorsGroup(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2                   $form
    ) : string
    {
        if (empty($renderer->errors))
        {
            return '';
        }

        $html = array();
        $prefix = $renderer->getErrorsPrefix();
        $suffix = $renderer->getErrorsSuffix();

        $html[] = '<div class="errors">';
        if (!empty($prefix))
        {
            $html[] = '<p>' . $prefix . '</p>';
        }
        $html[] = '<ul>';
        foreach ($renderer->errors as $error)
        {
            $html[] = '<li>' . $error . '</li>';
        }
        $html[] = '</ul>';

        if (!empty($suffix))
        {
            $html[] = '<p>' . $suffix . '</p>';
        }
        $html[] = '</div>';

        return implode("", $html);
    }

    public static function _renderHidden(
        HTML_QuickForm2_Renderer_Callback   $renderer,
        HTML_QuickForm2_Element_InputHidden $hidden
    ) : string
    {
        return '<div style="display: none;">' . $hidden->__toString() . '</div>';
    }

    public static function _renderHiddenGroup(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2                   $form
    ) : string
    {
        if (empty($renderer->hidden))
        {
            return '';
        }
        $html = array();
        foreach ($renderer->hidden as $hidden)
        {
            $html[] = $hidden->__toString();
        }
        return '<div style="display: none;">' . implode('', $html) . '</div>';
    }

    public static function _renderRequiredNote(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2                   $form
    ) : string
    {
        if ($renderer->hasRequired && !$form->isFrozen())
        {
            $note = $renderer->getRequiredNote();

            if (!empty($note))
            {
                return '<div class="reqnote">' . $note . '</div>';
            }
        }

        return '';
    }

    public static function _renderContainer(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2_Container         $container
    ) : string
    {
        $break = BaseHTMLElement::getOption('linebreak');
        return implode($break, array_pop($renderer->html));
    }

    public static function _renderGroup(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2_Container_Group   $group
    ) : string
    {
        $break = BaseHTMLElement::getOption('linebreak');
        $class = $group->getAttribute('class');
        $html[] = '<div class="row' . (!empty($class) ? ' ' . $class : '') . '">';
        $html[] = $renderer->renderLabel($group);
        $error = $group->getError();
        if ($error)
        {
            $html[] = '<div class="element group error" id="' . $group->getId() . '">';
            if ($renderer->isGroupErrorsEnabled())
            {
                $renderer->errors[] = $error;
            }
            else
            {
                $html[] = '<span class="error">' . $error . '</span><br />';
            }
        }
        else
        {
            $html[] = '<div class="element group" id="' . $group->getId() . '">';
        }

        $content = self::renderElementsWithSeparator($group->getSeparator(), array_pop($renderer->html));

        $html[] = $content;
        $html[] = '</div>';
        $html[] = '</div>';
        return implode($break, $html) . $break;
    }

    public static function _renderRepeat(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2_Container_Repeat  $repeat
    ) : string
    {
        $break = BaseHTMLElement::getOption('linebreak');
        $html[] = '<div class="row repeat" id="' . $repeat->getId() . '">';
        $label = $repeat->getLabel();
        if (!is_array($label))
        {
            $label = array($label);
        }
        if (!empty($label[0]))
        {
            $html[] = '<p>' . array_shift($label) . '</p>';
        }
        $elements = array_pop($renderer->html);
        $content = implode($break, $elements);
        $html[] = $content;
        $html[] = '</div>';
        return implode($break, $html) . $break;
    }

    public static function _renderFieldset(
        HTML_QuickForm2_Renderer_Callback  $renderer,
        HTML_QuickForm2_Container_Fieldset $fieldset
    ) : string
    {
        $break = BaseHTMLElement::getOption('linebreak');
        $html[] = '<fieldset' . $fieldset->getAttributes(true) . '>';
        $label = $fieldset->getLabel();
        if (!empty($label))
        {
            $html[] = sprintf(
                '<legend id="%s-legend">%s</legend>',
                $fieldset->getId(), $label
            );
        }
        $elements = array_pop($renderer->html);
        $html[] = implode($break, $elements);
        $html[] = '</fieldset>';
        return implode($break, $html) . $break;
    }

    public static function _renderLabel(
        HTML_QuickForm2_Renderer_Callback $renderer,
        HTML_QuickForm2_Node              $node
    ) : string
    {
        $html = array();
        $label = $node->getLabel();
        if (!is_array($label))
        {
            $label = array($label);
        }
        if ($node->isRequired())
        {
            $renderer->hasRequired = true;
        }
        $html[] = '<p class="label">';
        if (!empty($label[0]))
        {
            if ($node->isRequired())
            {
                $html[] = '<span class="required">*</span>';
            }
            if ($node instanceof HTML_QuickForm2_Container)
            {
                $html[] = '<label>';
            }
            else
            {
                $html[] = '<label for="' . $node->getId() . '">';
            }
            $html[] = array_shift($label);
            $html[] = '</label>';
        }
        $html[] = '</p>';
        return implode('', $html);
    }

    /**
     * Renders a generic element
     *
     * @param HTML_QuickForm2_Node $element Element being rendered
     */
    public function renderElement(HTML_QuickForm2_Node $element) : void
    {
        $default = $this->callbacksForClass['html_quickform2_element'];
        $callback = $this->findCallback($element, $default);
        $res = $callback($this, $element);
        $this->html[count($this->html) - 1][] = $res;
    }

    /**
     * Renders an element label
     *
     * @param HTML_QuickForm2_Node $element Element being rendered
     *
     * @return string
     */
    public function renderLabel(HTML_QuickForm2_Node $element) : string
    {
        return (string)call_user_func($this->labelCallback, $this, $element);
    }

    /**
     * Renders a hidden element
     *
     * @param HTML_QuickForm2_Node $element Hidden element being rendered
     */
    public function renderHidden(HTML_QuickForm2_Node $element) : void
    {
        if ($this->isGroupHiddensEnabled())
        {
            $this->hidden[] = $element;
            return;
        }

        $default = $this->callbacksForClass['html_quickform2_element_inputhidden'];
        $callback = $this->findCallback($element, $default);
        $this->html[count($this->html) - 1][] = $callback($this, $element);
    }

    /**
     * Starts rendering a generic container, called before processing contained elements
     *
     * @param HTML_QuickForm2_Node $container Container being rendered
     */
    public function startContainer(HTML_QuickForm2_Node $container) : void
    {
        $this->html[] = array();
        $this->groupId[] = false;
    }

    /**
     * Finishes rendering a generic container, called after processing contained elements
     *
     * @param HTML_QuickForm2_Node $container Container being rendered
     */
    public function finishContainer(HTML_QuickForm2_Node $container) : void
    {
        array_pop($this->groupId);
        $default = $this->callbacksForClass['html_quickform2_container'];
        $callback = $this->findCallback($container, $default);
        $res = $callback($this, $container);
        $this->html[count($this->html) - 1][] = $res;
    }

    /**
     * Starts rendering a group, called before processing grouped elements
     *
     * @param HTML_QuickForm2_Container_Group $group Group being rendered
     */
    public function startGroup(HTML_QuickForm2_Container_Group $group) : void
    {
        $this->html[] = array();
        $this->groupId[] = $group->getId();
    }

    /**
     * Finishes rendering a group, called after processing grouped elements
     *
     * @param HTML_QuickForm2_Container_Group $group Group being rendered
     */
    public function finishGroup(HTML_QuickForm2_Container_Group $group) : void
    {
        array_pop($this->groupId);
        $default = $this->callbacksForClass['html_quickform2_container_group'];
        $callback = $this->findCallback($group, $default);
        $res = $callback($this, $group);
        $this->html[count($this->html) - 1][] = $res;
    }

    /**
     * Starts rendering a form, called before processing contained elements
     *
     * @param HTML_QuickForm2_Node $form Form being rendered
     */
    public function startForm(HTML_QuickForm2_Node $form) : void
    {
        $this->reset();
    }

    /**
     * Finishes rendering a form, called after processing contained elements
     *
     * @param HTML_QuickForm2_Node $form Form being rendered
     */
    public function finishForm(HTML_QuickForm2_Node $form) : void
    {
        $default = $this->callbacksForClass['html_quickform2'];
        $callback = $this->findCallback($form, $default);
        $this->html[0] = array(
            $callback($this, $form)
        );
    }

    /**
     * Sets callback for rendering labels
     *
     * @param callable|null $callback PHP callback
     *
     * @return $this
     */
    public function setLabelCallback(?callable $callback) : self
    {
        $this->labelCallback = $callback;
        return $this;
    }

    /**
     * Sets callback for rendering hidden elements if option group_hiddens is true
     *
     * @param callable|null $callback PHP callback
     *
     * @return $this
     */
    public function setHiddenGroupCallback(?callable $callback) : self
    {
        $this->hiddenGroupCallback = $callback;
        return $this;
    }

    /**
     * Sets callback for rendering required note
     *
     * @param callable|null $callback PHP callback
     *
     * @return $this
     */
    public function setRequiredNoteCallback(?callable $callback) : self
    {
        $this->requiredNoteCallback = $callback;
        return $this;
    }

    /**
     * Sets callback for form elements that are instances of the given class
     *
     * When searching for a callback to use, renderer will check for callbacks
     * set for element's class and its parent classes, until found. Thus, a more
     * specific callbacks will override a more generic one.
     *
     * @param class-string $className Class name
     * @param callable|null $callback Callback to use for elements of that class
     *
     * @return $this
     */
    public function setCallbackForClass(string $className, ?callable $callback) : self
    {
        $this->callbacksForClass[strtolower($className)] = $callback;
        return $this;
    }

    /**
     * Sets callback for form element with the given id
     *
     * If a callback is set for an element via this method, it will be used.
     * In the other case a generic callback set by {@link setCallbackForClass()}
     * or {@link setElementCallbackForGroupClass()} will be used.
     *
     * @param string $id Element's id
     * @param callable|null $callback Callback to use for rendering of that element
     *
     * @return $this
     */
    public function setCallbackForId(string $id, ?callable $callback) : self
    {
        $this->callbacksForId[$id] = $callback;
        return $this;
    }

    /**
     * Sets callback for rendering validation errors
     *
     * This callback will be used if 'group_errors' option is set to true.
     *
     * @param callable|null $callback Callback for validation errors
     *
     * @return $this
     */
    public function setErrorGroupCallback(?callable $callback) : self
    {
        $this->errorGroupCallback = $callback;
        return $this;
    }

    /**
     * Sets grouped elements callbacks using group class
     *
     * Callbacks set via {@link setCallbackForClass()} will not be used for
     * grouped form elements. When searching for a callback to use, the renderer
     * will first consider callback set for a specific group id, then the
     * group callback set by group class.
     *
     * @param class-string $groupClass Group class name
     * @param class-string $elementClass Element class name
     * @param callable|null $callback Callback
     *
     * @return $this
     */
    public function setElementCallbackForGroupClass(string $groupClass, string $elementClass, ?callable $callback) : self
    {
        $this->elementCallbacksForGroupClass[strtolower($groupClass)][strtolower($elementClass)] = $callback;
        return $this;
    }

    /**
     * Sets grouped elements callback using group id
     *
     * Callbacks set via {@link setCallbackForClass()} will not be used for
     * grouped form elements. When searching for a callback to use, the renderer
     * will first consider callback set for a specific group id, then the
     * group callbacks set by group class.
     *
     * @param string $groupId Group id
     * @param string $elementClass Element class name
     * @param callable|null $callback Callback
     *
     * @return $this
     */
    public function setElementCallbackForGroupId(string $groupId, string $elementClass, ?callable $callback) : self
    {
        $this->elementCallbacksForGroupId[$groupId][strtolower($elementClass)] = $callback;
        return $this;
    }

    /**
     * Resets the accumulated data
     *
     * This method is called automatically by startForm() method, but should
     * be called manually before calling other rendering methods separately.
     *
     * @return $this
     */
    public function reset() : self
    {
        $this->html = array(array());
        $this->hiddenHtml = '';
        $this->errors = array();
        $this->hidden = array();
        $this->hasRequired = false;
        $this->groupId = array();

        return $this;
    }

    /**
     * Returns generated HTML
     *
     * @return string
     */
    public function __toString() : string
    {
        return (isset($this->html[0][0]) ? (string)$this->html[0][0] : '');
    }

    /**
     * Finds a proper callback for the element
     *
     * Callbacks are scanned in a predefined order. First, if a callback was
     * set for a specific element by id, it is returned, no matter if the
     * element belongs to a group. If the element does not belong to a group,
     * we try to match a callback using the element class.
     * But, if the element belongs to a group, callbacks are first looked up
     * using the containing group id, then using the containing group class.
     * When no callback is found, the provided default callback is returned.
     *
     * @param HTML_QuickForm2_Node $element Element being rendered
     * @param callable|null $default Default callback to use if not found
     *
     * @return callable|null
     */
    public function findCallback(HTML_QuickForm2_Node $element, ?callable $default = null) : ?callable
    {
        if (!empty($this->callbacksForId[$element->getId()]))
        {
            return $this->callbacksForId[$element->getId()];
        }

        $class = strtolower(get_class($element));
        $groupId = end($this->groupId);
        $elementClasses = array();

        do
        {
            if (empty($groupId) && !empty($this->callbacksForClass[$class]))
            {
                return $this->callbacksForClass[$class];
            }
            $elementClasses[$class] = true;
        }
        while ($class = strtolower((string)get_parent_class($class)));

        if (!empty($groupId))
        {
            if (!empty($this->elementCallbacksForGroupId[$groupId]))
            {
                foreach (array_keys($elementClasses) as $elClass)
                {
                    if (!empty($this->elementCallbacksForGroupId[$groupId][$elClass]))
                    {
                        return $this->elementCallbacksForGroupId[$groupId][$elClass];
                    }
                }
            }

            $group = $element->getContainer();

            if ($group !== null)
            {
                $grClass = strtolower(get_class($group));
                do
                {
                    if (!empty($this->elementCallbacksForGroupClass[$grClass]))
                    {
                        foreach (array_keys($elementClasses) as $elClass)
                        {
                            if (!empty($this->elementCallbacksForGroupClass[$grClass][$elClass]))
                            {
                                return $this->elementCallbacksForGroupClass[$grClass][$elClass];
                            }
                        }
                    }
                }
                while ($grClass = strtolower(get_parent_class($grClass)));
            }
        }

        return $default;
    }
}
