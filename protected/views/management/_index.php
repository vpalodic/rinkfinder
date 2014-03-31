<?php
    /**
     * This is the generic and dynamic view for the various management 
     * summaries that are requested
     * 
     * @var $this ManagementController
     * @var $data []
     * @var $headers []
     * 
     */
?>
<?php
    if((integer)$data['count'] == 0) {
         echo '<h3 class="sectionSubHeader">No Records Found!</h3>';
         Yii::app()->end();
    }
?>
<label class="control-label" for="tableFilter" style="display: inline">
    Search:
</label>
<input id="tableFilter" type="text" class="input-medium search-query" />
<br />
<br />
<?php
    $headerCount = count($headers);
    $itemCount = (integer)$data['count'];
    
    if($itemCount != count($data['items'])) {
        throw new CHttpException(500);
    }
    
    $items = $data['items'];
    
    // Now we have to setup our Footable!
    // Start with the <table> tag
    $table = '<table id="' . $data['model'] . 'Footable" class="items '
            . 'table table-striped table-bordered table-condensed '
            . 'table-hover footable toggle-large toggle-circle" '
            . 'style="padding: 0px;" data-filter="#tableFilter">';
    
    // Now we setup the <thead> and <tbody> tags!!!
    $tableHeader = '<thead><tr>';
    
    foreach($headers as $header) {
        // A header item may contain one of the following fields:
        // name - The field name from the database query. This is used as
        // a key for the item being processed.
        // display - The header name to show the user
        // type - The data type, if numeric and item has a dataConvert
        // property set then the data-value property of the <td> will be
        // set to the value of the dataConvert property.
        // hide - If set, then the data-hide property of the <th> tag will
        // be set to the value of this field
        // link - If set and the item has an endpoint property, then the
        // value will be wrapped in an anchor tag that opens the endpoint
        // in a new tab/window.
        
        // Temp header that will be added to the main header at the end of
        // the loops
        $th = '<th';
        
        if(isset($header['type'])) {
            $th .= ' data-type="' . $header['type'] . '"';
        }
        
        if(isset($header['hide'])) {
            $th .= ' data-hide="' . $header['hide'] . '"';
        }
        
        $th .= '>' . $header['display'] . '</th>';
        
        $tableHeader .= $th;
    }
    
    $tableHeader .= '</tr></thead>';

    $tableBody = '<tbody>';
    
    $odd = true;
    
    foreach($items as $item) {
        $tr = '<tr>';
        
        foreach($headers as $field => $header) {
            $td = '<td';
            
            if(isset($header['type']) && $header['type'] == 'numeric' && 
                    isset($item['dataConvert']) && isset($item['dataConvert'][$field])) {
                $str = filter_var($item['dataConvert'][$field], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $td .= ' data-value="' . $str . '"';
            } elseif(isset($header['type']) && $header['type'] == 'numeric' && isset($item[$field])) {
                $str = filter_var($item[$field], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $td .= ' data-value="' . $str . '"';
            }
            
            $td .= '>';
            
            if(isset($header['link']) && isset($item['endpoint']) && isset($item[$field])) {
                $td .= '<a target="_blank" href="' . $item['endpoint'] . '">'
                        . $item[$field] . '</a></td>';
            } elseif(isset($item[$field])) {
                $td .= $item[$field] . '</td>';
            } else {
                $td .= '</td>';
            }
            
            $tr .= $td;
        }
        
        $tr .= '</tr>';
        $tableBody .= $tr;
    }
    
    $tableBody .= '</tbody>';

    $tableFooter = '<tfoot class="hide-if-no-paging"><tr><td colspan="'
            . $headerCount . '"><div class="pagination pagination-centered">'
            . '</div></td></tr></tfoot>';
    
    // Put it all together now!
    $table .= $tableHeader . $tableBody . $tableFooter;
    $table .= '</table>';
    
    echo $table;
?>

<script type="text/javascript">
    $(".footable").footable();
</script>