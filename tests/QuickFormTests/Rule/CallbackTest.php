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

namespace QuickFormTests\Rule;

use HTML_QuickForm2_Element;
use HTML_QuickForm2_Element_Input;
use HTML_QuickForm2_Factory;
use HTML_QuickForm2_Rule_Callback;
use PHPUnit\Framework\MockObject\MockObject;
use QuickFormTests\CaseClasses\QuickFormCase;

/**
 * Unit test for {@see HTML_QuickForm2_Rule_Callback} class
 */
class CallbackTest extends QuickFormCase
{
    // region: _Tests

    public function testMissingCallback() : void
    {
        $this->expectExceptionCode(HTML_QuickForm2_Rule_Callback::ERROR_INVALID_CALLBACK);

        new HTML_QuickForm2_Rule_Callback(
            $this->createElementMock(),
            'an error'
        );
    }

    public function testInvalidCallback() : void
    {
        $this->expectExceptionCode(HTML_QuickForm2_Rule_Callback::ERROR_INVALID_CALLBACK);

        new HTML_QuickForm2_Rule_Callback(
            $this->createElementMock(),
            'an error',
            array('callback' => 'bogusfunctionname')
        );
    }

    public function testOptionsHandling() : void
    {
        $mockEl = $this->createElementMock();

        $mockEl
            ->expects($this->atLeastOnce())
            ->method(array(HTML_QuickForm2_Element::class, 'getRawValue')[1])
            ->willReturn('foo');

        $strlen = new HTML_QuickForm2_Rule_Callback($mockEl, 'an error', 'strlen');
        $this->assertTrue($strlen->validate());

        $notFoo = new HTML_QuickForm2_Rule_Callback($mockEl, 'an error', array($this, 'checkNotFoo'));
        $this->assertFalse($notFoo->validate());

        $inArray = new HTML_QuickForm2_Rule_Callback($mockEl, 'an error',
            array('callback' => 'in_array',
                'arguments' => array(array('foo', 'bar', 'baz'))));
        $this->assertTrue($inArray->validate());
    }

    public function testConfigHandling() : void
    {
        $mockEl = $this->createElementMock();

        $mockEl
            ->expects($this->atLeastOnce())
            ->method(array(HTML_QuickForm2_Element::class, 'getRawValue')[1])
            ->willReturn('foo');

        HTML_QuickForm2_Factory::registerRule('strlen', 'HTML_QuickForm2_Rule_Callback', null, 'strlen');
        $strlen = HTML_QuickForm2_Factory::createRule('strlen', $mockEl, 'an error');
        $this->assertTrue($strlen->validate());

        HTML_QuickForm2_Factory::registerRule('inarray', 'HTML_QuickForm2_Rule_Callback', null,
            array('callback' => 'in_array',
                'arguments' => array(array('foo', 'bar', 'baz'))));
        $inArray = HTML_QuickForm2_Factory::createRule('inarray', $mockEl, 'an error');
        $this->assertTrue($inArray->validate());

        HTML_QuickForm2_Factory::registerRule('inarray2', 'HTML_QuickForm2_Rule_Callback', null, 'in_array');
        $inArray2 = HTML_QuickForm2_Factory::createRule('inarray2', $mockEl, 'an error',
            array(array('one', 'two', 'three')));
        $this->assertFalse($inArray2->validate());
    }

    public function testConfigOverridesOptions() : void
    {
        $mockEl = $this->createElementMock();

        $mockEl
            ->expects($this->atLeastOnce())
            ->method('getRawValue')
            ->willReturn('foo');

        HTML_QuickForm2_Factory::registerRule(
            'inarray-override',
            HTML_QuickForm2_Rule_Callback::class,
            null,
            array(
                'callback' => 'in_array',
                'arguments' => array(array('foo', 'bar', 'baz'))
            )
        );

        $rule1 = HTML_QuickForm2_Factory::createRule(
            'inarray-override',
            $mockEl,
            'an error',
            array('callback' => array($this, 'checkNotFoo'))
        );

        $this->assertTrue($rule1->validate());

        $rule2 = HTML_QuickForm2_Factory::createRule(
            'inarray-override',
            $mockEl,
            'an error',
            array('arguments' => array(array('one', 'two', 'three')))
        );

        $this->assertTrue($rule2->validate());
    }

    public function testElementAddCallback() : void
    {
        $el = new HTML_QuickForm2_Element_Input();
        $el->addRuleCallback('message', array($this, 'checkNotFoo'));

        $rules = $el->getRules();
        $this->assertCount(1, $rules);
        $this->assertInstanceOf(HTML_QuickForm2_Rule_Callback::class, $rules[0]);
    }

    // endregion

    // region: Support methods

    /**
     * @return (HTML_QuickForm2_Element&MockObject)
     */
    protected function createElementMock() : HTML_QuickForm2_Element
    {
        return $this
            ->getMockBuilder(HTML_QuickForm2_Element::class)
            ->onlyMethods(array(
                array(HTML_QuickForm2_Element::class, 'getType')[1],
                array(HTML_QuickForm2_Element::class, 'getRawValue')[1],
                array(HTML_QuickForm2_Element::class, 'setValue')[1],
                array(HTML_QuickForm2_Element::class, '__toString')[1]
            ))
            ->getMock();
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function checkNotFoo($value) : bool
    {
        return $value !== 'foo';
    }

    // endregion
}
