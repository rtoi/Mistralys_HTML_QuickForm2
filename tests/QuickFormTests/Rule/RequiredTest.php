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

namespace QuickFormTests\Rule;

use HTML_QuickForm2_InvalidArgumentException;
use HTML_QuickForm2_Node;
use HTML_QuickForm2_Rule;
use HTML_QuickForm2_Rule_Required;
use QuickFormTests\CaseClasses\QuickFormCase;

/**
 * Unit test for HTML_QuickForm2_Rule_Required class
 */
class RequiredTest extends QuickFormCase
{
    // region: _Tests

    public function testMakesElementRequired() : void
    {
        $mockNode = $this->getMockBuilder('HTML_QuickForm2_Node')
            ->onlyMethods($this->nodeAbstractMethods)
            ->getMock();

        $mockNode->addRule(new HTML_QuickForm2_Rule_Required($mockNode, 'element is required'));
        $this->assertTrue($mockNode->isRequired());
    }

    public function testMustBeFirstInChain() : void
    {
        $mockNode = $this->getMockBuilder('HTML_QuickForm2_Node')
            ->onlyMethods($this->nodeAbstractMethods)
            ->getMock();

        $rule = $mockNode->addRule(
            $this->getMockBuilder('HTML_QuickForm2_Rule')
                ->onlyMethods(array('validateOwner'))
                ->setConstructorArgs(array($mockNode, 'some message'))
                ->getMock()
        );

        $this->expectExceptionCode(HTML_QuickForm2_Rule::ERROR_CANNOT_ADD_REQUIRED_RULE);
        $rule->and_(new HTML_QuickForm2_Rule_Required($mockNode, 'element is required'));

        $this->expectExceptionCode(HTML_QuickForm2_Rule_Required::ERROR_CANNOT_ADD_RULE_TO_REQUIRED);
        $rule->or_(new HTML_QuickForm2_Rule_Required($mockNode, 'element is required'));
    }

    public function testCannotAppendWithOr_() : void
    {
        $mockNode = $this->getMockBuilder(HTML_QuickForm2_Node::class)
            ->onlyMethods($this->nodeAbstractMethods)
            ->getMock();

        $required = new HTML_QuickForm2_Rule_Required($mockNode, 'element is required');

        $this->expectExceptionCode(HTML_QuickForm2_Rule_Required::ERROR_CANNOT_ADD_RULE_TO_REQUIRED);

        $required->or_(
            $this->getMockBuilder(HTML_QuickForm2_Rule::class)
                ->onlyMethods(array('validateOwner'))
                ->setConstructorArgs(array($mockNode, 'some message'))
                ->getMock()
        );
    }

    /**
     * @link http://pear.php.net/bugs/18133
     */
    public function testCannotHaveEmptyMessage() : void
    {
        $this->expectException(HTML_QuickForm2_InvalidArgumentException::class);

        $mockNode = $this->getMockBuilder(HTML_QuickForm2_Node::class)
            ->onlyMethods($this->nodeAbstractMethods)
            ->getMock();

        new HTML_QuickForm2_Rule_Required($mockNode);
    }

    public function testWillUseDefaultMessage() : void
    {
        $mockNode = $this->getMockBuilder(HTML_QuickForm2_Node::class)
            ->onlyMethods($this->nodeAbstractMethods)
            ->getMock();

        HTML_QuickForm2_Rule_Required::setDefaultMessage('Default message');

        $rule = new HTML_QuickForm2_Rule_Required($mockNode);

        $this->assertSame('Default message', $rule->getMessage());

        $rule->setMessage('Overridden default');

        $this->assertSame('Overridden default', $rule->getMessage());
    }

    // endregion

    // region: Support methods

    protected array $nodeAbstractMethods = array(
        array(HTML_QuickForm2_Node::class, 'updateValue')[1],
        array(HTML_QuickForm2_Node::class, 'getId')[1],
        array(HTML_QuickForm2_Node::class, 'getName')[1],
        array(HTML_QuickForm2_Node::class, 'getType')[1],
        array(HTML_QuickForm2_Node::class, 'getRawValue')[1],
        array(HTML_QuickForm2_Node::class, 'setId')[1],
        array(HTML_QuickForm2_Node::class, 'setName')[1],
        array(HTML_QuickForm2_Node::class, 'setValue')[1],
        array(HTML_QuickForm2_Node::class, '__toString')[1],
        array(HTML_QuickForm2_Node::class, 'getJavascriptValue')[1],
        array(HTML_QuickForm2_Node::class, 'getJavascriptTriggers')[1],
        array(HTML_QuickForm2_Node::class, 'render')[1]
    );

    protected function setUp() : void
    {
        parent::setUp();

        // Ensure the default message is empty
        HTML_QuickForm2_Rule_Required::setDefaultMessage('');
    }

    // endregion
}
