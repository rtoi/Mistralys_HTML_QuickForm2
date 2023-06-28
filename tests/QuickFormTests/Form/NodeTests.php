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

declare(strict_types=1);

namespace QuickFormTests\Form;

use HTML_QuickForm2;
use HTML_QuickForm2_Container;
use HTML_QuickForm2_Element_InputText;
use HTML_QuickForm2_InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use QuickFormTests\CustomClasses\TestNodeImpl;

/**
 * Unit test for HTML_QuickForm2_Node class,
 */
class NodeTests extends TestCase
{
    public function testGetElementById() : void
    {
        $obj = new TestNodeImpl();
        $obj->setId('my-node');

        $form = new HTML_QuickForm2('my-form');
        $form->addElement($obj);

        $this->assertNotNull($form->getElementById('my-node'));
    }

    public function testRequireElementById() : void
    {
        $obj = new TestNodeImpl();
        $obj->setId('my-node');

        $form = new HTML_QuickForm2('my-form');
        $form->addElement($obj);

        $this->assertNotNull($form->requireElementById('my-node'));
    }

    public function testRequireElementByIdNotExists() : void
    {
        $form = new HTML_QuickForm2('my-form');

        $this->expectExceptionCode(HTML_QuickForm2_Container::ERROR_ELEMENT_NOT_FOUND_BY_ID);

        $form->requireElementById('unknown-node');
    }

    public function testCanSetLabel(): void
    {
        $obj = new TestNodeImpl();
        $this->assertNull($obj->getLabel());

        $obj2 = new TestNodeImpl(null, null, array('label' => 'a label'));
        $this->assertEquals('a label', $obj2->getLabel());

        $this->assertSame($obj2, $obj2->setLabel('another label'));
        $this->assertEquals('another label', $obj2->getLabel());
    }

    public function testCanFreezeAndUnfreeze(): void
    {
        $obj = new TestNodeImpl();
        $this->assertFalse($obj->toggleFrozen(), 'Elements should NOT be frozen by default');

        $oldFrozen = $obj->toggleFrozen(true);
        $this->assertFalse($oldFrozen, 'toggleFrozen() should return previous frozen status');
        $this->assertTrue($obj->toggleFrozen());

        $this->assertTrue($obj->toggleFrozen(false), 'toggleFrozen() should return previous frozen status');
        $this->assertFalse($obj->toggleFrozen());
    }

    public function testCanSetPersistentFreeze(): void
    {
        $obj = new TestNodeImpl();
        $this->assertFalse($obj->persistentFreeze(), 'Frozen element\'s data should NOT persist by default');

        $oldPersistent = $obj->persistentFreeze(true);
        $this->assertFalse($oldPersistent, 'persistentFreeze() should return previous persistence status');
        $this->assertTrue($obj->persistentFreeze());

        $this->assertTrue($obj->persistentFreeze(false), 'persistentFreeze() should return previous persistence status');
        $this->assertFalse($obj->persistentFreeze());
    }

    public function testCanSetAndGetError() : void
    {
        $obj = new TestNodeImpl();
        $this->assertNull($obj->getError(), 'Elements shouldn\'t have a error message by default');

        $this->assertSame($obj, $obj->setError('An error message'));
        $this->assertEquals('An error message', $obj->getError());
    }

    public function testSetEmptyErrorMessage() : void
    {
        $obj = new TestNodeImpl();

        $this->assertSame($obj, $obj->setError(''));
        $this->assertNull($obj->getError());
    }

    public function testValidate() : void
    {
        $valid = new TestNodeImpl();
        $ruleTrue = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->setMethods(array('validateOwner'))
            ->setConstructorArgs(array($valid, 'A message'))
            ->getMock();
        $ruleTrue->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(true));
        $valid->addRule($ruleTrue);
        $this->assertTrue($valid->validate());
        $this->assertEquals('', $valid->getError());

        $invalid = new TestNodeImpl();
        $ruleFalse = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->setMethods(array('validateOwner'))
            ->setConstructorArgs(array($invalid, 'An error message'))
            ->getMock();
        $ruleFalse->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(false));
        $invalid->addRule($ruleFalse);
        $this->assertFalse($invalid->validate());
        $this->assertEquals('An error message', $invalid->getError());
    }

    public function testValidateUntilErrorMessage(): void
    {
        $preError = new TestNodeImpl();
        $preError->setError('some message');
        $ruleIrrelevant = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->onlyMethods(array('validateOwner'))
            ->setConstructorArgs(array($preError))
            ->getMock();
        $ruleIrrelevant->expects($this->never())->method('validateOwner');
        $preError->addRule($ruleIrrelevant);
        $this->assertFalse($preError->validate());

        $manyRules = new TestNodeImpl();
        $ruleTrue = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->setMethods(array('validateOwner'))
            ->setConstructorArgs(array($manyRules, 'irrelevant message'))
            ->getMock();
        $ruleTrue->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(true));
        $ruleFalseNoMessage = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->setMethods(array('validateOwner'))
            ->setConstructorArgs(array($manyRules, ''))
            ->getMock();
        $ruleFalseNoMessage->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(false));
        $ruleFalseWithMessage = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->setMethods(array('validateOwner'))
            ->setConstructorArgs(array($manyRules, 'some error'))
            ->getMock();
        $ruleFalseWithMessage->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(false));
        $ruleStillIrrelevant = $this->getMockBuilder('HTML_QuickForm2_Rule')
            ->setMethods(array('validateOwner'))
            ->setConstructorArgs(array($manyRules, '...'))
            ->getMock();
        $ruleStillIrrelevant->expects($this->never())->method('validateOwner');
        $manyRules->addRule($ruleTrue);
        $manyRules->addRule($ruleFalseNoMessage);
        $manyRules->addRule($ruleFalseWithMessage);
        $manyRules->addRule($ruleStillIrrelevant);
        $this->assertFalse($manyRules->validate());
        $this->assertEquals('some error', $manyRules->getError());
    }

    public function testRemoveRule(): void
    {
        $node = new TestNodeImpl();
        $removed = $node->addRule(
            $this->getMockBuilder('HTML_QuickForm2_Rule')
                ->setMethods(array('validateOwner'))
                ->setConstructorArgs(array($node, '...'))
                ->getMock()
        );
        $removed->expects($this->never())->method('validateOwner');
        $node->removeRule($removed);
        $this->assertTrue($node->validate());
    }

    public function testAddRuleOnlyOnce(): void
    {
        $node = new TestNodeImpl();
        $mock = $node->addRule(
            $this->getMockBuilder('HTML_QuickForm2_Rule')
                ->setMethods(array('validateOwner'))
                ->setConstructorArgs(array($node, '...'))
                ->getMock()
        );
        $mock->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(false));

        $node->addRule($mock);
        $this->assertFalse($node->validate());
    }

    public function testRemoveRuleOnChangingOwner(): void
    {
        $nodeOne = new TestNodeImpl();
        $nodeTwo = new TestNodeImpl();
        $mockRule = $nodeOne->addRule(
            $this->getMockBuilder('HTML_QuickForm2_Rule')
                ->setMethods(array('validateOwner'))
                ->setConstructorArgs(array($nodeOne, '...'))
                ->getMock()
        );
        $mockRule->expects($this->once())->method('validateOwner')
            ->will($this->returnValue(false));

        $nodeTwo->addRule($mockRule);
        $this->assertTrue($nodeOne->validate());
        $this->assertFalse($nodeTwo->validate());
    }

    public function testElementIsNotRequiredByDefault(): void
    {
        $node = new TestNodeImpl();
        $this->assertFalse($node->isRequired());
    }

    /**
     * Disallow spaces in values of 'id' attributes
     *
     * @dataProvider invalidIdProvider
     * @link http://pear.php.net/bugs/17576
     */
    public function testRequest18683($id): void
    {
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);

        $node = new TestNodeImpl();
        $node->setId($id);
    }

    public function testSetComment() : void
    {
        $el = new HTML_QuickForm2_Element_InputText();

        $this->assertNull($el->getComment());

        $el->setComment('');
        $this->assertNull($el->getComment());

        $el->setComment(78);
        $this->assertSame('78', $el->getComment());
    }

    public function testAppendComment() : void
    {
        $el = new HTML_QuickForm2_Element_InputText();

        $el->appendComment(null);
        $this->assertNull($el->getComment());

        $el->appendComment('');
        $this->assertNull($el->getComment());

        $el->appendComment('First');
        $this->assertSame('First', $el->getComment());

        $el->appendComment('Second');
        $this->assertSame('First Second', $el->getComment());
    }

    public static function invalidIdProvider()
    {
        return array(
            array("\x0C"),
            array(" foo\n"),
            array("foo\rbar"),
            array('bar baz')
        );
    }
}
