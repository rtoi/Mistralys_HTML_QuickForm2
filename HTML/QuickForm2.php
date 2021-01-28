<?php
/**
 * Class representing a HTML form
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to BSD 3-Clause License that is bundled
 * with this package in the file LICENSE and available at the URL
 * https://raw.githubusercontent.com/pear/HTML_QuickForm2/trunk/docs/LICENSE
 *
 * @category  HTML
 * @package   HTML_QuickForm2
 * @author    Alexey Borzov <avb@php.net>
 * @author    Bertrand Mansion <golgote@mamasam.com>
 * @copyright 2006-2020 Alexey Borzov <avb@php.net>, Bertrand Mansion <golgote@mamasam.com>
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      https://pear.php.net/package/HTML_QuickForm2
 */

/**
 * Class representing a HTML form
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2 extends HTML_QuickForm2_Container
{
   /**
    * Data sources providing values for form elements
    * @var array
    */
    protected $datasources = array();

   /**
    * We do not allow setting "method" and "id" other than through constructor
    * @var array
    */
    protected $watchedAttributes = array('id', 'method');
    
   /**
    * The event handler instance for the form
    * @var HTML_QuickForm2_EventHandler
    */
    protected $eventHandler;
    
    protected $dataReason;
    
   /**
    * Class constructor, form's "id" and "method" attributes can only be set here
    *
    * @param string       $id          "id" attribute of <form> tag
    * @param string       $method      HTTP method used to submit the form
    * @param string|array $attributes  Additional HTML attributes
    *                                  (either a string or an array)
    * @param bool         $trackSubmit Whether to track if the form was submitted
    *                                  by adding a special hidden field
    */
    public function __construct(
        $id, $method = 'post', $attributes = null, $trackSubmit = true
    ) {
        $this->eventHandler = new HTML_QuickForm2_EventHandler($this);
        $method      = ('GET' == strtoupper($method))? 'get': 'post';
        $trackSubmit = empty($id) ? false : $trackSubmit;
        
        $this->attributes = array_merge(
            self::prepareAttributes($attributes),
            array('method' => $method)
        );
        
        parent::setId(empty($id) ? null : $id);
        
        if(!isset($this->attributes['action'])) {
            $this->attributes['action'] = $_SERVER['PHP_SELF'];
        }

        $trackVarFound = isset($_REQUEST['_qf__' . $id]);
        $getNotEmpty = 'get' == $method && !empty($_GET);
        $postNotEmpty = 'post' == $method && (!empty($_POST) || !empty($_FILES));
        
        // automatically add the superglobals datasource to access
        // submitted form values, if data is present.
        if($trackSubmit && $trackVarFound || !$trackSubmit && ($getNotEmpty || $postNotEmpty))
        {
            $this->addDataSource(new HTML_QuickForm2_DataSource_SuperGlobal($method));
        }

        $this->dataReason = array(
            'trackVarFound' => $trackVarFound,
            'getNotEmpty' => $getNotEmpty,
            'postNotEmpty' => $postNotEmpty
        );
        
        if($trackSubmit) {
            $this->appendChild(HTML_QuickForm2_Factory::createElement(
                'hidden', '_qf__' . $id, array('id' => 'qf:' . $id)
            ));
        }
        
        $this->addFilter(array($this, 'skipInternalFields'));
    }
    
    public function getDataReason()
    {
        return $this->dataReason;
    }

    protected function onAttributeChange($name, $value = null)
    {
        throw new HTML_QuickForm2_InvalidArgumentException(
            'Attribute \'' . $name . '\' is read-only'
        );
    }

    protected function setContainer(HTML_QuickForm2_Container $container = null)
    {
        throw new HTML_QuickForm2_Exception('Form cannot be added to container');
    }

    public function setId($id = null)
    {
        throw new HTML_QuickForm2_InvalidArgumentException(
            "Attribute 'id' is read-only"
        );
    }


   /**
    * Adds a new data source to the form
    *
    * @param HTML_QuickForm2_DataSource $datasource Data source
    */
    public function addDataSource(HTML_QuickForm2_DataSource $datasource)
    {
        $this->datasources[] = $datasource;
        $this->updateValue();
    }

   /**
    * Replaces the list of form's data sources with a completely new one
    *
    * @param array $datasources A new data source list
    *
    * @throws   HTML_QuickForm2_InvalidArgumentException    if given array
    *               contains something that is not a valid data source
    */
    public function setDataSources(array $datasources)
    {
        foreach ($datasources as $ds) {
            if (!$ds instanceof HTML_QuickForm2_DataSource) {
                throw new HTML_QuickForm2_InvalidArgumentException(
                    'Array should contain only DataSource instances'
                );
            }
        }
        $this->datasources = $datasources;
        $this->updateValue();
    }

   /**
    * Returns the list of data sources attached to the form
    *
    * @return   array
    */
    public function getDataSources()
    {
        return $this->datasources;
    }

    public function getType()
    {
        return 'form';
    }

    public function setValue($value)
    {
        throw new HTML_QuickForm2_Exception('Not implemented');
    }

   /**
    * Tells whether the form was already submitted
    *
    * This is a shortcut for checking whether there is an instance of Submit
    * data source in the list of form data sources
    *
    * @return bool
    */
    public function isSubmitted()
    {
        foreach ($this->datasources as $ds) {
            if ($ds instanceof HTML_QuickForm2_DataSource_Submit) {
                return true;
            }
        }
        return false;
    }

   /**
    * Performs the server-side validation
    *
    * @return   boolean Whether all form's elements are valid
    */
    public function validate()
    {
        return $this->isSubmitted() && parent::validate();
    }

   /**
    * Renders the form using the given renderer
    *
    * @param HTML_QuickForm2_Renderer $renderer
    *
    * @return   HTML_QuickForm2_Renderer
    */
    public function render(HTML_QuickForm2_Renderer $renderer)
    {
        $this->preRender();
        
        $renderer->startForm($this);
        $renderer->getJavascriptBuilder()->setFormId($this->getId());
        foreach ($this as $element) {
            $element->render($renderer);
        }
        $this->renderClientRules($renderer->getJavascriptBuilder());
        $renderer->finishForm($this);
        return $renderer;
    }
    
    /**
     * Filter for form's getValue() removing internal fields' values from the array
     *
     * @param array $value
     *
     * @return array
     * @link http://pear.php.net/bugs/bug.php?id=19403
     */
    protected function skipInternalFields($value)
    {
        foreach (array_keys($value) as $key) {
            if ('_qf' === substr($key, 0, 3)) {
                unset($value[$key]);
            }
        }
        return $value;
    }
   
   /**
    * Retrieves the event handler instance of the form,
    * which is used to manage form events.
    * 
    * @return HTML_QuickForm2_EventHandler
    */
    public function getEventHandler()
    {
        return $this->eventHandler;
    }
}
