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

namespace QuickFormTests;

use HTML_Quickform2_Renderer;
use HTML_QuickForm2_Renderer_Proxy;
use PHPUnit\Framework\TestCase;
use QuickFormTests\CustomClasses\TestRendererAnotherHelloPlugin;
use QuickFormTests\CustomClasses\TestRendererGoodbyePlugin;
use QuickFormTests\CustomClasses\TestRendererHelloPlugin;
use QuickFormTests\CustomClasses\TestRendererImpl;

/**
 * Unit test for HTML_QuickForm2_Renderer class
 */
class RendererTest extends TestCase
{
    public function testRegisterRenderer() : void
    {
        $type = 'fake' . random_int(0, mt_getrandmax());

        HTML_Quickform2_Renderer::register($type, TestRendererImpl::class);

        $renderer = HTML_Quickform2_Renderer::factory($type);
        $this->assertInstanceOf(HTML_QuickForm2_Renderer::class, $renderer);
    }

    public function testRegisterPlugin() : void
    {
        $type = 'fake' . random_int(0, mt_getrandmax());
        HTML_QuickForm2_Renderer::register($type, TestRendererImpl::class);
        HTML_QuickForm2_Renderer::registerPlugin($type, TestRendererHelloPlugin::class);

        $renderer = HTML_Quickform2_Renderer::factory($type);
        $methodRender = array(TestRendererImpl::class, 'renderElement');
        $methodHello = array(TestRendererHelloPlugin::class, 'sayHello');
        $methodGoodbye = array(TestRendererGoodbyePlugin::class, 'sayGoodbye');

        $this->assertIsCallable(array($renderer, $methodRender[1]));
        $this->assertIsCallable(array($renderer, $methodHello[1]));
        $this->assertIsCallable(array($renderer, $methodGoodbye[1]));

        $this->assertTrue($renderer->methodExists($methodRender[1]));
        $this->assertTrue($renderer->methodExists($methodHello[1]));
        $this->assertFalse($renderer->methodExists($methodGoodbye[1]));

        HTML_QuickForm2_Renderer::registerPlugin($type, TestRendererGoodbyePlugin::class);
        $this->assertTrue($renderer->methodExists($methodGoodbye[1]));

        $this->assertEquals('Hello, fake!', $renderer->sayHello());
        $this->assertEquals('Goodbye, fake!', $renderer->sayGoodbye());
    }

    public function testRegisterPluginOnlyOnce() : void
    {
        $type = 'fake' . random_int(0, mt_getrandmax());
        HTML_QuickForm2_Renderer::register($type, TestRendererImpl::class);
        HTML_QuickForm2_Renderer::registerPlugin($type, TestRendererHelloPlugin::class);

        $this->expectExceptionCode(HTML_QuickForm2_Renderer::ERROR_RENDERER_PLUGIN_ALREADY_REGISTERED);

        HTML_QuickForm2_Renderer::registerPlugin($type, TestRendererHelloPlugin::class);
    }

    public function testDuplicateMethodNamesDisallowed() : void
    {
        $type = 'fake' . random_int(0, mt_getrandmax());

        HTML_QuickForm2_Renderer::register($type, TestRendererImpl::class);
        HTML_QuickForm2_Renderer::registerPlugin($type, TestRendererHelloPlugin::class);
        HTML_QuickForm2_Renderer::registerPlugin($type, TestRendererAnotherHelloPlugin::class);

        $helloMethodA = array(TestRendererHelloPlugin::class, 'sayHello')[1];
        $helloMethodB = array(TestRendererAnotherHelloPlugin::class, 'sayHello')[1];

        $this->assertIsCallable(array(new TestRendererHelloPlugin(), $helloMethodA));
        $this->assertIsCallable(array(new TestRendererAnotherHelloPlugin(), $helloMethodB));

        $this->expectExceptionCode(HTML_QuickForm2_Renderer_Proxy::ERROR_DUPLICATE_METHOD);

        $renderer = HTML_Quickform2_Renderer::factory($type);
        $renderer->sayHello();
    }
}
