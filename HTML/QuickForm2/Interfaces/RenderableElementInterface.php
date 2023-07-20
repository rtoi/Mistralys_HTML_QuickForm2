<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Interfaces;

use HTML\QuickForm2\Renderer\Proxy\ArrayRendererProxy;
use HTML\QuickForm2\Renderer\Proxy\CallbackRendererProxy;
use HTML_QuickForm2_Renderer;

interface RenderableElementInterface
{
    /**
     * Renders the element using the given renderer
     *
     * @param HTML_QuickForm2_Renderer $renderer
     * @return HTML_QuickForm2_Renderer
     */
    public function render(HTML_QuickForm2_Renderer $renderer): HTML_QuickForm2_Renderer;

    public function renderToArray(?ArrayRendererProxy $renderer=null) : array;

    public function renderWithCallback(?CallbackRendererProxy $renderer=null) : void;
}
