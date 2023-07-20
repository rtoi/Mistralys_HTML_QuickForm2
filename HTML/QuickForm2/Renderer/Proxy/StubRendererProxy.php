<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see \HTML\QuickForm2\Renderer\Proxy\StubRendererProxy
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Renderer\Proxy;

use HTML_QuickForm2_Renderer_Proxy;
use HTML_QuickForm2_Renderer_Stub;

/**
 * Concrete proxy for the {@see HTML_QuickForm2_Renderer_Stub}
 * renderer, making its public methods visible.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property HTML_QuickForm2_Renderer_Stub $_renderer
 */
class StubRendererProxy extends HTML_QuickForm2_Renderer_Proxy
{
    protected function __construct(HTML_QuickForm2_Renderer_Stub $renderer, array &$pluginClasses)
    {
        parent::__construct($renderer, $pluginClasses);
    }

    /**
     * Form errors if {@see \HTML_QuickForm2_Renderer::OPTION_GROUP_ERRORS} option is true.
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->_renderer->getErrors();
    }

    /**
     * Returns hidden elements' HTML if {@see \HTML_QuickForm2_Renderer::OPTION_GROUP_HIDDENS} option is true.
     * @return string[]
     */
    public function getHidden(): array
    {
        return $this->_renderer->getHidden();
    }

    /**
     * Checks whether form contains required elements.
     * @return bool
     */
    public function hasRequired(): bool
    {
        return $this->_renderer->hasRequired();
    }
}
