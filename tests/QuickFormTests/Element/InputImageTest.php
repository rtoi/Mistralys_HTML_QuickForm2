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
 * Unit test for HTML_QuickForm2_Element_InputImage class
 */
class HTML_QuickForm2_Element_InputImageTest extends TestCase
{
    protected function setUp() : void
    {
        $_POST = array(
            'foo_x' => '12',
            'foo_y' => '34',
            'bar' => array(
                'idx' => array('56', '78')
            )
        );
    }

    public function testCannotBeFrozen() : void
    {
        $image = new HTML_QuickForm2_Element_InputImage('foo');
        $this->assertFalse($image->isFreezable());
        $this->assertFalse($image->toggleFrozen(true));
        $this->assertFalse($image->toggleFrozen());
    }

    public function testPhpBug745Workaround(): void
    {
        $image1 = new HTML_QuickForm2_Element_InputImage('foo');
        $this->assertMatchesRegularExpression('/name="foo"/', $image1->__toString());

        $image2 = new HTML_QuickForm2_Element_InputImage('foo[bar]');
        $this->assertMatchesRegularExpression('/name="foo\\[bar\\]\\[\\]"/', $image2->__toString());
        $this->assertEquals('foo[bar]', $image2->getName());

        $image3 = new HTML_QuickForm2_Element_InputImage('foo[bar][]');
        $this->assertMatchesRegularExpression('/name="foo\\[bar\\]\\[\\]"/', $image3->__toString());
        $this->assertEquals('foo[bar][]', $image3->getName());
    }

    public function testSetValueFromSubmitDataSource(): void
    {
        $form = new HTML_QuickForm2('image', 'post', null, false);
        $foo = $form->appendChild(new HTML_QuickForm2_Element_InputImage('foo'));
        $bar = $form->appendChild(new HTML_QuickForm2_Element_InputImage('bar[idx]'));

        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'foo_x' => '1234',
            'foo_y' => '5678',
            'bar' => array(
                'idx' => array('98', '76')
            )
        )));
        $this->assertEquals(array('x' => '12', 'y' => '34'), $foo->getValue());
        $this->assertEquals(array('x' => '56', 'y' => '78'), $bar->getValue());

        $foo->setAttribute('disabled');
        $this->assertNull($foo->getValue());
    }
}
?>
