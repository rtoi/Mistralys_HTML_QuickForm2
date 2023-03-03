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
 * Unit test for HTML_QuickForm2_Element class,
 */
class HTML_QuickForm2_ElementTest extends TestCase
{
    protected function setUp() : void
    {
        $_REQUEST = array(
            '_qf__form1' => ''
        );

        $_POST = array(
            'foo' => 'a value',
            'fooReborn' => 'another value'
        );
    }

    protected function tearDown() : void
    {
        BaseHTMLElement::setOption('id_force_append_index', true);
    }

    public function testCanSetName()
    {
        $obj = new HTML_QuickForm2_ElementImpl();
        $this->assertNotNull($obj->getName(), 'Elements should always have \'name\' attribute');

        $obj = new HTML_QuickForm2_ElementImpl('foo');
        $this->assertEquals('foo', $obj->getName());

        $this->assertSame($obj, $obj->setName('bar'));
        $this->assertEquals('bar', $obj->getName());

        $obj->setAttribute('name', 'baz');
        $this->assertEquals('baz', $obj->getName());
    }


    public function testCanSetId()
    {
        $obj = new HTML_QuickForm2_ElementImpl(null, array('id' => 'manual'));
        $this->assertEquals('manual', $obj->getId());

        $this->assertSame($obj, $obj->setId('another'));
        $this->assertEquals('another', $obj->getId());

        $obj->setAttribute('id', 'yet-another');
        $this->assertEquals('yet-another', $obj->getId());
    }


    public function testCanNotRemoveNameOrId()
    {
        $obj = new HTML_QuickForm2_ElementImpl('somename', array(), array('id' => 'someid'));
        try {
            $obj->removeAttribute('name');
        } catch (HTML_QuickForm2_InvalidArgumentException $e) {
            $this->assertMatchesRegularExpression('/Required attribute(.*)can not be removed/', $e->getMessage());
            try {
                $obj->removeAttribute('id');
            } catch (HTML_QuickForm2_InvalidArgumentException $e) {
                $this->assertMatchesRegularExpression('/Required attribute(.*)can not be removed/', $e->getMessage());
                return;
            }
        }
        $this->fail('Expected HTML_QuickForm2_InvalidArgumentException was not thrown');
    }


    public function testUniqueIdsGenerated()
    {
        $names = array(
            '', 'value', 'array[]', 'array[8]', 'array[60000]', 'array[20]',
            'array[name][]', 'bigger[name][5]', 'bigger[name][]', 'bigger[name][6]'
        );
        
        $usedIds = array();
        
        foreach($names as $name) 
        {
            $el = new HTML_QuickForm2_ElementImpl($name);
            $this->assertNotEquals('', $el->getId(), 'Should have an auto-generated \'id\' attribute');
            $usedIds[] = $el->getId();
            $this->assertContains($el->getId(), $usedIds);
            
            // Duplicate name...
            $el2 = new HTML_QuickForm2_ElementImpl($name);
            $this->assertNotContains($el2->getId(), $usedIds);
            $usedIds[] = $el2->getId();
        }
    }


    public function testManualIdsNotReused()
    {
        // use a unique element name for this test, to avoid conflicts
        // with other tests.
        $elName = 'grabby';
        
        $usedIds = array(
            $elName.'-0', $elName.'-2', $elName.'-bar-0', $elName.'-bar-2', $elName.'-baz-0-0'
        );
        $names = array(
            $elName, $elName.'[bar]', $elName.'[baz][]'
        );
        foreach ($usedIds as $id) {
            $elManual = new HTML_QuickForm2_ElementImpl($elName, array('id' => $id));
        }
        foreach ($names as $name) {
            $el = new HTML_QuickForm2_ElementImpl($name);
            $this->assertNotContains($el->getId(), $usedIds);
            $usedIds[] = $el->getId();
            // Duplicate name...
            $el2 = new HTML_QuickForm2_ElementImpl($name);
            $this->assertNotContains($el2->getId(), $usedIds);
            $usedIds[] = $el2->getId();
        }
    }

    public function testSetValueFromSubmitDatasource()
    {
        $form = new HTML_QuickForm2('form1');
        $elFoo = $form->appendChild(new HTML_QuickForm2_ElementImpl('foo'));
        $elBar = $form->appendChild(new HTML_QuickForm2_ElementImpl('bar'));

        $this->assertEquals('a value', $elFoo->getValue());
        $this->assertNull($elBar->getValue());
    }

    public function testDataSourcePriority()
    {
        $form = new HTML_QuickForm2('form1');
        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'foo' => 'new value',
            'bar' => 'default value'
        )));
        $elFoo = $form->appendChild(new HTML_QuickForm2_ElementImpl('foo'));
        $elBar = $form->appendChild(new HTML_QuickForm2_ElementImpl('bar'));

        $this->assertEquals('a value', $elFoo->getValue());
        $this->assertEquals('default value', $elBar->getValue());
    }

    public function testUpdateValueFromNewDataSource()
    {
        $form = new HTML_QuickForm2('form2');
        $el = $form->appendChild(new HTML_QuickForm2_ElementImpl('foo'));
        $this->assertNull($el->getValue());

        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'foo' => 'updated value'
        )));
        $this->assertEquals('updated value', $el->getValue());
    }

    public function testUpdateValueOnNameChange()
    {
        $form = new HTML_QuickForm2('form1');
        $elFoo = $form->appendChild(new HTML_QuickForm2_ElementImpl('foo'));
        $elFoo->setName('fooReborn');
        $this->assertEquals('another value', $elFoo->getValue());
    }

    public function testGenerateIdsWithoutIndexes()
    {
        BaseHTMLElement::setOption('id_force_append_index', false);

        $name = 'finno_' . mt_rand(0, 1000);
        $el = new HTML_QuickForm2_ElementImpl($name);
        $this->assertEquals($name, $el->getId());

        $el2 = new HTML_QuickForm2_ElementImpl($name . '[bar]');
        $this->assertEquals($name . '-bar', $el2->getId());
    }

    public function testUniqueIdsGeneratedWithoutIndexes()
    {
        BaseHTMLElement::setOption('id_force_append_index', false);

        $this->testUniqueIdsGenerated();
    }

    /**
     * Prevent generating ids like "0-0" for (grouped) elements named "0"
     * @see http://news.php.net/php.pear.general/31496
     */
    public function testGeneratedIdsShouldNotStartWithNumbers()
    {
        $el = new HTML_QuickForm2_ElementImpl('0');
        $this->assertDoesNotMatchRegularExpression('/^\d/', $el->getId());
    }

    /**
     * If data source contains explicitly provided null values, those should be used
     * @link http://pear.php.net/bugs/bug.php?id=20295
     */
    public function testBug20295()
    {
        $form = new HTML_QuickForm2('bug20295');
        $el = $form->appendChild(new HTML_QuickForm2_ElementImpl('foo'));
        $el->setValue('not empty');

        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'foo' => null
        )));
        $this->assertNull($el->getValue());
    }
}
?>
