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
 * @copyright 2006-2019 Alexey Borzov <avb@php.net>, Bertrand Mansion <golgote@mamasam.com>
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      https://pear.php.net/package/HTML_QuickForm2
 */

use PHPUnit\Framework\TestCase;

/** Sets up includes */
require_once dirname(__FILE__) . '/TestHelper.php';

class FormRule extends HTML_QuickForm2_Rule
{
    protected function validateOwner()
    {
        return false;
    }

    protected function setOwnerError()
    {
        $this->owner->getElementById('foo')->setError('an error message');
    }
}


/**
 * Unit test for HTML_QuickForm2 class
 */
class HTML_QuickForm2Test extends TestCase
{
    protected function setUp() : void
    {
        $_REQUEST = array(
            '_qf__track' => ''
        );
        $_GET = array(
            'key' => 'value'
        );
        $_POST = array();
    }

    public function testTrackSubmit()
    {
        $tests = array(
            array(
                'label' => 'Datasource should be present because tracking var is found.',
                'method' => 'post',
                'id' => 'track',
                'tracking' => true,
                'count' => 1,
                'trackVarFound' => true,
                'getNotEmpty' => false,
                'postNotEmpty' => false
            ),
            array(
                'label' => 'Datasource should not be present because tracking var is disabled, and POST is empty.',
                'method' => 'post',
                'id' => 'track',
                'tracking' => false,
                'count' => 0,
                'trackVarFound' => true,
                'getNotEmpty' => false,
                'postNotEmpty' => false
            ),
            array(
                'label' => 'Datasource should be present because tracking var is found, regardless of method.',
                'method' => 'get',
                'id' => 'track',
                'tracking' => true,
                'count' => 1,
                'trackVarFound' => true,
                'getNotEmpty' => true,
                'postNotEmpty' => false
            ),
            array(
                'label' => 'Datasource should be present because GET is not empty, even though tracking var is disabled.',
                'method' => 'get',
                'id' => 'track',
                'tracking' => false,
                'count' => 1,
                'trackVarFound' => true,
                'getNotEmpty' => true,
                'postNotEmpty' => false
            ),
        );
        
        $number = 1;
        foreach($tests as $def)
        {
            $form = new HTML_QuickForm2($def['id'], $def['method'], null, $def['tracking']);

            $descr = 'Test #'.$number.' - '.strtoupper($def['method']).': '.$def['label'];
            
            $this->assertEquals(
                $def['count'], 
                count($form->getDataSources()), 
                'Datsource count does not match. '.$descr
            );
            
            $data = $form->getDataReason();
            $this->assertEquals($def['trackVarFound'], $data['trackVarFound'], 'Tracking var should have been found. '.$descr);
            $this->assertEquals($def['postNotEmpty'], $data['postNotEmpty'], 'Post should be empty. '.$descr);
            $this->assertEquals($def['getNotEmpty'], $data['getNotEmpty'], 'Get should not be empty. '.$descr);
            
            $number++;
        }
    }
    
    public function testConstructorSetsIdAndMethod()
    {
        $form1 = new HTML_QuickForm2(null);
        $this->assertEquals('post', $form1->getAttribute('method'));
        $this->assertNotEquals(0, strlen($form1->getAttribute('id')));

        $form2 = new HTML_QuickForm2('foo', 'get');
        $this->assertEquals('get', $form2->getAttribute('method'));
        $this->assertEquals('foo', $form2->getAttribute('id'));

        $form3 = new HTML_QuickForm2('bar', 'post', array('method' => 'get', 'id' => 'whatever'));
        $this->assertEquals('post', $form3->getAttribute('method'));
        $this->assertEquals('bar', $form3->getAttribute('id'));
    }

    public function testConstructorSetsDefaultAction()
    {
        $form1 = new HTML_QuickForm2('test');
        $this->assertEquals($_SERVER['PHP_SELF'], $form1->getAttribute('action'));

        $form2 = new HTML_QuickForm2('test2', 'post', array('action' => '/foobar.php'));
        $this->assertEquals('/foobar.php', $form2->getAttribute('action'));
    }

    public function testIdIsReadonly()
    {
        $form = new HTML_QuickForm2('foo', 'get');

        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);
        
        $form->removeAttribute('id');
    }
    
    public function testMethodIsReadonly()
    {
        $form = new HTML_QuickForm2('foo', 'get');
        
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);
        
        $form->setAttribute('method', 'post');
    }
    
    public function testSetIdIsReadonly()
    {
        $form = new HTML_QuickForm2('foo', 'get');
        
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);
        
        $form->setId('newId');
    }

    public function testCannotAddToContainer()
    {
        $form1 = new HTML_QuickForm2('form1');
        $form2 = new HTML_QuickForm2('form2');

        $this->expectException(HTML_QuickForm2_Exception::class);
        
        $form1->appendChild($form2);
    }

    public function testSetDataSources()
    {
        $ds1 = new HTML_QuickForm2_DataSource_Array(array('key' => 'value'));
        $ds2 = new HTML_QuickForm2_DataSource_Array(array('another key' => 'foo'));

        $form = new HTML_QuickForm2('dstest');
        $this->assertEquals(0, count($form->getDataSources()));
        $form->addDataSource($ds2);
        $this->assertEquals(1, count($form->getDataSources()));

        $form->setDataSources(array($ds1, $ds2));
        $this->assertEquals(2, count($form->getDataSources()));

        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);
        
        $form->setDataSources(array($ds1, 'bogus', $ds2));
    }

    public function testValidateChecksWhetherFormIsSubmitted()
    {
        $form1 = new HTML_QuickForm2('notrack', 'post');
        $this->assertFalse($form1->validate());

        $form2 = new HTML_QuickForm2('track', 'post');
        $this->assertTrue($form2->validate());
    }

    public function testFormRule()
    {
        $form = new HTML_QuickForm2('track', 'post');
        $foo = $form->addElement('text', 'foo', array('id' => 'foo'));
        $form->addRule(new FormRule($form));

        $this->assertFalse($form->validate());
        $this->assertEquals('an error message', $foo->getError());
    }

    /**
     * Do not return values for automatically added elements from getValue()
     * @link http://pear.php.net/bugs/bug.php?id=19403
     */
    public function testRequest19403()
    {
        $_POST = array('_qf__track' => '');
        $form  = new HTML_QuickForm2('track');

        $this->assertArrayHasKey('_qf__track', $form->getRawValue());
        $this->assertArrayNotHasKey('_qf__track', $form->getValue());
    }
}
?>
