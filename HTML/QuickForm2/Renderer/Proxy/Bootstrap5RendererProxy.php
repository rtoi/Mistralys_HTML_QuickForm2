<?php

declare(strict_types=1);

namespace HTML\QuickForm2\Renderer\Proxy;

use HTML_QuickForm2_Renderer_Bootstrap5;
use HTML_QuickForm2_Renderer_Proxy;

/**
 * Concrete proxy for the {@see HTML_QuickForm2_Renderer_Bootstrap5}
 * renderer, making its public methods visible.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property HTML_QuickForm2_Renderer_Bootstrap5 $_renderer
 */
class Bootstrap5RendererProxy extends HTML_QuickForm2_Renderer_Proxy
{
    protected function __construct(HTML_QuickForm2_Renderer_Bootstrap5 $renderer, array &$pluginClasses)
    {
        parent::__construct($renderer, $pluginClasses);
    }

    public function renderCDNIncludes() : string
    {
        return $this->_renderer->renderCDNIncludes();
    }
}
