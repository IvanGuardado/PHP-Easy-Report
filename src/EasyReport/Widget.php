<?php

/**
 * Base for any widget in the template
 */
abstract class EasyReport_Widget {
    
    /** @var string Namespace to compound the final widget class name */
    const WIDGETS_NAMESPACE = 'EasyReport_Widget_';
    
    /** @var string Base path to compound widget xml path */
    const WIDGETS_XML_PATH  = 'EasyReport/Widget/Xml/';
    
    /** @var string XML files extension */
    const WIDGETS_FILE_EXT  = '.php';
    
    /** @var array The data to use in the widget */
    protected $_data;

    /**
     * Retieves the content to replace in the widget placeholder
     */
    abstract public function render();
    
    /**
     * Set the data to use in the widget
     * @param array $data
     */
    protected function setData($data)
    {
        $this->_data = $data;
    }
    
    /**
     * Get the data to use in the widget
     * @return array
     */
    protected function getData()
    {
        return $this->_data;
    }
    
    /**
     * Loads the specified widget xml
     * @return string
     */
    protected function loadXml($file)
    {
        $path = self::WIDGETS_XML_PATH.$file;
        ob_start();
        require $path;
        return ob_get_clean();
    }
    
    /**
     * Function to create dynamically a widget
     * @param string $widgetName The widget name to create
     * @param array $data The data to pass the widget
     */
    public static function factory($widgetName, $data)
    {
        $className = self::WIDGETS_NAMESPACE.$widgetName;
        $classFile = str_replace('_', '/', $className).self::WIDGETS_FILE_EXT;
        require_once $classFile;
        $object = new $className;
        $object->setData($data);
        return $object;
    }
}
