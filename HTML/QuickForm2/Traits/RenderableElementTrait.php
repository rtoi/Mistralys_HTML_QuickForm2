<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Traits;

use HTML\QuickForm2\Renderer\Proxy\ArrayRendererProxy;
use HTML\QuickForm2\Renderer\Proxy\CallbackRendererProxy;
use HTML_QuickForm2_Renderer;

trait RenderableElementTrait
{
    /**
     * Shortcut to use the array renderer to render the element.
     *
     * @param ArrayRendererProxy|null $renderer If null, a new instance of the array renderer will be created.
     * @return array{id:string,html:string,value:mixed,type:string,required:bool,frozen:bool}
     */
    public function renderToArray(?ArrayRendererProxy $renderer=null) : array
    {
        if($renderer === null) {
            $renderer = HTML_QuickForm2_Renderer::createArray();
        }

        $this->render($renderer);

        return $renderer->toArray();
    }

    /**
     * Shortcut to use the callback renderer to render the element.
     *
     * @param CallbackRendererProxy|null $renderer If null, a new instance of the callback renderer will be created.
     * @return void
     */
    public function renderWithCallback(?CallbackRendererProxy $renderer=null) : void
    {
        if($renderer === null) {
            $renderer = HTML_QuickForm2_Renderer::createCallback();
        }

        $this->render($renderer);
    }
}
