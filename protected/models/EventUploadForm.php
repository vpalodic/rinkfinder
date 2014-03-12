<?php

/**
 * EventUploadForm class.
 * EventUploadForm is the data structure for keeping
 * upload form data. It is used by the 'uploadEvents' action of 'EventController'.
 *
 * The following are the additional properties:
 * @property boolean $emailResults
 */
class EventUploadForm extends RinkfinderUploadForm
{
    public $emailResults;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        $baseRules = parent::rules();
        
        $newRules = array(
            // fileName has to be a valid file
            array(
                'fileName',
                'file',
                'types' => 'csv, tsv, txt',
                'safe' => true,
                'message' => 'Only CSV, TSV, or TXT files may be uploaded'
            ),
            // emailResults needs to be a boolean
            array(
                'emailResults',
                'boolean'
            ),
        );
        
        return array_merge($baseRules, $newRules);
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        $baseLabels = parent::attributeLabels();
        
        $newLabels = array(
            'emailResults' => 'E-mail the results',
        );
        
        return array_merge($baseLabels, $newLabels);
    }
}