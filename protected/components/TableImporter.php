<?php
/**
 * Class to easily import a CSV file in to a database table
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

class TableImporter extends CComponent
{
    /**
     *
     * @var string Holds the name of the table we will be importing to 
     */
    protected $tableName;
    
    /**
     *
     * @var string[] Holds the SELECT clause fields when checking for
     * existing records
     */
    protected $tableSelectFields;

    /**
     *
     * @var string[] Holds the fields for the IN clause when checking for
     * existing records
     */
    protected $tableInFields;
    
    /**
     *
     * @var array[][] Holds the details on the table fields such as data type
     */
    protected $tableFields;
    
    /**
     *
     * @var array[] Holds the CSV processing options
     */
    protected $csvOptions;
    
    /**
     *
     * @var FileUpload Holds the FileUpload database record 
     */
    protected $fileUpload;
    
    /**
     *
     * @var array[][] Holda the CSV to database table mappings 
     */
    protected $mappings;

    /**
     *
     * @var array[][] Holds an array of existing Arena objects 
     */
    protected $existingRecords;
    
    /**
     *
     * @var array[][] Holds the CSV data rows
     */
    protected $csvData;
    
    /**
     *
     * @var CsvImporter Holds the CSV Importer object
     */
    protected $csvImporter;
    
    /**
     *
     * @var integer Number of rows updated
     */
    protected $rowsUpdated;
    
    /**
     *
     * @var integer Number of rows inserted
     */
    protected $rowsInserted;
    
    /**
     *
     * @var integer Number of rows processed
     */
    protected $rowsTotal;
    
    /**
     * Constructs the TableImporter object
     * @param string $tableName
     * @param string[] $tableSelectFields
     * @param string[] $tableInFields
     * @param array[][] $tableFields
     * @param array[] $csvOptions
     * @param FileUpload $fileUpload
     * @param array[][] $mappings
     */
    public function __construct($tableName, $tableSelectFields, $tableInFields, $tableFields, $csvOptions, $fileUpload, $mappings) 
    {
        $this->tableName = $tableName;
        $this->tableSelectFields = $tableSelectFields;
        $this->tableInFields = $tableInFields;
        $this->tableFields = $tableFields;
        $this->csvOptions = $csvOptions;
        $this->fileUpload = $fileUpload;
        $this->mappings = $mappings;
        $this->existingRecords = false;
        $this->csvData = false;
        $this->csvImporter = new CsvImporter(
                $this->fileUpload->path . DIRECTORY_SEPARATOR . $this->fileUpload->name,
                true,
                $this->csvOptions['skipRows'],
                $this->csvOptions['delimiter'],
                $this->csvOptions['enclosure'],
                $this->csvOptions['escapeChar']
        );
        $this->rowsInserted = 0;
        $this->rowsTotal = 0;
        $this->rowsUpdated = 0;
    }
    
    /**
     * Returns the number of rows inserted
     * @return integer
     */
    public function getRowsInserted() 
    {
        return $this->rowsInserted;
    }

    /**
     * Returns the number of rows updated
     * @return integer
     */
    public function getRowsUpdated() 
    {
        return $this->rowsUpdated;
    }

    /**
     * Returns the number of rows processed
     * @return integer
     */
    public function getRowsTotal() 
    {
        return $this->rowsTotal;
    }

    /**
     * 
     * @param boolean $transaction If set to true, import will be run inside a
     * transaction
     * @param integer $rowsPerCommit Number of rows to insert between commits
     * @return mixed True if import is successful, otherwise a JSON
     * formatted error string
     */
    public function doImport($transaction = true, $rowsPerCommit = 10)
    {
        // First we need to read in the CSV data.
        $csvRet = $this->csvImporter->open();
        
        if($csvRet !== true) {
            return $csvRet;
        }
        
        $this->csvData = $this->csvImporter->getRows();
        
        if(empty($this->csvData)) {
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'No data found to process in the file',
                    )
            );
        }
        
        // Close the open file!
        $this->csvImporter->close();
        
        // Check to see if we should look for existing records
        if($this->csvOptions['updateExisting'] == 1) {
            $this->findExistingRecords();
        }
    }
    
    /**
     * If existing rows are found, then $this->existingRecords will be
     * populated after this function returns.
     */
    protected function findExistingRecords()
    {
        $sql = "SELECT ";
        $from = " FROM " . $this->tableName;
        $selectCount = count($this->tableSelectFields);
        $inCount = count($this->tableInFields);
        
        // Build the SELECT clause
        foreach($this->tableSelectFields as $tableSelectField) {
            if($sql != "SELECT ") {
                $sql .= ", ";
            }
            
            $sql .= $tableSelectField;
        }
        
        // Add the FROM clause
        $sql .= $from;
        
        // Add the WHERE clause
        if($inCount == 1) {
            $inQuery = implode(',', array_fill(0, $this->csvImporter->getRowCount(), '?'));
            $sql .= " WHERE " . $this->tableInFields[0] . " IN (" . $inQuery . ")";
            $params = array();
            
            // Find the CSV header to use for the data access
            foreach($this->mappings as $mapping) {
                if($mapping['fieldName'] == $this->tableInFields[0]) {
                    $headerField = $mapping['headerName'];
                }
            }
            
            // Using the header found above, populate the params array!
            foreach($this->csvData as $row) {
                $params[] = $row[$headerField];
            }
            
            $command = Yii::app()->db->createCommand($sql);
            $this->existingRecords = $command->queryAll(true, $params);
            
        } elseif($inCount > 1) {
            $headerFields = array();
            
            // Get the header fields and finish the query strings
            $repParams = "(";
            $whereParams = "(";
            
            foreach($this->tableInFields as $tableInField) {
                if($repParams != "(" && $whereParams != "(") {
                    $repParams .= ",";
                    $whereParams .= ",";
                }
                
                foreach($this->mappings as $mapping) {
                    if($mapping['fieldName'] == $tableInField) {
                        $headerFields[] = $mapping['headerName'];
                        $repParams .= "?";
                        $whereParams .= $tableInField;
                    }
                }
            }
            
            $repParams .= ")";
            $whereParams .= ")";
            
            $inQuery = implode(', ', array_fill(0, $this->csvImporter->getRowCount(), $repParams));
            $sql .= " WHERE " . $whereParams . " IN (" . $inQuery . ")";
            $params = array();
            
            // Using the header found above, populate the params array!
            foreach($this->csvData as $row) {
                foreach($headerFields as $headerField) {
                    $params[] = $row[$headerField];
                }
            }
            
            $command = Yii::app()->db->createCommand($sql);
            $this->existingRecords = $command->queryAll(true, $params);
            
        } else {
            $this->existingRecords = array();
        }
    }
}