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
 * Class representing an HTML form
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
    public const ERROR_CANNOT_ADD_FORM_TO_CONTAINER = 139401;
    public const ERROR_ATTRIBUTE_IS_READONLY = 139402;
    public const ERROR_DATA_SOURCES_ARRAY_INVALID = 139403;
    public const ERROR_NOT_IMPLEMENTED = 139404;

    /**
    * Data sources providing values for form elements
    * @var HTML_QuickForm2_DataSource[]
    */
    protected array $datasources = array();

   /**
    * We do not allow setting "method" and "id" other than through constructor
    * @var string[]
    */
    protected array $watchedAttributes = array('id', 'method');

    /**
     * @var array{trackVarFound:bool,getNotEmpty:bool,postNotEmpty:bool}
     */
    protected array $dataReason;

    /**
     * The event handler instance for the form
     * @var HTML_QuickForm2_EventHandler
     */
    protected HTML_QuickForm2_EventHandler $eventHandler;

   /**
    * Class constructor, form's "id" and "method" attributes can only be set here
    *
    * @param string|NULL $id "id" attribute of <form> tag
    * @param string $method HTTP method used to submit the form
    * @param string|array<int|string,string|int|float|Stringable|NULL>|NULL $attributes  Additional HTML attributes
    *                                  (either a string or an array)
    * @param bool         $trackSubmit Whether to track if the form was submitted
    *                                  by adding a special hidden field
    */
    public function __construct(
        ?string $id,
        string $method = 'post',
        $attributes = null,
        bool $trackSubmit = true
    ) {

        // NOTE: We are not calling the parent constructor
        // to be able to initialize the attributes and ID
        // the way we want.

        $this->eventHandler = new HTML_QuickForm2_EventHandler($this);
        $method      = ('GET' === strtoupper($method))? 'get': 'post';
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
        $getNotEmpty = 'get' === $method && !empty($_GET);
        $postNotEmpty = 'post' === $method && (!empty($_POST) || !empty($_FILES));
        
        // automatically add the superglobals datasource to access
        // submitted form values, if data is present.
        if(($trackSubmit && $trackVarFound) || (!$trackSubmit && ($getNotEmpty || $postNotEmpty)))
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

    /**
     * @return array{trackVarFound:bool,getNotEmpty:bool,postNotEmpty:bool}
     */
    public function getDataReason() : array
    {
        return $this->dataReason;
    }

    protected function onAttributeChange(string $name, $value = null) : void
    {
        throw self::exceptionAttributeReadonly($name);
    }

    /**
     * @param HTML_QuickForm2_Container|null $container
     * @return void
     * @throws HTML_QuickForm2_Exception {@see self::ERROR_CANNOT_ADD_FORM_TO_CONTAINER}
     */
    protected function setContainer(?HTML_QuickForm2_Container $container = null) : void
    {
        throw new HTML_QuickForm2_Exception(
            'The main form object cannot be added to a container element.',
            self::ERROR_CANNOT_ADD_FORM_TO_CONTAINER
        );
    }

    public function setId($id = null)
    {
        throw self::exceptionAttributeReadonly('id');
    }

    /**
     * @param string $name The name of the attribute.
     * @param int|null $code Defaults to {@see self::ERROR_ATTRIBUTE_IS_READONLY}
     * @return HTML_QuickForm2_InvalidArgumentException
     */
    public static function exceptionAttributeReadonly(string $name, ?int $code=null) : HTML_QuickForm2_InvalidArgumentException
    {
        if($code === null) {
            $code = self::ERROR_ATTRIBUTE_IS_READONLY;
        }

        return new HTML_QuickForm2_InvalidArgumentException(
            sprintf("Attribute '%s' is read-only", strtolower($name)),
            $code
        );
    }

   /**
    * Adds a new data source to the form
    *
    * @param HTML_QuickForm2_DataSource $datasource Data source
    */
    public function addDataSource(HTML_QuickForm2_DataSource $datasource) : self
    {
        $this->datasources[] = $datasource;
        $this->updateValue();
        return $this;
    }

   /**
    * Replaces the list of form's data sources with a completely new one
    *
    * @param HTML_QuickForm2_DataSource[] $datasources A new data source list
    * @return $this
    *
    * @throws   HTML_QuickForm2_InvalidArgumentException    if given array
    *               contains something that is not a valid data source
    */
    public function setDataSources(array $datasources) : self
    {
        foreach ($datasources as $ds) {
            if (!$ds instanceof HTML_QuickForm2_DataSource) {
                throw new HTML_QuickForm2_InvalidArgumentException(
                    'Array should contain only DataSource instances',
                    self::ERROR_DATA_SOURCES_ARRAY_INVALID
                );
            }
        }

        $this->datasources = $datasources;
        $this->updateValue();

        return $this;
    }

   /**
    * Returns the list of data sources attached to the form
    *
    * @return HTML_QuickForm2_DataSource[]
    */
    public function getDataSources() : array
    {
        return $this->datasources;
    }

    public function getType() : string
    {
        return 'form';
    }

    public function setValue($value) : self
    {
        throw new HTML_QuickForm2_Exception(
            'Not implemented',
            self::ERROR_NOT_IMPLEMENTED
        );
    }

   /**
    * Tells whether the form was already submitted
    *
    * This is a shortcut for checking whether there is an instance of Submit
    * data source in the list of form data sources
    *
    * @return bool
    */
    public function isSubmitted() : bool
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
    public function validate() : bool
    {
        return $this->isSubmitted() && parent::validate();
    }

   /**
    * Renders the form using the given renderer
    *
    * @param HTML_QuickForm2_Renderer $renderer
    * @return HTML_QuickForm2_Renderer
    */
    public function render(HTML_QuickForm2_Renderer $renderer) : HTML_QuickForm2_Renderer
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
     * @param array<string,mixed> $value
     * @return array<string,mixed>
     *
     * @link http://pear.php.net/bugs/bug.php?id=19403
     */
    protected function skipInternalFields(array $value) : array
    {
        foreach (array_keys($value) as $key) {
            if (strpos($key, '_qf') === 0) {
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
    public function getEventHandler() : HTML_QuickForm2_EventHandler
    {
        return $this->eventHandler;
    }
}
