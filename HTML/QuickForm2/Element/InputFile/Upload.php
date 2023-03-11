<?php

class HTML_QuickForm2_Element_InputFile_Upload
{
    const ERROR_CANNOT_RETRIEVE_INVALID_UPLOAD_DATA = 31501;
    
   /**
    * @var HTML_QuickForm2_Element_InputFile
    */
    protected $element;
    
   /**
    * @var array
    */
    protected $value;
    
   /**
    * @var boolean
    */
    protected $valid;
    
    public function __construct(HTML_QuickForm2_Element_InputFile $element, $value)
    {
        $this->element = $element;
        $this->value = $value;
        $this->valid = !empty($this->value) && is_uploaded_file($value['tmp_name']);
    }
    
   /**
    * Whether this is a valid uploaded file.
    * @return boolean
    */
    public function isValid()
    {
        return $this->valid;
    }
    
   /**
    * The name of the uploaded file, without path.
    * 
    * NOTE: Make sure to check if the upload is valid before calling this method.
    * 
    * @return string
    * @throws HTML_QuickForm2_Exception
    */
    public function getName()
    {
        return $this->getKey('name');
    }
    
    protected function getKey($name)
    {
        if($this->valid) {
            return $this->value[$name];
        }
        
        throw new HTML_QuickForm2_Exception(
            'Cannot retrieve data of an invalid upload.',
            self::ERROR_CANNOT_RETRIEVE_INVALID_UPLOAD_DATA
        );
    }
    
   /**
    * Retrieves the type of the file, e.g. "image/jpeg".
    * 
    * NOTE: Make sure to check if the upload is valid before calling this method.
    * 
    * @return string
    * @throws HTML_QuickForm2_Exception
    */
    public function getFileType()
    {
        return $this->getKey('type');
    }
    
   /**
    * Retrieves the path to the temporary uploaded file.
    * 
    * NOTE: Make sure to check if the upload is valid before calling this method.
    * 
    * @return string
    * @throws HTML_QuickForm2_Exception
    */
    public function getTempPath()
    {
        return $this->getKey('tmp_name');
    }
    
   /**
    * Retrieves the file size, in bytes.
    * 
    * NOTE: Make sure to check if the upload is valid before calling this method.
    * 
    * @return int
    * @throws HTML_QuickForm2_Exception
    */
    public function getSize()
    {
        return $this->value['size'];
    }

   /**
    * Moves the uploaded file to the target location.
    * 
    * NOTE: Make sure to check if the upload is valid before calling this method.
    * 
    * @param string $path
    * @return boolean
    * @throws HTML_QuickForm2_Exception
    */
    public function moveTo($path)
    {
        return move_uploaded_file($this->getTempPath(), $path);
    }
}