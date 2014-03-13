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
    
    /**
     * Adds the arena_id and type_id fields to the mapping
     * table. Also renames the location field to the location_id
     */
    protected function updateMappings()
    {
        // First rename the location field
        $mapCount = count($this->mappings);
        
        for($i = 0; $i < $mapCount; $i++) {
            if($this->mappings[$i]['fieldName'] == 'location') {
                $this->mappings[$i]['fieldName'] = 'location_id';
            }
        }
        
        // Now push our new fields on to the end of the mapping array
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
    
    /**
     * Updates the data mapped to the location field to be the id of the
     * location instead of the name. Also adds the arena_id and type_id.
     * @throws CDbException
     */
    protected function updateCsvData()
    {
        // Cache found locations
        $locations = array();
        $dataLocation = '';
        
        // Find the data field that is mapped to the location_id
        foreach($this->mappings as $mapping) {
            if($mapping['fieldName'] == 'location_id') {
                $dataLocation = $mapping['headerName'];
            }
        }
        
        // Now go through each data row and add our new data
        for($i = 0; $i < $this->rowsTotal; $i++) {
            // First take care of the location_id
            if($dataLocation != '') {
                // Do a case insensitive search for the arena location
                $locationName = trim($this->csvData[$i][$dataLocation]);
                
                // See if it is in the cache and if so, use it
                if(isset($locations[$locationName])) {
                    $this->csvData[$i][$dataLocation] = $locations[$locationName];
                } else {
                    // Find the arena and cache the data
                    $locations[$locationName] = $this->findOrAddLocation($locationName);
                    
                    $this->csvData[$i][$dataLocation] = $locations[$locationName];
                }
            }
            
            $this->csvData[$i]['my_secret_arena_id'] = $this->arenaId;
            $this->csvData[$i]['my_secret_type_id'] = $this->typeId;
        }
    }
    
    /**
     * Returns the location_id for the passed in name. If the
     * location doesn't exist, it is created. New locations are
     * auto tagged as well with the Arena name, Location Name, and
     * display name of the location type. All new locations default to
     * the "Standard" type.
     * @param string $locationName The name of the location
     * @return integer The location_id for the location
     * @throws CDbException
     */
    protected function findOrAddLocation($locationName) {
        // First lets try and find the location
        $sql = "SELECT id FROM location WHERE arena_id = :aid AND LOWER(name) = :name";
        $command = Yii::app()->db->createCommand($sql);
        $lid = $command->queryScalar(
                array(
                    ':aid' => $this->arenaId,
                    ':name' => strtolower($locationName)
                )
        );
        
        if($lid != false) {
            return $lid;
        }
        
        // Location wasn't found so we need to add it to the database
        $location = new Location;
        
        $location->name = $locationName;
        $location->arena_id = $this->arenaId;
        
        if($location->save()) {
            $location->autoTag();
            $location->save();
            return $location->id;
        }
        
        return 0;
    }
}
