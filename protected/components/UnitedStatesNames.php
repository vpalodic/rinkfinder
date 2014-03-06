<?php
/**
 * Class to easily retrieve a states name or abbreviation
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

class UnitedStatesNames extends CComponent
{ 
    /**
     *
     * @var mixed Holds the file pointer. Value is false when the file is closed
     */
    public static $states = array(
        'AA' => 'Armed Forces Americas',
        'AE' => 'Armed Forces Europe, Middle East, & Canada',
        'AK' => 'Alaska',
        'AL' => 'Alabama',
        'AP' => 'Armed Forces Pacific',
        'AR' => 'Arkansas',
        'AS' => 'American Samoa',
        'AZ' => 'Arizona',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DC' => 'District of Columbia',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'FM' => 'Federated States of Micronesia',
        'GA' => 'Georgia',
        'GU' => 'Guam',
        'HI' => 'Hawaii',
        'IA' => 'Iowa',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'MA' => 'Massachusetts',
        'MD' => 'Maryland',
        'ME' => 'Maine',
        'MH' => 'Marshall Islands',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MO' => 'Missouri',
        'MP' => 'Northern Mariana Islands',
        'MS' => 'Mississippi',
        'MT' => 'Montana',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'NE' => 'Nebraska',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NV' => 'Nevada',
        'NY' => 'New York',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'PW' => 'Palau',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VA' => 'Virginia',
        'VI' => 'Virgin Islands',
        'VT' => 'Vermont',
        'WA' => 'Washington',
        'WI' => 'Wisconsin',
        'WV' => 'West Virginia',
        'WY' => 'Wyoming'
    );
    
    /**
     * Returns the name of the state or territory
     * @param string $abbreviation The two character abbreviation to lookup
     * @return mixed If abbreviation is found, then the string name, else null
     */
    public static function getName($abbreviation)
    {
        return isset(UnitedStatesNames::$states[$abbreviation]) ? UnitedStatesNames::$states[$abbreviation] : null;
    }
    
    /**
     * Returns the two character abbreviation of the state or territory
     * @param string $name The state or territory to lookup
     * @return mixed If name is found, then the two character abbreviation, else null
     */
    public static function getAbbreviation($name)
    {
        foreach(UnitedStatesNames::$states as $key => $state) {
            if($name == $state) {
                return $key;
            }
        }
        
        return null;
    }
}