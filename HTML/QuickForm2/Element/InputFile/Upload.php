<?php

class HTML_QuickForm2_Element_InputFile_Upload
{
   /**
    * @var HTML_QuickForm2_Element_InputFile
    */
    protected $element;
    
   /**
    * @var array
    */
    protected $value;
    
    public function __construct(HTML_QuickForm2_Element_InputFile $element, $value)
    {
        $this->element = $element;
        $this->value = $value;
    }
    
   /**
    * Whether this is a valid uploaded file.
    * @return boolean
    */
    public function isValid()
    {
        return !empty($this->value) && is_uploaded_file($this->getTempPath());
    }
    
   /**
    * The name of the uploaded file, without path.
    * @return string
    */
    public function getName()
    {
        return $this->value['name'];
    }
    
   /**
    * Retrieves the type of the file, e.g. "image/jpeg".
    * @return string
    */
    public function getFileType()
    {
        return $this->value['type'];
    }
    
   /**
    * Retrieves the path to the temporary uploaded file
    * @return string
    */
    public function getTempPath()
    {
        return $this->value['tmp_name'];
    }
    
   /**
    * Retrieves the file size, in bytes.
    * @return int
    */
    public function getSize()
    {
        return $this->value['size'];
    }

   /**
    * Moves the uploaded file to the target location.
    * 
    * @param string $path
    * @return boolean
    */
    public function moveTo($path)
    {
        return move_uploaded_file($this->getTempPath(), $path);
    }
}