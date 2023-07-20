<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Renderer_Plugin;

/**
 * Plugin for FakeRenderer
 */
class TestRendererHelloPlugin extends HTML_QuickForm2_Renderer_Plugin
{
    public function sayHello() : string
    {
        return sprintf('Hello, %s!', $this->renderer->name);
    }
}
