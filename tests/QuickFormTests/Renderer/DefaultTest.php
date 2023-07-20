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
 * Unit test for HTML_QuickForm2_Renderer_Default class
 */
class HTML_QuickForm2_Renderer_DefaultTest extends TestCase
{
    public function testRenderElementUsingMostAppropriateTemplate(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testRenderElement')
        );
        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForClass(
                'HTML_QuickForm2_Element_InputText', 'InputText;id={id},html={element}'
            )->setTemplateForClass(
                'HTML_QuickForm2_Element_Input', 'Input;id={id},html={element}'
            )->setTemplateForId(
                'testRenderElement', 'testRenderElement;id={id},html={element}'
            );

        $this->assertEquals(
            'testRenderElement;id=' . $element->getId() . ',html=' . $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $renderer->setTemplateForId('testRenderElement', null);
        $this->assertEquals(
            'InputText;id=' . $element->getId() . ',html=' . $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $renderer->setTemplateForClass('HTML_QuickForm2_Element_InputText', null);
        $this->assertEquals(
            'Input;id=' . $element->getId() . ',html=' . $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderRequiredElement(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testRenderRequiredElement')
        );

        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForId(
                'testRenderRequiredElement',
                '<qf:required>required!</qf:required>{element}<qf:required><em>*</em></qf:required>'
            );
        $this->assertEquals(
            $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $element->addRule('required', 'error message');
        $this->assertEquals(
            'required!' . $element->__toString() . '<em>*</em>',
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderElementWithValidationError(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testElementWithError')
        );
        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForId(
                'testElementWithError',
                '<qf:error>an error!</qf:error>{element}<qf:error>{error}</qf:error>'
            );

        $this->assertEquals(
            $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );

        $element->setError('some message');
        $this->assertEquals(
            'an error!' . $element->__toString() . 'some message',
            $element->render(
                $renderer->reset()->setGroupErrors(false)
            )->__toString()
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
        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForId(
                'testSingleLabel',
                '<qf:label>A label: </qf:label>{element}{label}'
            );

        $this->assertEquals(
            $element->__toString(),
            $element->render($renderer->reset())->__toString()
        );
        $element->setLabel('the label!');
        $this->assertEquals(
            'A label: ' . $element->__toString() . 'the label!',
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderElementWithMultipleLabels(): void
    {
        $element = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'testMultipleLabels')
        )->setLabel(array('first', 'second'));
        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForId(
                'testMultipleLabels',
                '<qf:label>First label: {label}</qf:label>{element}<qf:label_2>Second label: {label_2}</qf:label_2>' .
                '<qf:label_foo>Named label: {label_foo}</qf:label_foo>'
            );

        $this->assertEquals(
            'First label: first' . $element->__toString() . 'Second label: second',
            $element->render($renderer->reset())->__toString()
        );

        $element->setLabel(array('another', 'foo' => 'foo'));
        $this->assertEquals(
            'First label: another' . $element->__toString() . 'Named label: foo',
            $element->render($renderer->reset())->__toString()
        );
    }

    public function testRenderRequiredNote(): void
    {
        $form = new HTML_QuickForm2('reqnote');
        $element = $form->addText('testReqnote');

        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setRequiredNote('This is requi-i-i-ired!');

        $this->assertStringNotContainsString('<div class="reqnote">', $form->render($renderer)->__toString());

        $element->addRule('required', 'error message');
        $this->assertStringContainsString('<div class="reqnote">This is requi-i-i-ired!</div>', $form->render($renderer)->__toString());
    }

    public function testRenderGroupedErrors(): void
    {
        $form = new HTML_QuickForm2('groupedErrors');

        $form->addText('testGroupedErrors')->setError('Some error');

        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setGroupErrors(true)
            ->setErrorsPrefix('Your errors:')
            ->setErrorsSuffix('');

        $this->assertStringContainsString(
            '<div class="errors"><p>Your errors:</p><ul><li>Some error</li></ul></div>',
            $form->render($renderer)->__toString()
        );
    }

    public function testRenderGroupedHiddens(): void
    {
        $form     = new HTML_QuickForm2('groupedHiddens');
        $hidden1  = $form->addHidden('hidden1');
        $hidden2  = $form->addHidden('hidden2');
        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setGroupHiddens(false);

        $html = $form->render($renderer)->__toString();
        $this->assertStringContainsString('<div style="display: none;">' . $hidden1->__toString() . '</div>', $html);
        $this->assertStringContainsString('<div style="display: none;">' . $hidden2->__toString() . '</div>', $html);

        $renderer->setGroupHiddens(true);
        $html = $form->render($renderer)->__toString();
        $this->assertStringNotContainsString('<div style="display: none;">', $html);
        $this->assertStringContainsString($hidden1->__toString() . $hidden2->__toString(), $html);
    }

    public function testRenderGroupedElementUsingMostAppropriateTemplate(): void
    {
        $group   = HTML_QuickForm2_Factory::createElement('group', 'foo', array('id' => 'testRenderGroup'));
        $element = $group->addElement('text', 'bar', array('id' => 'testRenderGroupedElement'));

        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForClass(
                'HTML_QuickForm2_Element_InputText', 'IgnoreThis;html={element}'
            )->setElementTemplateForGroupClass(
                'HTML_QuickForm2_Container_Group', 'HTML_QuickForm2_Element_Input',
                'GroupedInput;id={id},html={element}'
            )->setElementTemplateForGroupId(
                'testRenderGroup', 'HTML_QuickForm2_Element', 'GroupedElement;id={id},html={element}'
            )->setTemplateForId(
                'testRenderGroupedElement', 'testRenderGroupedElement;id={id},html={element}'
            );

        $this->assertStringContainsString(
            'testRenderGroupedElement;id=' . $element->getId() . ',html=' . $element->__toString(),
            $group->render($renderer->reset())->__toString()
        );

        $renderer->setTemplateForId('testRenderGroupedElement', null);
        $this->assertStringContainsString(
            'GroupedElement;id=' . $element->getId() . ',html=' . $element->__toString(),
            $group->render($renderer->reset())->__toString()
        );

        $renderer->setElementTemplateForGroupId('testRenderGroup', 'HTML_QuickForm2_Element', null);
        $this->assertStringContainsString(
            'GroupedInput;id=' . $element->getId() . ',html=' . $element->__toString(),
            $group->render($renderer->reset())->__toString()
        );

        $renderer->setElementTemplateForGroupClass('HTML_QuickForm2_Container_Group', 'HTML_QuickForm2_Element_Input', null);
        $this->assertStringNotContainsString(
            'IgnoreThis', $group->render($renderer->reset())->__toString()
        );
    }

    public function testRenderGroupedElementsWithSeparators(): void
    {
        $group = HTML_QuickForm2_Factory::createElement('group', 'foo', array('id' => 'testSeparators'));
        $element1 = $group->addElement('text', 'bar');
        $element2 = $group->addElement('text', 'baz');
        $element3 = $group->addElement('text', 'quux');

        $renderer = HTML_Quickform2_Renderer::createDefault()
            ->setTemplateForId('testSeparators', '{content}')
            ->setElementTemplateForGroupId(
                'testSeparators', 'HTML_QuickForm2_Element_InputText', '<foo>{element}</foo>'
            );

        $this->assertEquals(
            '<foo>' . $element1 . '</foo><foo>' . $element2 . '</foo><foo>' . $element3 . '</foo>',
            $group->render($renderer->reset())->__toString()
        );

        $group->setSeparator('&nbsp;');
        $this->assertEquals(
            '<foo>' . $element1 . '</foo>&nbsp;<foo>' . $element2 . '</foo>&nbsp;<foo>' . $element3 . '</foo>',
            $group->render($renderer->reset())->__toString()
        );

        $group->setSeparator(array('<br />', '&nbsp;'));
        $this->assertEquals(
            '<foo>' . $element1 . '</foo><br /><foo>' . $element2 . '</foo>&nbsp;<foo>' . $element3 . '</foo>',
            $group->render($renderer->reset())->__toString()
        );
    }
}
?>
