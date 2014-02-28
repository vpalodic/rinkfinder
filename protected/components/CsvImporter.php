<?php
/**
 * Class to easily import a CSV file
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

class CsvImporter extends CComponent
{ 
    /**
     *
     * @var mixed Holds the file pointer. Value is false when the file is closed
     */
    protected $fp;
    
    /**
     *
     * @var string The name of the file that contains the data 
     */
    protected $file_name;
    
    /**
     *
     * @var boolean Is set to true, the CSV data will be indexed by the header row
     */
    protected $parse_header;
    
    /**
     *
     * @var array[] Contains the CSV field names if parse_header is true 
     */
    protected $header;
    
    /**
     *
     * @var string Contains the field delimiter character
     */
    protected $delimiter;
    
    /**
     *
     * @var string Contains the field enclosure character
     */
    protected $enclosure;
    
    /**
     *
     * @var string Contains the escape character
     */
    protected $escape;
    
    /**
     *
     * @var integer If greater than 0, conains the maximum number of characters
     * to read per row
     */
    protected $length;
    
    /**
     *
     * @var integer If $parse_header is true, contains the number of rows to
     * skip to get to the header row
     */
    protected $skipRows;
    
    /**
     *
     * @var integer The number of rows read from the CSV file
     */
    protected $rowCount;
    
    /**
     * Constructs the CvsImporter object
     * @param string $file_name
     * @param boolean $parse_header
     * @param integer $skipRows
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param integer $length
     */
    public function __construct($file_name, $parse_header = false, $skipRows = 0, $delimiter = ',', $enclosure = '"', $escape = '\\', $length = 0) 
    {
        $this->file_name = $file_name;
        $this->parse_header = $parse_header;
        $this->skipRows = $skipRows;
        $this->delimiter = $delimiter; 
        $this->enclosure = $enclosure; 
        $this->escape = $escape; 
        $this->length = $length;
        $this->rowCount = 0;
        $this->header = false;
        $this->fp = false;
    }
    
    /**
     * Destroys the CsvImporter object and closes the CSV file if it is open
     */
    public function __destruct() 
    { 
        if($this->fp) {
            fclose($this->fp);
        }
    }
    
    /**
     * Opens the CSV file and parses the header row
     * @return mixed Returns true if the file is opened and the header
     * is successfully parsed. otherwise a JSON encoded error string
     */
    public function open() 
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

    /**
     * Closes the file if it is open
     * @return boolean
     */
    public function close() 
    {
        if($this->fp !== false) {
            $ret = fclose($this->fp);
            
            $this->fp = false;
            
            return $ret;
        }
        
        return true;
    }
    
    /**
     * Returns the parsed header
     * @return string[]
     */
    public function getHeader() 
    {
        return $this->header;
    }

    /**
     * Returns the row count
     * @return string[]
     */
    public function getRowCount() 
    {
        return $this->rowCount;
    }

    /**
     * Retrives the number of rows up to $max_lines. If $max_lines is 0,
     * then all rows are retrieved. If there is a header, the row data is
     * indexed by the header fields.
     * @param integer $max_lines
     * @return array[] The CSV data
     */
    public function getRows($max_lines = 0)
    { 
        // if $max_lines is === 0, then get all the data 

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
            
            $this->rowCount++;
        } 
        return $data; 
    }
}