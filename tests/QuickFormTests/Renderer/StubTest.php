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
 * Unit test for HTML_QuickForm2_Renderer_Stub class
 */
class HTML_QuickForm2_Renderer_StubTest extends TestCase
{
    public function testHasRequired(): void
    {
        $form     = new HTML_QuickForm2('testHasRequired');
        $text     = $form->addText('anElement');
        $renderer = HTML_QuickForm2_Renderer::createStub();

        $form->render($renderer);
        $this->assertFalse($renderer->hasRequired());

        $text->addRule('required', 'element is required');
        $form->render($renderer);
        $this->assertTrue($renderer->hasRequired());
    }

    public function testGroupErrors(): void
    {
        $form     = new HTML_QuickForm2('testGroupErrors');
        $text     = $form->addText('anElement', array('id' => 'anElement'))
                        ->setError('an error');
        $renderer = HTML_QuickForm2_Renderer::createStub();

        $renderer->setGroupErrors(false);
        $form->render($renderer);
        $this->assertEquals(array(), $renderer->getErrors());

        $renderer->setGroupErrors(true);
        $form->render($renderer);
        $this->assertEquals(array('anElement' => 'an error'), $renderer->getErrors());
    }

    public function testGroupHiddens(): void
    {
        $form     = new HTML_QuickForm2('testGroupHiddens', 'post', null, false);
        $hidden   = $form->addHidden('aHiddenElement');
        $renderer = HTML_QuickForm2_Renderer::createStub();

        $renderer->setGroupHiddens(false);
        $form->render($renderer);
        $this->assertEquals(array(), $renderer->getHidden());

        $renderer->setGroupHiddens(true);
        $form->render($renderer);
        $this->assertEquals(array($hidden->__toString()), $renderer->getHidden());
    }
}
