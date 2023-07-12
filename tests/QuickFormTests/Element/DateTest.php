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

namespace QuickFormTests\Element;

use DateTime;
use DateTimeImmutable;
use HTML\QuickForm2\Element\Select\SelectOption;
use HTML\QuickForm2\ElementFactory;
use HTML_QuickForm2;
use HTML_QuickForm2_DataSource_Array;
use HTML_QuickForm2_Element_Date;
use HTML_QuickForm2_Element_Select;
use HTML_QuickForm2_InvalidArgumentException;
use HTML_QuickForm2_MessageProvider;
use HTML_QuickForm2_Node;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function testInvalidMessageProvider() : void
    {
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);

        new HTML_QuickForm2_Element_Date('invalid', null, array('messageProvider' => array()));
    }

    public static function callbackMessageProvider() : array
    {
        return array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Caturday');
    }

    public function testCallbackMessageProvider() : void
    {
        $date = ElementFactory::date('callback')
            ->setFormat('l')
            ->setMessageProvider(array(__CLASS__, 'callbackMessageProvider'));

        $weekdays = $date->getWeekdayNames();

        $this->assertArrayHasKey(6, $weekdays);
        $this->assertSame('Caturday', $weekdays[6]);
        $this->assertStringContainsString('<option value="6">Caturday</option>', $date->__toString());
    }

    public function testObjectMessageProvider() : void
    {
        $mockProvider = $this->getMockBuilder(HTML_QuickForm2_MessageProvider::class)
            ->onlyMethods(array('get'))
            ->getMock();

        $mockProvider
            ->expects($this->once())
            ->method('get')
            ->willReturn(array(
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Caturday'
            ));

        $date = ElementFactory::date('object')
            ->setFormat('l')
            ->setMessageProvider($mockProvider);

        $this->assertStringContainsString('<option value="6">Caturday</option>', $date->__toString());
    }

    /**
     * Support for minHour and maxHour
     * @see http://pear.php.net/bugs/4061
     */
    public function testRequest4061() : void
    {
        $date = ElementFactory::date('MaxMinHour')
            ->setFormat('H')
            ->setMinHour(22)
            ->setMaxHour(6);

        $this->assertMatchesRegularExpression(
            '!<option value="22">22</option>.+<option value="6">06</option>!is',
            $date->__toString()
        );
        $this->assertStringNotContainsString(
            '<option value="5">05</option>',
            $date->__toString()
        );
    }

    /**
     * Support for minMonth and maxMonth
     * @see http://pear.php.net/bugs/5957
     */
    public function testRequest5957() : void
    {
        $date = ElementFactory::date('MaxMinMonth')
            ->setFormat('F')
            ->setMinMonth(10)
            ->setMaxMonth(3);

        $this->assertMatchesRegularExpression('!October.+March!is', $date->__toString());
        $this->assertStringNotContainsString('January', $date->__toString());
    }

    public function testSetValueAcceptsDateTime() : void
    {
        $date = new HTML_QuickForm2_Element_Date('DateTimeTest', null, array('format' => 'Ymd'));
        $date->setValue(new DateTime('2012-06-26'));
        $this->assertEquals(array('Y' => 2012, 'm' => 6, 'd' => 26), $date->getValue());
    }

    public function testSetValueAcceptsDateTimeImmutable() : void
    {
        if (PHP_VERSION_ID < 50500)
        {
            $this->markTestSkipped("DateTimeImmutable is available since PHP 5.5");
        }

        $date = new HTML_QuickForm2_Element_Date('DateTimeImmutableTest', null, ['format' => 'Ymd']);
        $date->setValue(new DateTimeImmutable('2020-09-14'));
        $this->assertEquals(['Y' => 2020, 'm' => 9, 'd' => 14], $date->getValue());
    }

    /**
     * If data source contains explicitly provided null values, those should be used
     * @link http://pear.php.net/bugs/bug.php?id=20295
     */
    public function testBug20295() : void
    {
        $form = new HTML_QuickForm2('bug20295');
        $date = $form->addDate('aDate', null, array('format' => 'Ymd'))
            ->setValue('today');

        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'aDate' => null
        )));

        $this->assertNull($date->getValue());
    }

    public function testEmptyOptionDefault() : void
    {
        $el = ElementFactory::date();

        $this->assertFalse($el->hasEmptyOption('Y'));
    }

    public function testEmptyOptionForAllSelects() : void
    {
        $el = ElementFactory::date();
        $el->setEmptyOptionForAll('text', 'value');

        $this->assertTrue($el->hasEmptyOption('Y'));
    }

    public function testEmptyOptionForSingleSelect() : void
    {
        $el = ElementFactory::date();
        $el->setEmptyOptionForFormat('Y', 'text', 'value');

        $this->assertTrue($el->hasEmptyOption('Y'));
    }

    public function testSetFormatRegeneratesSelects() : void
    {
        $el = ElementFactory::date();

        $this->assertCount(3, $el->getElements());

        $el->setFormat('dmYHis');

        $this->assertCount(6, $el->getElements());
    }

    public function testEmptyOptionIsPresentInSelects() : void
    {
        $el = ElementFactory::date();

        $el->setEmptyOptionForFormat('Y', 'text', 'value');

        $yearSelect = $el->getElementByName('Y');

        $this->assertSelectHasEmptyOption($yearSelect, 'text', 'value');
    }

    public function testEmptyOptionIsPresentInAllSelects() : void
    {
        $el = ElementFactory::date();

        $el->setEmptyOptionForAll('text', 'value');

        $elements = $el->getElements();

        foreach ($elements as $element)
        {
            $this->assertSelectHasEmptyOption($element, 'text', 'value');
        }
    }

    private function assertSelectHasEmptyOption(HTML_QuickForm2_Node $select, string $text, string $value) : void
    {
        $this->assertInstanceOf(HTML_QuickForm2_Element_Select::class, $select);

        $options = $select->getOptionContainer()->getOptions();
        $this->assertNotEmpty($options);

        $first = array_shift($options);
        $this->assertInstanceOf(SelectOption::class, $first);
        $this->assertSame($text, $first->getLabel());
        $this->assertSame($value, $first->getValue());
    }
}
