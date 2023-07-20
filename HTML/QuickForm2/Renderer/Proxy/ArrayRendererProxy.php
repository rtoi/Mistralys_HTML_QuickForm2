<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see \HTML\QuickForm2\Renderer\Proxy\ArrayRendererProxy
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Renderer\Proxy;

use HTML_QuickForm2_Renderer_Array;
use HTML_QuickForm2_Renderer_Proxy;

/**
 * Concrete proxy for the {@see HTML_QuickForm2_Renderer_Array}
 * renderer, making its public methods visible.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property HTML_QuickForm2_Renderer_Array $_renderer
 */
class ArrayRendererProxy extends HTML_QuickForm2_Renderer_Proxy
{
    protected function __construct(HTML_QuickForm2_Renderer_Array $renderer, array &$pluginClasses)
    {
        parent::__construct($renderer, $pluginClasses);
    }

    public function toArray() : array
    {
        return $this->_renderer->toArray();
    }

    /**
     * Sets a style for element rendering
     *
     * "Style" is some information that is opaque to Array Renderer but may be
     * of use to e.g. template engine that receives the resultant array.
     *
     * @param string|array $idOrStyles Element id or array ('element id' => 'style')
     * @param mixed $style Element style if $idOrStyles is not an array
     * @return $this
     */
    public function setStyleForId($idOrStyles, $style = null) : self
    {
        $this->_renderer->setStyleForId($idOrStyles, $style);
        return $this;
    }

    public function setStaticLabels(bool $enabled) : self
    {
        $this->_renderer->setStaticLabels($enabled);
        return $this;
    }

    public function isStaticLabelsEnabled() : bool
    {
        return $this->_renderer->isStaticLabelsEnabled();
    }
}
