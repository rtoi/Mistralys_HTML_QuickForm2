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

use HTML_QuickForm2;
use HTML_QuickForm2_Element_InputFile;
use HTML_QuickForm2_Element_InputFile_Upload;
use HTML_QuickForm2_InvalidArgumentException;
use HTML_QuickForm2_MessageProvider;
use HTML_QuickForm2_Renderer;
use QuickFormTests\CaseClasses\QuickFormCase;

/**
 * @package QuickForm Tests
 * @see HTML_QuickForm2_Element_InputFile
 */
class InputFileTest extends QuickFormCase
{
    // region: Support methods

    protected function setUp() : void
    {
        $_FILES = array(
            'foo' => array(
                'name' => 'file.doc',
                'tmp_name' => '/tmp/nothing',
                'type' => 'text/plain',
                'size' => 1234,
                'error' => UPLOAD_ERR_OK
            ),
            'toobig' => array(
                'name' => 'ahugefile.zip',
                'tmp_name' => '',
                'type' => '',
                'size' => 0,
                'error' => UPLOAD_ERR_FORM_SIZE
            ),
            'local' => array(
                'name' => 'nasty-trojan.exe',
                'tmp_name' => '',
                'type' => '',
                'size' => 0,
                'error' => UPLOAD_ERR_CANT_WRITE
            )
        );
        $_POST = array(
            'MAX_FILE_SIZE' => '987654'
        );
    }

    public static function callbackMessageProvider() : string
    {
        return "A nasty error happened!";
    }

    // endregion

    // region: _Tests

    public function testCannotBeFrozen() : void
    {
        $upload = new HTML_QuickForm2_Element_InputFile('foo');
        $this->assertFalse($upload->isFreezable());
        $this->assertFalse($upload->toggleFrozen(true));
        $this->assertFalse($upload->toggleFrozen());
    }

    public function testSetValueFromSubmitDataSource() : void
    {
        $form = new HTML_QuickForm2('upload', 'post', null, false);
        $foo = $form->appendChild(new HTML_QuickForm2_Element_InputFile('foo'));
        $bar = $form->appendChild(new HTML_QuickForm2_Element_InputFile('bar'));

        $this->assertNull($bar->getValue());
        $this->assertEquals(array(
            'name' => 'file.doc',
            'tmp_name' => '/tmp/nothing',
            'type' => 'text/plain',
            'size' => 1234,
            'error' => UPLOAD_ERR_OK
        ), $foo->getValue());
    }

    public function testGetUploadInstanceValidatesFile() : void
    {
        $form = new HTML_QuickForm2('upload', 'post', null, false);
        $foo = new HTML_QuickForm2_Element_InputFile('foo');
        $form->appendChild($foo);

        $upload = $foo->getUpload();

        // This upload is invalid, because it's only simulated.
        $this->assertFalse($upload->isValid());

        $this->expectExceptionCode(HTML_QuickForm2_Element_InputFile_Upload::ERROR_CANNOT_RETRIEVE_INVALID_UPLOAD_DATA);

        $upload->getName();
    }

    public function testBuiltinValidation() : void
    {
        $form = new HTML_QuickForm2('upload', 'post', null, false);
        $form->appendChild(new HTML_QuickForm2_Element_InputFile('foo'));
        $this->assertTrue($form->validate());

        $toobig = $form->appendChild(new HTML_QuickForm2_Element_InputFile('toobig'));
        $this->assertFalse($form->validate());
        $this->assertStringContainsString('987654', $toobig->getError());
    }

    public function testInvalidMessageProvider() : void
    {
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);

        new HTML_QuickForm2_Element_InputFile('invalid', null, array('messageProvider' => array()));
    }

    public function testAddAccept() : void
    {
        $el = new HTML_QuickForm2_Element_InputFile('foo');
        $el->addAccept('text/plain');

        $this->assertSame('text/plain', $el->getAccept());
    }

    public function testAddAccepts() : void
    {
        $el = new HTML_QuickForm2_Element_InputFile('foo');
        $el->addAccepts(array('text/plain', 'image/png', 'application/json'));

        // Accept mimes are sorted alphabetically
        $this->assertSame('application/json,image/png,text/plain', $el->getAccept());
        $this->assertSame(array('application/json', 'image/png', 'text/plain'), $el->getAcceptMimes());
    }

    /**
     * A file upload element must be attached to a form to be
     * rendered or submitted, or to fetch its value.
     */
    public function testFormIsRequiredToRender() : void
    {
        $el = new HTML_QuickForm2_Element_InputFile('foo');

        $this->expectExceptionCode(HTML_QuickForm2_Element_InputFile::ERROR_ELEMENT_HAS_NO_FORM);

        $el->renderToArray();
    }

    public function testCallbackMessageProvider() : void
    {
        $form = new HTML_QuickForm2('upload', 'post', null, false);
        $upload = $form->addFile('local', array(), array(
            'messageProvider' => array(__CLASS__, 'callbackMessageProvider')
        ));
        $this->assertFalse($form->validate());
        $this->assertEquals('A nasty error happened!', $upload->getError());
    }

    public function testObjectMessageProvider() : void
    {
        $mockProvider = $this
            ->getMockBuilder(HTML_QuickForm2_MessageProvider::class)
            ->onlyMethods(array('get'))
            ->getMock();

        $mockProvider
            ->expects($this->once())
            ->method('get')
            ->willReturn('A nasty error happened!');

        $form = new HTML_QuickForm2('upload', 'post', null, false);
        $upload = $form->addFile('local', array(), array(
            'messageProvider' => $mockProvider
        ));

        $this->assertFalse($form->validate());
        $this->assertEquals('A nasty error happened!', $upload->getError());
    }

    /**
     * File should check that the form has POST method, set enctype to multipart/form-data
     * @see http://pear.php.net/bugs/bug.php?id=16807
     */
    public function testRequest16807() : void
    {
        $form = new HTML_QuickForm2('broken1', 'get');

        $upload = $form->addFile('upload', array('id' => 'upload'));

        $this->expectExceptionCode(HTML_QuickForm2::ERROR_MULTIPART_REQUIRES_POST);

        $upload->getValue();
    }

    /**
     * Ensure that the form action is checked on retrieving the value
     */
    public function testRequest16807_2() : void
    {
        $form = new HTML_QuickForm2('broken2', 'get');

        /* HTML_QuickForm2_Container_Group */
        $group = $form->addElement('group', 'fileGroup');

        $upload = $group->addFile('upload', array('id' => 'upload'));

        $this->expectExceptionCode(HTML_QuickForm2::ERROR_MULTIPART_REQUIRES_POST);

        $upload->getValue();
    }

    /**
     * Ensure that the form action is checked on render
     */
    public function testRequest16807_3() : void
    {
        $form = new HTML_QuickForm2('broken2', 'get');

        $form->addFile('upload', array('id' => 'upload'));

        $this->expectExceptionCode(HTML_QuickForm2::ERROR_MULTIPART_REQUIRES_POST);

        $form->renderToArray();
    }

    public function testRequest16807_4() : void
    {
        $post = new HTML_QuickForm2('okform', 'post');

        $this->assertNull($post->getAttribute('enctype'));

        $upload = $post->addFile('upload');

        // the check is done whenever the value is accessed,
        // or the form is validated / rendered.
        $upload->getValue();

        $this->assertEquals('multipart/form-data', $post->getAttribute('enctype'));
        $this->assertTrue($post->isMultiPart());
    }

    // endregion
}
