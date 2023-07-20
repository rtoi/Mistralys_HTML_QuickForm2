<?php
/**
 * Unit tests for HTML_QuickForm2 package
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

use PHPUnit\Framework\TestCase;

/**
 * Unit test for HTML_QuickForm2_Renderer_Callback class
 */
class HTML_QuickForm2_Renderer_CallbackTest extends TestCase
{
    public static function _renderInputText($renderer, $element)
    {
        return 'InputText;id='.$element->getId().',html='.$element;
    }

    public static function _renderInput($renderer, $element)
    {
        return 'Input;id='.$element->getId().',html='.$element;
    }

    public static function _renderTestRenderElement($renderer, $element)
    {
        return 'testRenderElement;id='.$element->getId().',html='.$element;
    }

    public static function _renderTestRenderRequiredElement($renderer, $element)
    {
        if ($element->isRequired()) {
            return 'required!'.$element.'<em>*</em>';
        } else {
            return (string)$element;
        }
    }

    public static function _renderTestElementWithError(HTML_QuickForm2_Renderer $renderer, $element) : string
    {
        if (($error = $element->getError()) && $error &&
            !$renderer->isGroupErrorsEnabled()) {
            return 'an error!'.$element.$error;
        }

        return (string)$element;
    }

    public static function _renderTestSingleLabel($renderer, $element)
    {
        if (($label = $element->getLabel()) && !empty($label)) {
            return 'A label: '.$element.$element->getLabel();
        }
        return (string)$element;
    }

    public static function _renderTestMultipleLabels($renderer, $element)
    {
        if (($label = $element->getLabel()) && !empty($label)) {
            $html = "";
            if (!empty($label[0])) {
                $html .= "First label: ".$label[0].$element;
            }
            if (!empty($label[1])) {
                $html .= "Second label: ".$label[1];
            }
            if (!empty($label['foo'])) {
                $html .= "Named label: ".$label['foo'];
            }
            return $html;
        }
        return (string)$element;
    }

    public function testRenderElementUsingMostAppropriateCallback(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testRenderElement')
        );

        $class = get_class($this);
        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForClass(
                'HTML_QuickForm2_Element_InputText', array($class, '_renderInputText')
            )->setCallbackForClass(
                'HTML_QuickForm2_Element_Input', array($class, '_renderInput')
            )->setCallbackForId(
                'testRenderElement', array($class, '_renderTestRenderElement')
            );

        $this->assertEquals(
            'testRenderElement;id=' . $element->getId() . ',html=' . $element->__toString(),
            (string)$element->render($renderer->reset())
        );

        $renderer->setCallbackForId('testRenderElement', null);
        $this->assertEquals(
            'InputText;id=' . $element->getId() . ',html=' . $element->__toString(),
            (string)$element->render($renderer->reset())
        );

        $renderer->setCallbackForClass('HTML_QuickForm2_Element_InputText', null);
        $this->assertEquals(
            'Input;id=' . $element->getId() . ',html=' . $element->__toString(),
            (string)$element->render($renderer->reset())
        );
    }

    public function testRenderRequiredElement(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testRenderRequiredElement')
        );

        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForId(
                'testRenderRequiredElement',
                array(get_class($this), '_renderTestRenderRequiredElement')
            );
        $this->assertEquals(
            (string)$element,
            (string)$element->render($renderer->reset())
        );

        $element->addRule('required', 'error message');
        $this->assertEquals(
            'required!' . $element->__toString() . '<em>*</em>',
            (string)$element->render($renderer->reset())
        );
    }

    public function testRenderElementWithValidationError(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testElementWithError')
        );
        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForId(
                'testElementWithError',
                array(get_class($this), '_renderTestElementWithError')
            );

        $this->assertEquals(
            (string)$element,
            (string)$element->render($renderer->reset())
        );

        $element->setError('some message');
        $this->assertEquals(
            'an error!' . $element . 'some message',
            (string)$element->render(
                $renderer->reset()->setGroupErrors(false)
            )
        );

        $this->assertEquals(
            $element->__toString(),
            $element->render(
                $renderer->reset()->setGroupErrors(true)
            )->__toString()
        );
    }

    public function testRenderElementWithSingleLabel(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testSingleLabel')
        );
        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForId(
                'testSingleLabel',
                array(get_class($this), '_renderTestSingleLabel')
            );

        $this->assertEquals(
            (string)$element,
            (string)$element->render($renderer->reset())
        );

        $element->setLabel('the label!');
        $this->assertEquals(
            'A label: ' . $element . 'the label!',
            (string)$element->render($renderer->reset())
        );
    }

    public function testRenderElementWithMultipleLabels(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testMultipleLabels')
        )->setLabel(array('first', 'second'));
        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForId(
                'testMultipleLabels',
                array($this, '_renderTestMultipleLabels')
            );

        $this->assertEquals(
            'First label: first' . $element . 'Second label: second',
            (string)$element->render($renderer->reset())
        );

        $element->setLabel(array('another', 'foo' => 'foo'));
        $this->assertEquals(
            'First label: another' . $element . 'Named label: foo',
            (string)$element->render($renderer->reset())
        );
    }

    public function testRenderRequiredNote(): void
    {
        $form = new HTML_QuickForm2('reqnote');
        $element = $form->addText('testReqnote');

        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setRequiredNote('This is requi-i-i-ired!');

        $this->assertStringNotContainsString('<div class="reqnote">', (string)$form->render($renderer));

        $element->addRule('required', 'error message');
        $this->assertStringContainsString('<div class="reqnote">This is requi-i-i-ired!</div>', (string)$form->render($renderer));
    }

    public function testRenderGroupedErrors(): void
    {
        $form = new HTML_QuickForm2('groupedErrors');

        $form->addText('testGroupedErrors')->setError('Some error');

        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setGroupErrors(true)
            ->setErrorsPrefix('Your errors:')
            ->setErrorsSuffix('');

        $this->assertStringContainsString(
            '<div class="errors"><p>Your errors:</p><ul><li>Some error</li></ul></div>',
            (string)$form->render($renderer)
        );
    }

    public function testRenderGroupedHiddens(): void
    {
        $form     = new HTML_QuickForm2('groupedHiddens');
        $hidden1  = $form->addHidden('hidden1');
        $hidden2  = $form->addHidden('hidden2');
        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setGroupHiddens(false);

        $html = (string)$form->render($renderer);
        $this->assertStringContainsString('<div style="display: none;">' . $hidden1->__toString() . '</div>', $html);
        $this->assertStringContainsString('<div style="display: none;">' . $hidden2->__toString() . '</div>', $html);

        $renderer->setGroupHiddens(true);
        $html = (string)$form->render($renderer);

        // why not ?
        // $this->assertStringNotContainsString('<div style="display: none;">', $html);

        $this->assertStringContainsString($hidden1 . $hidden2, $html);
    }

    public static function _renderGroupInputText($renderer, $element)
    {
        return 'IgnoreThis;html='.$element;
    }

    public static function _renderGroupInput($renderer, $element)
    {
        return 'GroupedInput;id='.$element->getId().',html='.$element;
    }

    public static function _renderGroup($renderer, $element)
    {
        return 'GroupedElement;id='.$element->getId().',html='.$element;
    }

    public static function _renderGroupedElement($renderer, $element)
    {
        return 'testRenderGroupedElement;id='.$element->getId().',html='.$element;
    }

    public function testRenderGroupedElementUsingMostAppropriateTemplate(): void
    {
        $group   = HTML_QuickForm2_Factory::createElement('group', 'foo', array('id' => 'testRenderGroup'));
        $element = $group->addElement('text', 'bar', array('id' => 'testRenderGroupedElement'));

        $class= get_class($this);
        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForClass(
                'HTML_QuickForm2_Element_InputText',
                array($class, '_renderGroupInputText')
            )->setElementCallbackForGroupClass(
                'HTML_QuickForm2_Container_Group', 'HTML_QuickForm2_Element_Input',
                array($class, '_renderGroupInput')
            )->setElementCallbackForGroupId(
                'testRenderGroup', 'HTML_QuickForm2_Element',
                array($class, '_renderGroup')
            )->setCallbackForId(
                'testRenderGroupedElement',
                array($class, '_renderGroupedElement')
            );

        $this->assertStringContainsString(
            'testRenderGroupedElement;id=' . $element->getId() . ',html=' . $element,
            (string)$group->render($renderer->reset())
        );

        $renderer->setCallbackForId('testRenderGroupedElement', null);
        $this->assertStringContainsString(
            'GroupedElement;id=' . $element->getId() . ',html=' . $element,
            (string)$group->render($renderer->reset())
        );

        $renderer->setElementCallbackForGroupId('testRenderGroup', 'HTML_QuickForm2_Element', null);
        $this->assertStringContainsString(
            'GroupedInput;id=' . $element->getId() . ',html=' . $element,
            (string)$group->render($renderer->reset())
        );

        $renderer->setElementCallbackForGroupClass('HTML_QuickForm2_Container_Group', 'HTML_QuickForm2_Element_Input', null);
        $this->assertStringNotContainsString(
            'IgnoreThis', (string)$group->render($renderer->reset())
        );
    }

    public static function _renderTestSeparators($renderer, $group)
    {
        $separator = $group->getSeparator();
        $elements  = array_pop($renderer->html);
        if (!is_array($separator)) {
            $content = implode((string)$separator, $elements);
        } else {
            $content    = '';
            $cSeparator = count($separator);
            for ($i = 0, $count = count($elements); $i < $count; $i++) {
                $content .= (0 == $i? '': $separator[($i - 1) % $cSeparator]) .
                            $elements[$i];
            }
        }
        return $content;
    }

    public static function _renderTestSeparators2($renderer, $element)
    {
        return '<foo>'.$element.'</foo>';
    }

    public function testRenderGroupedElementsWithSeparators(): void
    {
        $group = HTML_QuickForm2_Factory::createElement('group', 'foo', array('id' => 'testSeparators'));
        $element1 = $group->addElement('text', 'bar');
        $element2 = $group->addElement('text', 'baz');
        $element3 = $group->addElement('text', 'quux');

        $renderer = HTML_Quickform2_Renderer::createCallback()
            ->setCallbackForId('testSeparators', array(get_class($this), '_renderTestSeparators'))
            ->setElementCallbackForGroupId(
                'testSeparators', 'HTML_QuickForm2_Element_InputText', array(get_class($this), '_renderTestSeparators2')
            );

        $this->assertEquals(
            '<foo>' . $element1 . '</foo><foo>' . $element2 . '</foo><foo>' . $element3 . '</foo>',
            (string)$group->render($renderer->reset())
        );

        $group->setSeparator('&nbsp;');
        $this->assertEquals(
            '<foo>' . $element1 . '</foo>&nbsp;<foo>' . $element2 . '</foo>&nbsp;<foo>' . $element3 . '</foo>',
            (string)$group->render($renderer->reset())
        );

        $group->setSeparator(array('<br />', '&nbsp;'));
        $this->assertEquals(
            '<foo>' . $element1 . '</foo><br /><foo>' . $element2 . '</foo>&nbsp;<foo>' . $element3 . '</foo>',
            (string)$group->render($renderer->reset())
        );
    }
}

