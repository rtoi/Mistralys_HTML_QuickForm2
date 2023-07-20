<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Container_Group;
use HTML_QuickForm2_Node;
use HTML_QuickForm2_Renderer;

/**
 * An "implementation" of renderer, to be able to create an instance
 */
class TestRendererImpl extends HTML_QuickForm2_Renderer
{
    public const RENDERER_ID = 'test-impl';

    public string $name = 'fake';

    public function getID() : string
    {
        return self::RENDERER_ID;
    }

    public function renderElement(HTML_QuickForm2_Node $element) : void
    {
    }

    public function renderHidden(HTML_QuickForm2_Node $element) : void
    {
    }

    public function startForm(HTML_QuickForm2_Node $form) : void
    {
    }

    public function finishForm(HTML_QuickForm2_Node $form) : void
    {
    }

    public function startContainer(HTML_QuickForm2_Node $container) : void
    {
    }

    public function finishContainer(HTML_QuickForm2_Node $container) : void
    {
    }

    public function startGroup(HTML_QuickForm2_Container_Group $group) : void
    {
    }

    public function finishGroup(HTML_QuickForm2_Container_Group $group) : void
    {
    }

    public function reset() : self
    {
        return $this;
    }
}
