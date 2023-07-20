<?php
/**
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @see \HTML\QuickForm2\Renderer\Proxy\CallbackRendererProxy
 */

declare(strict_types=1);

namespace HTML\QuickForm2\Renderer\Proxy;

use HTML_QuickForm2_Renderer_Callback;
use HTML_QuickForm2_Renderer_Proxy;

/**
 * Concrete proxy for the {@see HTML_QuickForm2_Renderer_Callback}
 * renderer, making its public methods visible.
 *
 * @package HTML_QuickForm2
 * @subpackage Renderer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property HTML_QuickForm2_Renderer_Callback $_renderer
 */
class CallbackRendererProxy extends HTML_QuickForm2_Renderer_Proxy
{
    protected function __construct(HTML_QuickForm2_Renderer_Callback $renderer, array &$pluginClasses)
    {
        parent::__construct($renderer, $pluginClasses);
    }

    public function setLabelCallback(?callable $callback) : self
    {
        $this->_renderer->setLabelCallback($callback);
        return $this;
    }

    public function setRequiredNoteCallback(?callable $callback) : self
    {
        $this->_renderer->setRequiredNoteCallback($callback);
        return $this;
    }

    public function setHiddenGroupCallback(?callable $callback) : self
    {
        $this->_renderer->setHiddenGroupCallback($callback);
        return $this;
    }

    public function setElementCallbackForGroupId(string $id, string $elementClass, ?callable $callback) : self
    {
        $this->_renderer->setElementCallbackForGroupId($id, $elementClass, $callback);
        return $this;
    }

    public function setElementCallbackForGroupClass(string $class, string $elementClass, ?callable $callback) : self
    {
        $this->_renderer->setElementCallbackForGroupClass($class, $elementClass, $callback);
        return $this;
    }

    public function setErrorGroupCallback(?callable $callback) : self
    {
        $this->_renderer->setErrorGroupCallback($callback);
        return $this;
    }

    public function setCallbackForClass(string $class, ?callable $callback) : self
    {
        $this->_renderer->setCallbackForClass($class, $callback);
        return $this;
    }

    public function setCallbackForId(string $id, ?callable $callback) : self
    {
        $this->_renderer->setCallbackForId($id, $callback);
        return $this;
    }
}
