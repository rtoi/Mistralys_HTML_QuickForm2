<?php

declare(strict_types=1);

namespace QuickFormTests\CustomClasses;

use HTML_QuickForm2_Renderer_Plugin;

/**
 * Yet another plugin for FakeRenderer with duplicate method name
 */
class TestRendererAnotherHelloPlugin extends HTML_QuickForm2_Renderer_Plugin
{
    public function sayHello() : string
    {
        return 'Hello, world!';
    }
}
