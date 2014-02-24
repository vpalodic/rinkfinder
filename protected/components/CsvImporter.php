<?php
/**
 * Class to easily import a CSV file
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

class CsvImporter extends CComponent
{ 
    private $fp;
    private $file_name;
    private $parse_header;
    private $header;
    private $delimiter;
    private $enclosure;
    private $escape;
    private $length;
    private $skipRows;
    
    //-------------------------------------------------------------------- 
    function __construct($file_name, $parse_header = false, $skipRows = 0, $delimiter = ',', $enclosure = '"', $escape = '\\', $length = 0) 
    {
        $this->file_name = $file_name;
        $this->parse_header = $parse_header;
        $this->skipRows = $skipRows;
        $this->delimiter = $delimiter; 
        $this->enclosure = $enclosure; 
        $this->escape = $escape; 
        $this->length = $length; 
        $this->header = false;
        $this->fp = false;

    }
    
    //-------------------------------------------------------------------- 
    function __destruct() 
    { 
        if ($this->fp) 
        { 
            fclose($this->fp); 
        } 
    }
    
    //-------------------------------------------------------------------- 
    function open() 
    {
        $this->fp = fopen($this->file_name, "r");
        
        if($this->fp === false) {
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to open CSV file for processing',
                    )
            );
        }
        
        if($this->parse_header) { 
            $i = 0;
            
            while($i < $this->skipRows) {
                fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure, $this->escape);
                $i += 1;
            }
            
            $this->header = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure, $this->escape);
            
            if($this->header === false) {
                return json_encode(
                        array(
                            'success' => false,
                            'error' => 'Unable to process the CSV header row',
                        )
                );
            }
        }
        
        return true;
    }

    //-------------------------------------------------------------------- 
    function close() 
    {
        if($this->fp !== false) {
            $ret = fclose($this->fp);
            
            $this->fp = false;
            
            return $ret;
        }
        
        return true;
    }
    
    //-------------------------------------------------------------------- 
    function getHeader() 
    {
        return $this->header;
    }

    //-------------------------------------------------------------------- 
    function getRows($max_lines = 0)
    { 
        //if $max_lines is set to 0, then get all the data 

        $data = array(); 

        if($max_lines > 0) {
            $line_count = 0;
        } else {
            $line_count = -1; // so loop limit is ignored
        }

        while ($line_count < $max_lines &&
               ($row = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure, $this->escape)) !== FALSE) {
            if($this->parse_header) { 
                foreach($this->header as $i => $heading_i)
                { 
                    $row_new[$heading_i] = $row[$i]; 
                } 
                $data[] = $row_new; 
            } else { 
                $data[] = $row; 
            }

            if($max_lines > 0) {
                $line_count++;
            }
        } 
        return $data; 
    }
}