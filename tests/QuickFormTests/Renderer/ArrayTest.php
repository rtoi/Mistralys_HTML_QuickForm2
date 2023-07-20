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

use HTML\QuickForm2\ElementFactory;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for HTML_QuickForm2_Renderer_Array class
 */
class HTML_QuickForm2_Renderer_ArrayTest extends TestCase
{
    private function _assertHasKeys($array, array $keys): void
    {
        sort($keys);
        $realKeys = array_keys($array);
        sort($realKeys);
        $this->assertEquals($keys, $realKeys);
    }

    public function testRenderElementSeparately(): void
    {
        $element  = HTML_QuickForm2_Factory::createElement(
            'text', 'foo', array('id' => 'arrayRenderElement')
        );

        $renderer = HTML_QuickForm2_Renderer::createArray();
        $array = $element->renderToArray($renderer);

        $this->_assertHasKeys(
            $array,
            array('id', 'html', 'value', 'type', 'required', 'frozen')
        );

        $element->setLabel('Foo label:');
        $element->setError('an error!');

        $renderer->reset();
        $array = $element->renderToArray($renderer);

        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('error', $array);
    }

    public function testRenderHidden(): void
    {
        $hidden = HTML_QuickForm2_Factory::createElement(
            'hidden', 'bar', array('id' => 'arrayRenderHidden')
        );
        $renderer = HTML_QuickForm2_Renderer::createArray()
            ->setGroupHiddens(false);

        $array = $hidden->renderToArray($renderer);

        $this->_assertHasKeys(
            $array,
            array('id', 'html', 'value', 'type', 'required', 'frozen')
        );

        $renderer
            ->setGroupHiddens(true)
            ->reset();

        $array = $hidden->renderToArray($renderer);

        $this->assertEquals(array('hidden'), array_keys($array));
        $this->assertEquals($hidden->__toString(), $array['hidden'][0]);
    }

    public function testRenderContainerSeparately(): void
    {
        $fieldset = ElementFactory::fieldset('baz')
            ->setId('arrayRenderContainer');

        $renderer = HTML_QuickForm2_Renderer::createArray();

        $array = $fieldset->renderToArray($renderer);
        $this->_assertHasKeys(
            $array,
            array('id', 'type', 'required', 'frozen', 'elements', 'attributes')
        );
        $this->assertEquals(array(), $array['elements']);

        $fieldset->setLabel('a label');
        $fieldset->setError('an error!');
        $text = $fieldset->addText('insideFieldset');

        $renderer->reset();
        $array = $fieldset->renderToArray($renderer);

        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('error', $array);
        $this->assertEquals($array['elements'][0]['html'], $text->__toString());
    }

    public function testRenderNestedContainers(): void
    {
        $fieldset = ElementFactory::fieldset('quux')
        ->setId('arrayNestedContainers');

        $group = $fieldset->addGroup('xyzzy')
            ->setId('arrayInnerContainer')
            ->setSeparator('<br />');

        $text = $group->addText('foobar')
            ->setId('arrayInnermost');

        $renderer = HTML_QuickForm2_Renderer::createArray();

        $array = $fieldset->renderToArray($renderer);

        $renderer->reset();
        $elArray = $text->renderToArray($renderer);

        $this->assertArrayHasKey('elements', $array['elements'][0]);
        $this->assertArrayHasKey('separator', $array['elements'][0]);
        $this->assertEquals($elArray, $array['elements'][0]['elements'][0]);
    }

    public function testRenderGroupedErrors(): void
    {
        $form     = new HTML_QuickForm2('arrayGroupedErrors');
        $form->addText('testArrayGroupedErrors')->setError('Some error');
        
        $renderer = HTML_QuickForm2_Renderer::createArray()
            ->setGroupErrors(false);

        $this->assertArrayNotHasKey('errors', $form->render($renderer)->toArray());

        $renderer->setGroupErrors(true);
        $array = $form->renderToArray($renderer);

        $this->assertArrayNotHasKey('error', $array['elements'][0]);
        $this->assertContains('Some error', $array['errors']);
    }

    public function testRenderRequiredNote(): void
    {
        $form = new HTML_QuickForm2('arrayReqnote');
        $element = $form->addText('testArrayReqnote');

        $renderer = HTML_Quickform2_Renderer::createArray()
            ->setRequiredNote('This is requi-i-i-ired!');

        $this->assertArrayNotHasKey('required_note', $form->renderToArray($renderer));

        $element->addRule('required', 'error message');

        $array = $form->renderToArray($renderer);

        $this->assertEquals('This is requi-i-i-ired!', $array['required_note']);
    }

    public function testRenderWithStyle(): void
    {
        $form = new HTML_QuickForm2('arrayStyle');
        $form->addText('foo', array('id' => 'testArrayWithStyle'));
        $form->addText('bar', array('id' => 'testArrayWithoutStyle'));

        $renderer = HTML_Quickform2_Renderer::createArray()
            ->setStyleForId('testArrayWithStyle', 'weird');

        $array = $form->renderToArray($renderer);

        $this->assertEquals('weird', $array['elements'][0]['style']);
        $this->assertArrayNotHasKey('style', $array['elements'][1]);
    }

    public function testRenderStaticLabels(): void
    {
        $element  = ElementFactory::text('static')
            ->setLabel(array(
                'a label',
                'another label',
                'foo' => 'named label'
            )
        );

        $renderer = HTML_QuickForm2_Renderer::createArray()
            ->setStaticLabels(false);

        $this->assertFalse($renderer->isStaticLabelsEnabled());

        $array = $element->renderToArray($renderer);
        $this->assertIsArray($array['label']);

        $renderer
            ->setStaticLabels(true)
            ->reset();

        $this->assertTrue($renderer->isStaticLabelsEnabled());

        $array = $element->renderToArray($renderer);

        $this->assertEquals('a label', $array['label']);
        $this->assertEquals('another label', $array['label_2']);
        $this->assertEquals('named label', $array['label_foo']);
    }
}
