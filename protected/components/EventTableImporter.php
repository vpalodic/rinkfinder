<?php
/**
 * Class to easily import a CSV file in to the event database table
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

class EventTableImporter extends TableImporter
{
    /**
     *
     * @var integer Holds the id of the arena we will be importing to
     */
    protected $arenaId;
    
    /**
     *
     * @var integer Holds the id of the arena we will be importing to
     */
    protected $typeId;
    
    /**
     * Set the Arena ID that we will import to
     * @param integer $arenaId
     */
    public function setArenaId($arenaId)
    {
        $this->arenaId = $arenaId;
    }
    
    /**
     * Return the Arena ID that we will import to
     * @return integer
     */
    public function getArenaId()
    {
        return $this->arenaId;
    }
    
    /**
     * Set the Event Type ID that we will import to
     * @param integer $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }
    
    /**
     * Return the Event Type ID that we will import to
     * @return integer
     */
    public function getTypeId()
    {
        return $this->typeId;
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
        // Check that the arenaId has been set
        if(!isset($this->arenaId) || $this->arenaId <= 0 ||
                !isset($this->typeId) || $this->typeId <= 0) {
            return json_encode(array(
                'success' => false,
                'error' => 'Missing or invalid arena_id.'
            ));
        }
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
        
        // Update the mappings!
        $this->updateMappings();
        
        // Update the CSV Data to add the arena_id to it!
        $this->updateCsvData();
        
        if($transaction == true && $rowsPerCommit == 0) {
            $rowsPerCommit = $this->rowsTotal;
        }
        
        // Check to see if we should look for existing records
        if($this->csvOptions['updateExisting'] == 1) {
            // Only check for existing records if the external_id field
            // has been mapped!
            foreach($this->mappings as $mapping) {
                if($mapping['fieldName'] == 'external_id') {
                    $this->findExistingRecords();
                }
            }
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
    
    protected function updateMappings()
    {
        $this->mappings[] = array(
            'fieldName' => 'arena_id',
            'headerName' => 'my_secret_arena_id',
            'fieldType' => 'integer',
            'fieldSize' => 0,
            'fieldRequired' => true,
        );
        
        $this->mappings[] = array(
            'fieldName' => 'type_id',
            'headerName' => 'my_secret_type_id',
            'fieldType' => 'integer',
            'fieldSize' => 0,
            'fieldRequired' => true,
        );
    }
    
    protected function updateCsvData()
    {
        for($i = 0; $i < $this->rowsTotal; $i++) {
            $this->csvData[$i]['my_secret_arena_id'] = $this->arenaId;
            $this->csvData[$i]['my_secret_type_id'] = $this->typeId;
        }
    }
}
