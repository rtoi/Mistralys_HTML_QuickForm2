<?php

declare(strict_types=1);

namespace QuickFormTests\CaseClasses;

use HTML_QuickForm2_Node;
use HTML_QuickForm2_Rule_Required;
use PHPUnit\Framework\TestCase;

abstract class QuickFormCase extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        // Ensure the default message is empty
        HTML_QuickForm2_Rule_Required::setDefaultMessage('');
    }

    /**
     * @var string[]
     */
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
}
