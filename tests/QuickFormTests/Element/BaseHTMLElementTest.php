<?php

declare(strict_types=1);

namespace QuickFormTests\Element;

use BaseHTMLElement;
use QuickFormTests\CaseClasses\QuickFormCase;
use stdClass;

class BaseHTMLElementTest extends QuickFormCase
{
    // region: _Tests

    public function test_getAttributesStringHandlesAllTypes() : void
    {
        $this->assertAttributeValueConvertedTo(null, '');
        $this->assertAttributeValueConvertedTo('', '');
        $this->assertAttributeValueConvertedTo(42, '42');
        $this->assertAttributeValueConvertedTo(3.14, '3.14');
        $this->assertAttributeValueConvertedTo('foo', 'foo');
        $this->assertAttributeValueConvertedTo(true, 'true');
        $this->assertAttributeValueConvertedTo(false, 'false');

        $this->assertAttributeValueIgnored(array('foo', 'bar'));
        $this->assertAttributeValueIgnored(new stdClass());
    }

    public function test_getAttributesStringSanitizesNames() : void
    {
        $this->assertAttributeNameSanitizedTo('with space', 'with_space');
        $this->assertAttributeNameSanitizedTo('with > greater', 'with___greater');
        $this->assertAttributeNameSanitizedTo('with " double-quote', 'with__double-quote');
        $this->assertAttributeNameSanitizedTo("with ' apostrophe", 'with__apostrophe');
        $this->assertAttributeNameSanitizedTo('with = equals', 'with___equals');
    }

    // endregion

    // region: Support methods

    private function assertAttributeValueIgnored($value) : void
    {
        $this->assertEquals(
            '',
            BaseHTMLElement::getAttributesString(array('attrib' => $value))
        );
    }

    /**
     * @param mixed $value
     * @param string $expected
     * @return void
     */
    private function assertAttributeValueConvertedTo($value, string $expected) : void
    {
        $this->assertEquals(
            ' value="'.$expected.'"',
            BaseHTMLElement::getAttributesString(array('value' => $value))
        );
    }

    private function assertAttributeNameSanitizedTo(string $name, string $expected) : void
    {
        $this->assertEquals(
            ' '.$expected.'=""',
            BaseHTMLElement::getAttributesString(array($name => ''))
        );
    }

    // endregion
}
