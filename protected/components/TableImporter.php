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
     * existing records. This must contain the fields specified in
     * $tableInFields and should contain the primary key.
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
     * @var array[] The fields and values that should be added for new records
     */
    protected $tableCreateFields;
    
    /**
     *
     * @var array[] The fields and values that should be added for existing
     * records
     */
    protected $tableUpdateFields;
    
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
     * @param array[] $tableCreateFields
     * @param array[] $tableUpdateFields
     * @param array[][] $tableFields
     * @param array[] $csvOptions
     * @param FileUpload $fileUpload
     * @param array[][] $mappings
     */
    public function __construct($tableName, $tableSelectFields, $tableInFields, $tableCreateFields, $tableUpdateFields, $tableFields, $csvOptions, $fileUpload, $mappings) 
    {
        $this->tableName = $tableName;
        $this->tableSelectFields = $tableSelectFields;
        $this->tableInFields = $tableInFields;
        $this->tableCreateFields = $tableCreateFields;
        $this->tableUpdateFields = $tableUpdateFields;
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
     * Imports the CSV data in to the table
     * @param boolean $transaction If set to true, import will be run inside a
     * transaction
     * @param integer $rowsPerCommit Number of rows to insert between commits
     * @return mixed True if import is successful, otherwise a JSON
     * formatted error string
     * @throws CDbException
     */
    public function doImport($transaction = true, $rowsPerCommit = 0)
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
        
        // Get the total row count
        $this->rowsTotal = count($this->csvData);
        
        if($transaction == true && $rowsPerCommit == 0) {
            $rowsPerCommit = $this->rowsTotal;
        }
        
        // Check to see if we should look for existing records
        if($this->csvOptions['updateExisting'] == 1) {
            $this->findExistingRecords();
        }
        
        // Update the existing rows if we have any
        if($this->existingRecords !== false && !empty($this->existingRecords)) {
            $this->updateExistingRecords($transaction, $rowsPerCommit);
        }
        
        // Now insert the new rows if we have any!
        if(!empty($this->csvData)) {
            $this->insertNewRecords($transaction, $rowsPerCommit);
        }
        
        return true;
    }
    
    /**
     * If existing rows are found, then $this->existingRecords will be
     * populated after this function returns.
     * @throws CDbException
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
            $this->existingRecords = false;
        }
    }
    
    /**
     * Updates the existing records in the database and sets the $rowsUpdated
     * property
     * @param boolean $transaction If set to true, import will be run inside a
     * transaction
     * @param integer $rowsPerCommit Number of rows to insert between commits
     * @throws CDbException
     */
    protected function updateExistingRecords($transaction, $rowsPerCommit)
    {
        // At this point, we have our existing records but, we don't know
        // which CSV records correspond to the existing records.
        // Also, we need to remove the existing records from the CSV Data
        // once we have placed it in to the update array. We will use a helper
        // function to do this and return the update array!
        $updateRecords = $this->buildUpdateRecords();
        
        if(empty($updateRecords)) {
            return true;
        }
        
        $updateRecordsTotal = count($updateRecords);
        $this->rowsUpdated = 0;
        
        // We have records to update!
        if($transaction == true) {
            $dbTransaction = Yii::app()->db->beginTransaction();
            $lastCommitCount = 0;
            try
            {
                $needCommit = true;
                
                for($i = 0; $i < $updateRecordsTotal; $i++) {
                    $this->rowsUpdated += $this->updateExistingRecord($updateRecords[$i]);                    
                    unset($updateRecords[$i]);
                    
                    if(fmod($i + 1, $rowsPerCommit) == 0){
                        $dbTransaction->commit();
                        $lastCommitCount = $this->rowsUpdated;
                        $needCommit = false;
                        
                        if($this->rowsUpdated != $updateRecordsTotal) {
                            $dbTransaction = Yii::app()->db->beginTransaction();
                        }
                    } else {
                        $needCommit = true;
                    }
                }                
                
                if($needCommit === true) {
                    $dbTransaction->commit();
                }
            }
            catch(Exception $e)
            {
                $dbTransaction->rollback();
                $this->rowsUpdated = $lastCommitCount;
                $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
                $message = $e->getMessage();
                throw new CDbException(
                        'Failed to execute the SQL statement: ' . $message,
                        (int)$e->getCode(),
                        $errorInfo
                );
            }
        } else {
            for($i = 0; $i < $updateRecordsTotal; $i++) {
                $this->rowsUpdated += $this->updateExistingRecord($updateRecords[$i]);
                unset($updateRecords[$i]);
            }
        }
        
        return true;
    }
    
    /**
     * Updates an existing record in the database
     * @param mixed[] $record
     * @return integer The number of database rows updated
     * @throws CDbException
     */
    protected function updateExistingRecord($record)
    {
        $command = Yii::app()->db->createCommand();

        $where = "";
        $params = array();

        // Build the WHERE clause
        foreach($this->tableSelectFields as $tableSelectField) {
            if($where != "") {
                $where .= " AND ";
            }
            
            $where .= $tableSelectField . "= :where" . $tableSelectField;
            $params[":where" . $tableSelectField] = $record[$tableSelectField];
        }
        
        return $command->update($this->tableName, $record, $where, $params);
    }
    
    /**
     * Inserts new records in to the database and sets the $rowsInserted
     * property
     * @param boolean $transaction If set to true, import will be run inside a
     * transaction
     * @param integer $rowsPerCommit Number of rows to insert between commits
     * @throws CDbException
     */
    protected function insertNewRecords($transaction, $rowsPerCommit)
    {
        // At this point, any existing records have been updated
        // and only the new records exist in the CSV Data CSV Data. We need
        // to build the $insertRecords and we will use a helper
        // function to do this and return the insert array!
        $insertRecords = $this->buildInsertRecords();
        
        if(empty($insertRecords)) {
            return true;
        }
        
        $insertRecordsTotal = count($insertRecords);
        $this->rowsInserted = 0;
        
        // We have records to update!
        if($transaction == true) {
            $dbTransaction = Yii::app()->db->beginTransaction();
            $lastCommitCount = 0;
            try
            {
                $needCommit = true;
                
                for($i = 0; $i < $insertRecordsTotal; $i++) {
                    $this->rowsInserted += $this->insertNewRecord($insertRecords[$i]);                    
                    unset($insertRecords[$i]);
                    
                    if(fmod($i + 1, $rowsPerCommit) == 0){
                        $dbTransaction->commit();
                        $lastCommitCount = $this->rowsInserted;
                        $needCommit = false;
                        
                        if($this->rowsInserted != $insertRecordsTotal) {
                            $dbTransaction = Yii::app()->db->beginTransaction();
                        }
                    } else {
                        $needCommit = true;
                    }
                }                
                
                if($needCommit === true) {
                    $dbTransaction->commit();
                }
            }
            catch(Exception $e)
            {
                if($dbTransaction->active == true) {
                    $dbTransaction->rollback();
                }
                
                $this->rowsInserted = $lastCommitCount;
                
                if($e instanceof CDbException) {
                    throw $e;
                }
                
                $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
                $message = $e->getMessage();
                throw new CDbException(
                        'Failed to execute the SQL statement: ' . $message,
                        (int)$e->getCode(),
                        $errorInfo
                );
            }
        } else {
            for($i = 0; $i < $insertRecordsTotal; $i++) {
                $this->rowsInserted += $this->insertNewRecord($insertRecords[$i]);
                unset($insertRecords[$i]);
            }
        }
        
        return true;
    }
    
    /**
     * Inserts a new record in to the database
     * @param mixed[] $record
     * @return integer The number of database rows created
     * @throws CDbException
     */
    protected function insertNewRecord($record)
    {
        $command = Yii::app()->db->createCommand();

        return $command->insert($this->tableName, $record);
    }
    
    /**
     * Iterates through the $csvData and $existingRecords. When matches are 
     * found, it builds a row in an update array and removes the found rows from
     * both the $csvData and $existingRecords.
     * @return array[][] The fully populated array of updated records
     */
    protected function buildUpdateRecords()
    {
        $updateArray = array();
        
        /**
         * @var string[] Holds the fields that we need to determine a match
         */
        $matchFields = array();
        
        foreach($this->tableInFields as $tableInField) {
            foreach($this->mappings as $mapping) {
                if($mapping['fieldName'] == $tableInField) {
                    array_push($matchFields, $mapping);
                }
            }
        }

        /**
         * @var integer Holds the count for the match fields. We need this to 
         * determine how many and which fields to match. This count and match
         * count must be equal in order for the rows to be matched
         */
        $matchCount = count($matchFields);
        
        // This will use three nested foreach loops to build each update row
        // The most outer will be the CSV Data, then the existing records, and
        // finally the mappings so that we can transfer both the existing and
        // CSV Data to the update record. We will also add the update fields to
        // the update record if they are specified. After processing a row, that
        // row is removed from both the CSV Data and existing records.
        foreach($this->csvData as $csvK => $csvR) {
            foreach($this->existingRecords as $exiK => $exiR) {
                // We need to match the records and only process it if it is
                // an exact match!
                $matchFound = 0;
                
                foreach($matchFields as $matchField) {
                    $csvV = trim($csvR[$matchField['headerName']]);
                    
                    if($matchField['fieldType'] == 'string' || $matchField['fieldType'] == 'text') {
                        if(strlen($csvV) > $matchField['fieldSize']) {
                            $csvV = substr($csvV, 0, $matchField['fieldSize']);
                        }
                    } elseif($matchField['fieldType'] == 'float') {
                        $str = filter_var($csvV, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        $csvV = floatval($str);
                    } elseif($matchField['fieldType'] == 'integer') {
                        $str = filter_var($csvV, FILTER_SANITIZE_NUMBER_INT);
                        $csvV = intval($str);
                    } elseif($matchField['fieldType'] == 'date') {
                        $csvV = date("Y-m-d", strtotime($csvV));
                    } elseif($matchField['fieldType'] == 'datetime') {
                        $csvV = date("Y-m-d H:i:s", strtotime($csvV));
                    } elseif($matchField['fieldType'] == 'time') {
                        $csvV = date("H:i:s", strtotime($csvV));
                    } elseif($matchField['fieldType'] == 'phone') {
                        $csvV = preg_replace('/[^0-9]/s', '', $csvV);
                    }
                    
                    if($csvV == $exiR[$matchField['fieldName']]) {
                        $matchFound++;
                    }
                }
                
                if($matchFound == $matchCount) {
                    // Copy the existing data over first
                    $updateIndex = array_push($updateArray, $exiR) - 1;
                    
                    foreach($this->mappings as $mapping) {
                        $csvV = trim($csvR[$mapping['headerName']]);
                        
                        if($mapping['fieldType'] == 'string' || $mapping['fieldType'] == 'text') {
                            if(strlen($csvV) > $mapping['fieldSize']) {
                                $csvV = substr($csvV, 0, $mapping['fieldSize']);
                            }
                        } elseif($mapping['fieldType'] == 'float') {
                            $str = filter_var($csvV, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            $csvV = floatval($str);
                        } elseif($mapping['fieldType'] == 'integer') {
                            $str = filter_var($csvV, FILTER_SANITIZE_NUMBER_INT);
                            $csvV = intval($str);
                        } elseif($mapping['fieldType'] == 'date') {
                            $csvV = date("Y-m-d", strtotime($csvV));
                        } elseif($mapping['fieldType'] == 'datetime') {
                            $csvV = date("Y-m-d H:i:s", strtotime($csvV));
                        } elseif($mapping['fieldType'] == 'time') {
                            $csvV = date("H:i:s", strtotime($csvV));
                        } elseif($mapping['fieldType'] == 'phone') {
                            $csvV = preg_replace('/[^0-9]/s', '', $csvV);

                            if(strlen($csvV) > $mapping['fieldSize']) {
                                $csvV = substr($csvV, 0, $mapping['fieldSize']);
                            }
                        }
                        
                        $updateArray[$updateIndex][$mapping['fieldName']] = $csvV;
                    }
                    
                    // Now add the update fields to the updateArray
                    foreach($this->tableUpdateFields as $uK => $uV) {
                        $updateArray[$updateIndex][$uK] = $uV;
                    }
                    
                    // Remove the records from the CSV Data and existing records.
                    unset($this->csvData[$csvK]);
                    unset($this->existingRecords[$exiK]);
                }
            }
        }        
        return $updateArray;
    }
    
    /**
     * Iterates through the $csvData and builds a row in an insert array and
     * removes the rows from the $csvData
     * @return array[][] The fully populated array of new records
     */
    protected function buildInsertRecords()
    {
        $insertArray = array();
        
        // This will use two nested foreach loops to build each insert row
        // The most outer will be the CSV Data and then 
        // the mappings so that we can transfer the CSV Data to the new record.
        // We will also add the create and update fields to
        // the new record if they are specified. After processing a row, that
        // row is removed from the CSV Data
        foreach($this->csvData as $csvK => $csvR) {
            $insertIndex = -1;

            foreach($this->mappings as $mapping) {
                $csvV = trim($csvR[$mapping['headerName']]);

                if($mapping['fieldType'] == 'string' || $mapping['fieldType'] == 'text') {
                    if(strlen($csvV) > $mapping['fieldSize']) {
                        $csvV = substr($csvV, 0, $mapping['fieldSize']);
                    }
                } elseif($mapping['fieldType'] == 'float') {
                    $str = filter_var($csvV, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $csvV = floatval($str);
                } elseif($mapping['fieldType'] == 'integer') {
                    $str = filter_var($csvV, FILTER_SANITIZE_NUMBER_INT);
                    $csvV = intval($str);
                } elseif($mapping['fieldType'] == 'date') {
                    $csvV = date("Y-m-d", strtotime($csvV));
                } elseif($mapping['fieldType'] == 'datetime') {
                    $csvV = date("Y-m-d H:i:s", strtotime($csvV));
                } elseif($mapping['fieldType'] == 'time') {
                    $csvV = date("H:i:s", strtotime($csvV));
                } elseif($mapping['fieldType'] == 'phone') {
                    $csvV = preg_replace('/[^0-9]/s', '', $csvV);

                    if(strlen($csvV) > $mapping['fieldSize']) {
                        $csvV = substr($csvV, 0, $mapping['fieldSize']);
                    }
                }
                
                if($insertIndex == -1) {
                    $insertIndex = array_push($insertArray, array($mapping['fieldName'] => $csvV)) - 1;
                } else {
                    $insertArray[$insertIndex][$mapping['fieldName']] = $csvV;
                }
            }
                    
            // Now add the update fields to the insertArray
            foreach($this->tableUpdateFields as $uK => $uV) {
                $insertArray[$insertIndex][$uK] = $uV;
            }
                    
            // Now add the create fields to the insertArray
            foreach($this->tableCreateFields as $cK => $cV) {
                $insertArray[$insertIndex][$cK] = $cV;
            }
                    
            // Remove the record from the CSV Data.
            unset($this->csvData[$csvK]);
        }
        return $insertArray;
    }
}