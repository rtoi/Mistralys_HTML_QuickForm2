<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see \HTML\QuickForm2\Renderer\Proxy\DefaultRendererProxy
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Renderer\Proxy;

use HTML_QuickForm2_Renderer_Default;
use HTML_QuickForm2_Renderer_Proxy;

/**
 * Concrete proxy for the {@see HTML_QuickForm2_Renderer_Default}
 * renderer, making its public methods visible.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property HTML_QuickForm2_Renderer_Default $_renderer
 */
class DefaultRendererProxy extends HTML_QuickForm2_Renderer_Proxy
{
    protected function __construct(HTML_QuickForm2_Renderer_Default $renderer, array &$pluginClasses)
    {
        parent::__construct($renderer, $pluginClasses);
    }

    public function setTemplateForClass(string $class, ?string $template): self
    {
        $this->_renderer->setTemplateForClass($class, $template);
        return $this;
    }

    public function setTemplateForId(string $id, ?string $template): self
    {
        $this->_renderer->setTemplateForId($id, $template);
        return $this;
    }

    public function setErrorTemplate(?string $prefix, ?string $separator, ?string $suffix): self
    {
        $this->_renderer->setErrorTemplate($prefix, $separator, $suffix);
        return $this;
    }

    public function setElementTemplateForGroupClass(string $groupClass, string $elementClass, ?string $template): self
    {
        $this->_renderer->setElementTemplateForGroupClass($groupClass, $elementClass, $template);
        return $this;
    }

    public function setElementTemplateForGroupId(string $groupId, string $elementClass, ?string $template): self
    {
        $this->_renderer->setElementTemplateForGroupId($groupId, $elementClass, $template);
        return $this;
    }
}
