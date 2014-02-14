<?php

/**
 * ArenaUploadForm class.
 * ArenaUploadForm is the data structure for keeping
 * upload form data. It is used by the 'upload' action of 'ArenaController'.
 */
class ArenaUploadForm extends CFormModel
{
    public $fileName;
    public $emailResults;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // fileName is required
            array(
                'fileName',
                'required'
            ),
            // fileName has to be a valid file
            array(
                'fileName',
                'file',
                'types' => 'csv',
                'safe' => true,
                'message' => 'Only CSV files may be uploaded'
            ),
            // $emailResults needs to be a boolean
            array(
                'emailResults',
                'boolean'
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
            'emailResults' => 'E-mail the results',
        );
    }
}