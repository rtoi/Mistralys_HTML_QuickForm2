<?php
/**
 * Class for handling form events.
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2006-2014, Alexey Borzov <avb@php.net>,
 *                          Bertrand Mansion <golgote@mamasam.com>,
 *                          Sebastian Mordziol <s.mordziol@mistralys.eu>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @subpackage EventHandler
 * @author   Sebastian Mordziol <s.mordziol@mistralys.eu>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://pear.php.net/package/HTML_QuickForm2
 */

/**
 * Class for handling form events.
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @subpackage EventHandler
 * @author   Sebastian Mordziol <s.mordziol@mistralys.eu>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_EventHandler
{
    public const ERROR_UNEXPECTED_EVENT_INSTANCE_TYPE = 131501;

    /**
    * @var HTML_QuickForm2
    */
    protected $form;
    
   /**
    * Container for all added event handling callbacks.
    * @var array
    */
    protected $handlers = array();
    
   /**
    * Known event names that can be used.
    * 
    * @var string[]
    */
    protected $eventNames = array(
        'NodeAdded'
    );
    
   /**
    * Counter for the event handler IDs.
    * @var integer
    */
    protected static $idCounter = 0;
    
    public function __construct(HTML_QuickForm2 $form)
    {
        $this->form = $form;
    }
    
   /**
    * Registers a callback for the NodeAdded event.
    * 
    * The callback gets two parameters, as shown in 
    * the following example:
    * 
    * <pre>
    * function callback_nodeAdded(HTML_QuickForm2_Event_NodeAdded $event, $params=array() { }
    * </pre>
    * 
    * @param callable $callback
    * @param array $params
    * @return int
    */
    public function onNodeAdded(callable $callback, $params=array())
    {
        return $this->addHandler('NodeAdded', $callback, $params);
    }
    
   /**
    * Adds an event handler for the specified event name,
    * and returns the handler ID. The ID can be used to
    * reference the handler again later, for example to
    * remove it again.
    * 
    * @param string $eventName
    * @param callable $callback
    * @param array $params Optional parameters that are passed on to the callback when the event is triggered
    * @return int
    */
    protected function addHandler($eventName, callable $callback, array $params=array())
    {
        // to make the class available as soon as the event has been registered
        $this->includeEventClass($eventName);
        
        if(!isset($this->handlers[$eventName])) {
            $this->handlers[$eventName] = array();
        }
        
        $id = $this->nextHandlerID();
        
        $this->handlers[$eventName][$id] = array(
            'callback' => $callback,
            'params' => $params
        );
        
        return $id;
    }
    
    protected function nextHandlerID()
    {
        self::$idCounter++;
        return self::$idCounter;
    }
    
   /**
    * Removes a handler by its ID. Has no effect
    * if no corresponding handler is found.
    * 
    * @param int $handlerID
    */
    public function removeHandler($handlerID): void
    {
        foreach($this->handlers as $name => $handlers) {
            if(isset($handlers[$handlerID])) {
                unset($this->handlers[$name][$handlerID]);
                break;
            }
        }
    }
    
   /**
    * Checks whether the specified event name is a valid, existing event name.
    * @param string $eventName
    * @return boolean
    */
    public function eventNameExists($eventName)
    {
        return in_array($eventName, $this->eventNames);
    }
    
   /**
    * Called whenever a new node is added to the form.
    * @param HTML_QuickForm2_Node $node
    * @return HTML_QuickForm2_Event_NodeAdded
    */
    public function triggerNodeAdded(HTML_QuickForm2_Node $node) : HTML_QuickForm2_Event_NodeAdded
    {
        $event = $this->triggerEvent(
            'NodeAdded', 
            array(
                'node' => $node
            )
        );

        if($event instanceof HTML_QuickForm2_Event_NodeAdded)
        {
            return $event;
        }

        throw new HTML_QuickForm2_Exception(
            'Unexpected event class type instance created.',
            self::ERROR_UNEXPECTED_EVENT_INSTANCE_TYPE
        );
    }
    
   /**
    * Triggers the specified event. Returns an event instance
    * regardless of whether there were any handlers registered
    * for it.
    *  
    * @param string $eventName
    * @param array $args
    * @return HTML_QuickForm2_Event
    */
    protected function triggerEvent($eventName, array $args=array())
    {
        $class = $this->includeEventClass($eventName);
        
        $event = new $class($args);
        
        if(isset($this->handlers[$eventName])) {
            foreach($this->handlers[$eventName] as $handler) {
                call_user_func($handler['callback'], $event, $handler['params']);
            }
        }
        
        return $event;
    }
    
   /**
    * Includes the event-specific class.
    * 
    * @param string $eventName
    * @return string The name of the event class
    */
    protected function includeEventClass($eventName)
    {
        $this->requireValidEvent($eventName);
        
        $className = 'HTML_QuickForm2_Event_'.$eventName;
        
        return $className;
    }
    
   /**
    * Throws an exception if the event name is not known.
    * @param string $eventName
    * @throws HTML_QuickForm2_InvalidEventException
    */
    protected function requireValidEvent($eventName)
    {
        if($this->eventNameExists($eventName)) {
            return; 
        }
        
        throw new HTML_QuickForm2_InvalidEventException(
            sprintf(
                'Invalid event name. Known events are [%s].',
                implode(', ', $this->eventNames)
            )
        );    
    }
}
