<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Renderer_Plugin;

/**
 * Another plugin for FakeRenderer
 */
class TestRendererGoodbyePlugin extends HTML_QuickForm2_Renderer_Plugin
{
    public function sayGoodbye() : string
    {
        return sprintf('Goodbye, %s!', $this->renderer->name);
    }
}
