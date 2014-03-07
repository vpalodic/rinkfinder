<?php

/**
 * RinkfinderUploadForm class.
 * RinkfinderUploadForm is the base class for keeping
 * upload form data. It is used by the 'upload' actions in controllers.
 *
 * The following are the available properties:
 * @property string $fileName
 * @property integer $fileSize
 */
class RinkfinderUploadForm extends CFormModel
{
    public $fileName;
    public $fileSize;

    protected $fileInstance;
    protected $fileUploadTypeId;
    protected $fileUploadUserId;
    protected $fileUploadArenaId;
    protected $fileUploadIceSheetId;
    protected $fileUploadPath;
    protected $fileUploadUri;
    protected $fileUploadRecord;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // fileName and fileSize are required
            array(
                'fileName, fileSize',
                'required'
            ),
            // fileSize needs to be an integer
            array(
                'fileSize',
                'numerical',
                'integerOnly' => true
            ),
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'fileName' => 'File Name',
            'fileSize' => 'File Size',
            'emailResults' => 'E-mail the results',
        );
    }

    /**
     * Retrieves the uploaded file
     * @return mixed Returns true if the file was retrieved or else
     * a JSON encoded error string
     */
    public function getUploadFileInstance()
    {
        $this->fileInstance = CUploadedFile::getInstance($this, 'fileName');
        
        if($this->fileInstance === null) {
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Failed to retrieve uploaded file.',
                    )
            );
        }
        
        return true;
    }
    
    /**
     * Validates the model's properties against the file that was uploaded
     * @return mixed Returns true if the file is valid or else
     * a JSON encoded error string
     */
    public function isValidUploadedFile()
    {
        // Work around for IE 7 - 9!
        if($this->fileSize == 0 && $this->fileName == '') {
            $this->fileSize = $this->fileInstance->size;
            $this->fileName = $this->fileInstance->name;
        }

        if($this->fileInstance->size != $this->fileSize || strcmp($this->fileInstance->name, $this->fileName) != 0) {
            return json_encode(
                    array(
                        'success' => false,
                        'error' => "File received does not match file uploaded.\n"
                        . 'Sent File Name: ' . $this->fileName . "\n"
                        . 'Sent Files Sze: ' . $this->fileSize . "\n"
                        . 'Received File Name: ' . $this->fileInstance->name . "\n"
                        . 'Received File Size: ' . $this->fileInstance->size . "\n"
                    )
            );
        }
        
        return true;
    }
    
    /**
     * Prepares a directory for saving the uploaded file to
     * The directory structure is as follows: BaseUploadDir
     *  -> Date -> TypeId -> UserId -> ArenaId -> IceSheetId
     * @param CUploadedFile $file The uploaded file instance
     * @return mixed Returns true if the reuqested directory
     * has been created or else a JSON encoded error string
     */
    public function prepareUploadDirectory($uploadTypeId, $uploadUserId, $uploadArenaId = null, $uploadIceSheetId = null)
    {
        // Save the parameters to our protected attributes
        $this->fileUploadTypeId = $uploadTypeId;
        $this->fileUploadUserId = $uploadUserId;
        $this->fileUploadArenaId = $uploadArenaId;
        $this->fileUploadIceSheetId = $uploadIceSheetId;
        
        // We build our directory path by starting with the base and working
        // our way down the chain...
        $this->fileUploadPath = Yii::app()->params['uploads']['path'];
        $this->fileUploadPath .= DIRECTORY_SEPARATOR;
        $this->fileUploadPath .= Yii::app()->params['uploads']['directory'];
        $this->fileUploadUri = Yii::app()->params['uploads']['directory'];

        // Each year gets its own directory
        $datestr = date("Y");
        $this->fileUploadPath .= DIRECTORY_SEPARATOR;
        $this->fileUploadPath .= $datestr;
        $this->fileUploadUri .= DIRECTORY_SEPARATOR;
        $this->fileUploadUri .= $datestr;
        
        // Each month gets its own directory
        $datestr = date("m");
        $this->fileUploadPath .= DIRECTORY_SEPARATOR;
        $this->fileUploadPath .= $datestr;
        $this->fileUploadUri .= DIRECTORY_SEPARATOR;
        $this->fileUploadUri .= $datestr;
        
        // Each day gets its own directory
        $datestr = date("d");
        $this->fileUploadPath .= DIRECTORY_SEPARATOR;
        $this->fileUploadPath .= $datestr;
        $this->fileUploadUri .= DIRECTORY_SEPARATOR;
        $this->fileUploadUri .= $datestr;
        
        // Each type gets its own directory
        $this->fileUploadPath .= DIRECTORY_SEPARATOR;
        $this->fileUploadPath .= (string)$uploadTypeId;
        $this->fileUploadUri .= DIRECTORY_SEPARATOR;
        $this->fileUploadUri .= (string)$uploadTypeId;
            
        // Each user gets it's own subdirectory
        $this->fileUploadPath .= DIRECTORY_SEPARATOR;
        $this->fileUploadPath .= (string)$uploadUserId;
        $this->fileUploadUri .= DIRECTORY_SEPARATOR;
        $this->fileUploadUri .= (string)$uploadUserId;

        // Each Arena gets its own subdirectory
        if($uploadArenaId !== null) {
            $this->fileUploadPath .= DIRECTORY_SEPARATOR;
            $this->fileUploadPath .= (string)$uploadArenaId;
            $this->fileUploadUri .= DIRECTORY_SEPARATOR;
            $this->fileUploadUri .= (string)$uploadArenaId;
        }
        
        // Finally, each Ice Sheet gets its own subdirectory
        if($uploadIceSheetId !== null) {
            $this->fileUploadPath .= DIRECTORY_SEPARATOR;
            $this->fileUploadPath .= (string)$uploadIceSheetId;
            $this->fileUploadUri .= DIRECTORY_SEPARATOR;
            $this->fileUploadUri .= (string)$uploadIceSheetId;
        }
            
        // Check if the path exists. If it doesn't, then create it!
        if(!file_exists($this->fileUploadPath)) {
            // Attempt to create the directory path
            if(!mkdir($this->fileUploadPath, 0777, true)) {
                return json_encode(
                        array(
                            'success' => false,
                            'error' => 'Failed to create upload directory: ' . $this->fileUploadPath,
                        )
                );
            }
        }
        
        return true;
    }
    
    /**
     * Checks if a file record already exists in the database
     * with the file name and upload type
     * @param string $deleteUrl Url to use to delete the file!
     * @param string $overwriteUrl Url to use to overwrite the file!
     * @return mixed Returns false if the file doesn't have an
     * existing database record or else a JSON encoded error string
     */
    public function getHasFileRecord($deleteUrl = '', $overwriteUrl = '')
    {
        // Check for an existing record
        $fileUploadRecord = FileUpload::model()->find(
                'upload_type_id = :upload_type_id AND name = :name'
                . ' AND path = :path',
                array(
                    ':upload_type_id' => $this->fileUploadTypeId,
                    ':name' => $this->fileName,
                    ':path' => $this->fileUploadPath
                )
        );

        if($fileUploadRecord !== null) {
            // Ok, we have an existing record, so we abort!
            $fuarr = array(
            );

            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Uploaded file has an existing database record: ' . $this->fileName,
                        'existingFile' => true,
                        'fileUpload' => array(
                            'id' => (integer)$fileUploadRecord->id,
                            'upload_type_id' => (integer)$fileUploadRecord->upload_type_id,
                            'user_id' => $fileUploadRecord->user_id,
                            'name' => $fileUploadRecord->name,
                            'path' => $fileUploadRecord->path,
                            'uri' => $fileUploadRecord->uri,
                            'extension' => $fileUploadRecord->extension,
                            'mime_type' => $fileUploadRecord->mime_type,
                            'size' => (integer)$fileUploadRecord->size,
                            'error_code' => (integer)$fileUploadRecord->error_code,
                            'created_on' => date_format(date_create_from_format("Y-m-d H:i:s", $fileUploadRecord->created_on), "m-d-Y H:i:s"),
                        ),
                        'deleteFile' => array(
                            'endpoint' => $deleteUrl,
                        ),
                        'overwriteFile' => array(
                            'endpoint' => $overwriteUrl,
                        )
                    )
            );                    
        }
        
        return false;
    }
    
    /**
     * Saves the uploaded file to the file system
     * @return mixed Returns true if the file was saved
     * or else a JSON encoded error string
     */
    public function saveUploadedFile()
    {
        if(!$this->fileInstance->saveAs($this->fileUploadPath . DIRECTORY_SEPARATOR . $this->fileName)) {
            // Something went horribly wrong!!!
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to save the uploaded file to the system: ' . $this->fileName,
                    )
            );
        }
        
        $this->fileUploadUri .= DIRECTORY_SEPARATOR . $this->fileName;
        
        return true;
    }
    
    /**
     * Saves the uploaded file details to the dabase!
     * @return mixed Returns truee if the file was saved
     * or else a JSON encoded error string
     */
    public function saveUploadedFileRecord()
    {
        // Check for an existing record
        $fileUploadRecord = FileUpload::model()->find(
                'upload_type_id = :upload_type_id AND name = :name'
                . ' AND path = :path',
                array(
                    ':upload_type_id' => $this->fileUploadTypeId,
                    ':name' => $this->fileName,
                    ':path' => $this->fileUploadPath
                )
        );

        if($fileUploadRecord !== null) {
            $this->fileUploadRecord = $fileUploadRecord;
        } else {
            $this->fileUploadRecord = new FileUpload();
            $this->fileUploadRecord->upload_type_id = $this->fileUploadTypeId;
            $this->fileUploadRecord->user_id = $this->fileUploadUserId;
            $this->fileUploadRecord->name = $this->fileInstance->getName();
            $this->fileUploadRecord->path = $this->fileUploadPath;
            $this->fileUploadRecord->uri = $this->fileUploadUri;
            $this->fileUploadRecord->extension = $this->fileInstance->getExtensionName();
            $this->fileUploadRecord->mime_type = $this->fileInstance->getType();
            $this->fileUploadRecord->size = $this->fileInstance->getSize();
            $this->fileUploadRecord->error_code = $this->fileInstance->getError();
        }

        if(!$this->fileUploadRecord->save()) {
            // Something went horribly wrong!!!
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to save the uploaded file details to the database: ' . $this->fileName,
                    )
            );
        }
        
        return true;
    }
    
    /**
     * Returns a JSON encoded success string
     * @param string $deleteUrl The URL to delete the uploaded file
     * @param string $processUrl The URL to process the uploaded file
     * @return string JSON encoded success string
     */
    public function getJsonSuccessResponse($deleteUrl = '', $processUrl = '')
    {
        return json_encode(
                array(
                    'success' => true,
                    'error' => false,
                    'uploadType' => FileUpload::itemAlias('UploadType', $this->fileUploadRecord->upload_type_id),
                    'userFullName' => Yii::app()->user->fullName,
                    'fileUpload' => array(
                        'id' => (integer)$this->fileUploadRecord->id,
                        'upload_type_id' => (integer)$this->fileUploadRecord->upload_type_id,
                        'user_id' => Yii::app()->user->id,
                        'name' => $this->fileUploadRecord->name,
                        'path' => $this->fileUploadRecord->path,
                        'uri' => $this->fileUploadRecord->uri,
                        'extension' => $this->fileUploadRecord->extension,
                        'mime_type' => $this->fileUploadRecord->mime_type,
                        'size' => (integer)$this->fileUploadRecord->size,
                        'error_code' => (integer)$this->fileUploadRecord->error_code,
                        'created_on' => isset($this->fileUploadRecord->created_on) ? $this->fileUploadRecord->created_on : date("m-d-Y H:i:s"), // Needed as we don't set the created_on database field
                        'updated_on' => isset($this->fileUploadRecord->updated_on) ? $this->fileUploadRecord->updated_on : date("m-d-Y H:i:s"), // Needed as we don't set the created_on database field
                    ),
                    'deleteFile' => array(
                        'endpoint' => $deleteUrl,
                    ),
                    'processFile' => array(
                        'endpoint' => $processUrl,
                    )
                )
        );
    }
    
    /**
     * Deletes an uploaded file from the file system and database
     * @param integer $fid The FileUpload database ID to delete
     * @param string $name The name of the file to delete
     * @param integer $type_id The FileUpload Type ID to delete
     * @return string JSON encoded string
     */
    public static function deleteUploadedFile($fid, $name, $type_id)
    {
        $fileUploadRecord = FileUpload::model()->find(
                'upload_type_id = :upload_type_id AND name = :name AND id = :fid',
                array(
                    ':upload_type_id' => $type_id,
                    ':name' => $name,
                    ':fid' => $fid,
                )
        );

        if($fileUploadRecord === null) {
            // Unable to find an existing record, so we abort!
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to find database record for: ' . $name,
                    )
            );
        }
            
        // Ok, we can safely delete the file!
        $fuarr = array(
            'id' => (integer)$fileUploadRecord->id,
            'upload_type_id' => (integer)$fileUploadRecord->upload_type_id,
            'user_id' => $fileUploadRecord->user_id,
            'name' => $fileUploadRecord->name,
            'path' => $fileUploadRecord->path,
            'uri' => $fileUploadRecord->uri,
            'extension' => $fileUploadRecord->extension,
            'mime_type' => $fileUploadRecord->mime_type,
            'size' => (integer)$fileUploadRecord->size,
            'error_code' => (integer)$fileUploadRecord->error_code,
            'created_on' => date_format(date_create_from_format("Y-m-d H:i:s", $fileUploadRecord->created_on), "m-d-Y H:i:s"),
        );

        $deleteResult = true;
        $unlinkResult = true;
        $error = '';
        
        // First try and remove the file from the file system.
        // We only remove it from the database if it was removed
        // from the files ystem.
        if(!unlink($fileUploadRecord->path . DIRECTORY_SEPARATOR . $fileUploadRecord->name)) {
            // Unable to delete the file from the file system
            $unlinkResult = false;
            $error .= 'Unable to delete ' . $fileUploadRecord->path . DIRECTORY_SEPARATOR . $fileUploadRecord->name . '. It may have already been removed.';
        }
        
        // The file is now deleted so let us delete the database record
        if(!$fileUploadRecord->delete()) {
            // Unable to delete the file from the database
            $deleteResult = false;
            $error .= 'Unable to delete the file\'s database record.';
        }
        
        // We are ready to process the file so let the UI know that we are ready for it!
        return json_encode(
                array(
                    'success' => ($deleteResult === true && $unlinkResult === true) ? true : false,
                    'error' => $error,
                    'userFullName' => Yii::app()->user->fullName,
                    'uploadType' => FileUpload::itemAlias('UploadType', $fileUploadRecord->upload_type_id),
                    'fileUpload' => $fuarr,
                    'recordDeleted' => $deleteResult,
                    'fileDeleted' => $unlinkResult,
                )
        );
    }
}