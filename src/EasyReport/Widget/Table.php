<?php

require_once 'EasyReport/Widget.php';

/**
 * A simple table widget 
 */
class EasyReport_Widget_Table extends EasyReport_Widget 
{
    /** @var array The table header row*/
    protected $_headerRow;
    
    /**
     * Gets the table header row
     * @return array
     */
    public function getHeaderRow(){
        return $this->_headerRow;
    }
    
    /**
     * Overrides the base setData to auto-set the header row
     * @param array $data
     */
    public function setData($data){
        $this->_headerRow = array_shift($data);
        $this->_data = $data;
    }
    
    /**
     * Renders the OpenOffice table XML with the data
     */
    public function render()
    {
        return $this->loadXml('table.php');
    }
}
