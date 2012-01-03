<?php

require_once 'EasyReport/Widget.php';

/**
 * Class to create documents in formats (ODT, PDF or DOC) using a ODT temlate
 * document dynamycally.
 * It is very importante unoconv is installed and configured in the server 
 * to create PDF and DOC.
 */
class EasyReport
{
    /** @var string The file with the OpenOffice XML content */
	const CONTENT_XML = 'content.xml';
	
	/** @var string Microsoft Word extension */
	const DOC_EXTENSION = 'doc';
	
	/** @var string OpenOffice Writer file extension*/
	const ODT_EXTENSION = 'odt';
	
	/** @var string PDF file extension */
	const PDF_EXTENSION = 'pdf';
	
	/** @ var string The unoconv command format */
	const UNOCONV_COMMAND = 'unoconv -f %s %s';

    /** @var string The specified template */
	protected $_templateFile;
	
	/** @var string The template file basename  */
	protected $_templateBaseName;
	
	/** @var string Temporal path to generate intermediate files */
	protected $_tmpPath = '/tmp';
	
	/** @var string Temporal path to genereate files for the current document */
	protected $_tmpFilePath;
	
	/**
	 * Class constructor
	 * @param string $template ODT File to use as template.
	 * @param string $tmpPath Path for temporal files (must be writeable).
	 */
	public function __construct($template, $tmpPath=null)
	{
		if(!$template){
			throw new InvalidArgumentException('$template');
		}

		if($tmpPath !== null){
			$this->_tmpPath = $tmpPath;
		}
		
		$this->_templateFile = $template;
		$this->_templateBaseName = basename($this->_templateFile);
		$this->_tmpFilePath = $this->_tmpPath.DIRECTORY_SEPARATOR.$this->_templateBaseName;
	}


    /**
     * Use the template to create a file with the specified data.
     * The output file can be formatted in pdf, doc or odt.
     * The format is recognized from the specified output file name, ex: report.pdf
     * @var string $outputFile The output file.
     * @var string $data Array with data to replace into the template
     */
	public function create($outputFile, $data)
	{
	    //Lookup for the output extension. Default is odt
	    if(preg_match('/\.(.*)$/', $outputFile, $matches)){
	        $outputFormat = $matches[1];
	    }else{
	        $outputFormat = self::ODT_EXTENSION;
	    }
	    
	    
		if(!empty($data)){
		    //Unzip the odt template and load the content
			$this->unzip();
			
			$contentXmlFile = $this->_tmpFilePath.DIRECTORY_SEPARATOR
				.self::CONTENT_XML;

			$content = file_get_contents($contentXmlFile);
			
			//replace the template content with the data
			$newContent = $this->replaceVars($content, $data);
			file_put_contents($contentXmlFile, $newContent);
			
			//If the output format is pdf or doc, it needs to create de odt and
			//to convert it.
			if($outputFormat != self::ODT_EXTENSION){
			    $temporalOdtFile = $this->_tmpPath.DIRECTORY_SEPARATOR
			        .basename($outputFile).'.'.self::ODT_EXTENSION;

		        //Creates the odt base file
		        $this->zip($temporalOdtFile);
		        
		        //Converts the odt file to the specified format
		        exec(sprintf(self::UNOCONV_COMMAND, $outputFormat, $temporalOdtFile));
		        
		        //Moves de file to the specified path
		        $convertedFileName = str_replace('.odt', '.'.$outputFormat,
		            $temporalOdtFile);
		        if(!@rename($convertedFileName, $outputFile)){
		            copy($convertedFileName, $outputFile);
		        }
		        
			}else{
			    $this->zip($outputFile);
			}
			//Remove temporal directory
			$this->recursiveDelete($this->_tmpFilePath);
		}
	}

    /**
     * Unzip the template file
     * @throws Exception 
     */
	protected function unzip()
	{
		$zip = new ZipArchive();
		if($zip->open($this->_templateFile) === true){
			$zip->extractTo($this->_tmpFilePath);
			$zip->close();
		}else{
			throw new Exception("{$this->_templateFile} cannot be opened.");
		}
	}

    /**
     * Zip the template unziped files into the specified output
     * @param string $outputFile
     */
	protected function zip($outputFile)
	{
		$zip = new ZipArchive();
		if($zip->open($outputFile, ZipArchive::CREATE) === true){
			$this->recursiveZip($zip, $this->_tmpFilePath);
			$zip->close();
		}
	}

    /**
     * Helper method to zip a full directory and all his subdirectories
     * @param Resource $zip
     * @param string $path Path to zip
     */
	protected function recursiveZip($zip, $path)
	{
		if( ($handler = opendir($path)) ){
			while( ($file = readdir($handler)) ){
				
				if($file == '.' || $file == '..'){
					continue;
				}
				$fullPath = $path.DIRECTORY_SEPARATOR.$file;
				$relativePath = str_replace($this->_tmpFilePath.DIRECTORY_SEPARATOR,
						'',$fullPath);

				if(is_dir($fullPath)){
					$zip->addEmptyDir($relativePath);
					$this->recursiveZip($zip, $fullPath);
				}else{
					$zip->addFile($fullPath, $relativePath);
				}
			}
			closedir($handler);
		}
	}
	
	/**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    protected function recursiveDelete($str){
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }

    /**
     * Replace the data into the OpenOffice XML content
     * @var string $text The XML
     * @var array $data
     * @return string
     */
	protected function replaceVars($text, $data)
	{
	    $cleanText = preg_replace('/<text:p[^<]+?>{{:(\S+ \S+)}}<\/text:p>/', '{{:$1}}', $text);
	    //$cleanText = $text;
	    
		return preg_replace('/{{(.+?)}}/e', "\$this->_replaceVarsCallback(\$data, '$1')"
		    , $cleanText);
	}
	
	/**
	 * Callback function for preg_replace_callback called in replaceVars.
	 * Inject the data into the vars and widget placeholders.
	 * @return string
	 */
	public function _replaceVarsCallback($data, $var){
	    if(preg_match('/:(\S+) (\S+)/', $var, $matches)){
	        $widgetName = $matches[1];
	        $dataIndex = $matches[2];
	        $widgetData = isset($data[$dataIndex])? $data[$dataIndex] : null;
	        $widget = EasyReport_Widget::factory($widgetName, $widgetData);
	        return $widget->render();
	    }elseif(isset($data[$var])) {
            return $data[$var];
	    }else{
	        return null;
	    }
	}
}
