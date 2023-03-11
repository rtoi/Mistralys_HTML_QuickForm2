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
 * @package   HTML_QuickForm2
 * @author    Alexey Borzov <avb@php.net>
 * @author    Bertrand Mansion <golgote@mamasam.com>
 * @category  HTML
 * @copyright 2006-2020 Alexey Borzov <avb@php.net>, Bertrand Mansion <golgote@mamasam.com>
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      https://pear.php.net/package/HTML_QuickForm2
 */

namespace QuickFormTests\Element;

use HTML_QuickForm2_InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use QuickFormTests\CustomClasses\TestInputImplElement;

/**
 * Unit test for {@see \HTML_QuickForm2_Element_Input} class
 *
 * @see TestInputImplElement
 */
class InputTest extends TestCase
{
    public function testTypeAttributeIsReadonly() : void
    {
        $obj = new TestInputImplElement();
        try
        {
            $obj->removeAttribute('type');
        }
        catch (HTML_QuickForm2_InvalidArgumentException $e)
        {
            $this->assertEquals("Attribute 'type' is read-only", $e->getMessage());
            try
            {
                $obj->setAttribute('type', 'bogus');
            }
            catch (HTML_QuickForm2_InvalidArgumentException $e)
            {
                $this->assertEquals("Attribute 'type' is read-only", $e->getMessage());
                return;
            }
        }
        $this->fail('Expected HTML_QuickForm2_InvalidArgumentException was not thrown');
    }

    public function testCanSetAndGetValue() : void
    {
        $obj = new TestInputImplElement();

        $this->assertSame($obj, $obj->setValue('foo'));
        $this->assertEquals('foo', $obj->getValue());

        $obj->setAttribute('value', 'bar');
        $this->assertEquals('bar', $obj->getValue());

        $obj->setAttribute('disabled');
        $this->assertNull($obj->getValue());
    }

    public function testSetNullValue() : void
    {
        $obj = new TestInputImplElement();
        $obj->setValue(null);

        $this->assertEquals('', $obj->getValue());
    }

    public function testHtmlGeneration() : void
    {
        $obj = new TestInputImplElement();
        $this->assertMatchesRegularExpression('!<input[^>]*type="concrete"[^>]*/>!', $obj->__toString());
    }

    public function testFrozenHtmlGeneration() : void
    {
        $obj = new TestInputImplElement('test');
        $obj->setValue('bar');
        $obj->toggleFrozen(true);

        $obj->persistentFreeze(false);
        $this->assertDoesNotMatchRegularExpression('/[<>]/', $obj->__toString());
        $this->assertMatchesRegularExpression('/bar/', $obj->__toString());

        $obj->persistentFreeze(true);
        $this->assertMatchesRegularExpression('!<input[^>]*type="hidden"[^>]*/>!', $obj->__toString());

        $obj->setAttribute('disabled');
        $this->assertMatchesRegularExpression('/bar/', $obj->__toString());
        $this->assertDoesNotMatchRegularExpression('!<input[^>]*type="hidden"[^>]*/>!', $obj->__toString());
    }
}
