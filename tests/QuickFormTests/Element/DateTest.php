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

class HTML_QuickForm2_Element_DateTest extends TestCase
{
    public function testInvalidMessageProvider(): void
    {
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);
        
        $invalid = new HTML_QuickForm2_Element_Date('invalid', null, array('messageProvider' => array()));
    }

    public static function callbackMessageProvider($messageId, $langId)
    {
        return array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Caturday');
    }

    public function testCallbackMessageProvider(): void
    {
        $date = new HTML_QuickForm2_Element_Date('callback', null, array(
            'format'          => 'l',
            'messageProvider' => array(__CLASS__, 'callbackMessageProvider')
        ));
        $this->assertStringContainsString('<option value="6">Caturday</option>', $date->__toString());
    }

    public function testObjectMessageProvider(): void
    {
        $mockProvider = $this->getMockBuilder('HTML_QuickForm2_MessageProvider')
            ->setMethods(array('get'))
            ->getMock();
        $mockProvider->expects($this->once())->method('get')
                     ->will($this->returnValue(array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Caturday')));
        $date = new HTML_QuickForm2_Element_Date('object', null, array(
            'format'          => 'l',
            'messageProvider' => $mockProvider
        ));
        $this->assertStringContainsString('<option value="6">Caturday</option>', $date->__toString());
    }

   /**
    * Support for minHour and maxHour
    * @see http://pear.php.net/bugs/4061
    */
    public function testRequest4061(): void
    {
        $date = new HTML_QuickForm2_Element_Date('MaxMinHour', null, array(
            'format' => 'H', 'minHour' => 22, 'maxHour' => 6
        ));
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
    public function testRequest5957(): void
    {
        $date = new HTML_QuickForm2_Element_Date('MaxMinMonth', null, array(
            'format' => 'F', 'minMonth' => 10, 'maxMonth' => 3
        ));
        $this->assertMatchesRegularExpression('!October.+March!is', $date->__toString());
        $this->assertStringNotContainsString('January', $date->__toString());
    }

    public function testSetValueAcceptsDateTime(): void
    {
        $date = new HTML_QuickForm2_Element_Date('DateTimeTest', null, array('format' => 'Ymd'));
        $date->setValue(new DateTime('2012-06-26'));
        $this->assertEquals(array('Y' => 2012, 'm' => 6, 'd' => 26), $date->getValue());
    }

    public function testSetValueAcceptsDateTimeImmutable(): void
    {
        if (version_compare(phpversion(), '5.5.0', '<')) {
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
    public function testBug20295(): void
    {
        $form = new HTML_QuickForm2('bug20295');
        $date = $form->addDate('aDate', null, array('format' => 'Ymd'))
                    ->setValue('today');

        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'aDate' => null
        )));

        $this->assertNull($date->getValue());
    }
}
?>