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
 * Unit test for HTML_QuickForm2_Element_InputCheckable class
 */
class HTML_QuickForm2_Element_InputCheckableTest extends TestCase
{
    public function testConstructorSetsContent(): void
    {
        $checkable = new HTML_QuickForm2_Element_InputCheckable('foo', null, array('content' => 'I am foo'));
        $this->assertEquals('I am foo', $checkable->getContent());
    }

    public function testContentRendering(): void
    {
        $checkable = new HTML_QuickForm2_Element_InputCheckable(
            'foo', array('id' => 'checkableFoo'), array('content' => 'I am foo')
        );
        $this->assertMatchesRegularExpression(
            '!<label\\s+for="checkableFoo">I am foo</label>!',
            $checkable->__toString()
        );

        $checkable->toggleFrozen(true);
        $this->assertDoesNotMatchRegularExpression('!<label!', $checkable->__toString());

        $checkable->toggleFrozen(false);
        $this->assertSame($checkable, $checkable->setContent(''));
        $this->assertDoesNotMatchRegularExpression('!<label!', $checkable->__toString());
    }

    public function testEmptyContentRendering(): void
    {
        $checkable = new HTML_QuickForm2_Element_InputCheckable(
            'foo1', array('id' => 'checkableFoo1')
        );
        $this->assertDoesNotMatchRegularExpression('!<label!', $checkable->__toString());
    }

    public function testSetAndGetValue(): void
    {
        $checkable = new HTML_QuickForm2_Element_InputCheckable();
        $checkable->setAttribute('value', 'my value');

        $this->assertNull($checkable->getValue());

        $this->assertSame($checkable, $checkable->setValue('my value'));
        $this->assertEquals('checked', $checkable->getAttribute('checked'));
        $this->assertEquals('my value', $checkable->getValue());

        $this->assertSame($checkable, $checkable->setValue('not my value!'));
        $this->assertNull($checkable->getAttribute('checked'));
        $this->assertNull($checkable->getValue());

        $checkable->setAttribute('checked');
        $this->assertEquals('my value', $checkable->getValue());
    }

    public function testGetValueDisabled(): void
    {
        $checkable = new HTML_QuickForm2_Element_InputCheckable();
        $checkable->setAttribute('value', 'my value');

        $checkable->setValue('my value');
        $checkable->setAttribute('disabled');
        $this->assertEquals('checked', $checkable->getAttribute('checked'));
        $this->assertNull($checkable->getValue());
    }

    public function testFrozenHtmlGeneration(): void
    {
        $checkable = new HTML_QuickForm2_Element_InputCheckable(
            'checkableFreeze', array('value' => 'my value'), array('content' => 'freeze me')
        );
        $checkable->setAttribute('checked');

        $checkable->toggleFrozen(true);
        $this->assertMatchesRegularExpression('!<input[^>]*type="hidden"[^>]*/>!', $checkable->__toString());

        $checkable->removeAttribute('checked');
        $this->assertDoesNotMatchRegularExpression('!<input!', $checkable->__toString());
    }

    public function testBug15708(): void
    {
        $form = new HTML_QuickForm2('bug15708');
        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'aRadio' => 1
        )));
        $aRadio = $form->appendChild(
                            new HTML_QuickForm2_Element_InputCheckable('aRadio')
                      )->setAttribute('value', 1);
        $this->assertStringContainsString('checked', $aRadio->__toString());
    }

}
?>
