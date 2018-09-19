<?php

class HTML_QuickForm2_Event_NodeAdded extends HTML_QuickForm2_Event
{
   /**
    * Retrieves the node instance that was added.
    * @return HTML_QuickForm2_Node
    */
    public function getNode()
    {
        return $this->args['node'];
    }
}
