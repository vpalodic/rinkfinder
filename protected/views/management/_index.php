<?php
    /**
     * This is the generic and dynamic view for the various management 
     * summaries that are requested
     * 
     * @var $this ManagementController
     * @var $data []
     * @var $headers []
     * @var $doReady boolean
     * @var $path string
     * @var $jsFile string
     * 
     */
?>
<?php if((integer)$data['count'] == 0) : ?>
    <h3 class="sectionSubHeader">No Records Found!</h3>
<?php else: ?>
<form class="form-search">
    <div class="input-prepend">
        <span class="add-on">Filter</span>
        <input id="tableFilter" type="text" class="input-medium search-query" placeholder="Filter" />
    </div>
    <?php if(isset($data['types'])) : ?>
    <span> </span>
    <div id="tableFilterTypeGroup" class="input-prepend">
        <span class="add-on">Type:</span>
        <select class="input-medium search-query" id="tableFilterType" placeholder="Type">
            <option value=""></option>
            <?php
                foreach($data['types'] as $key => $display) {
                    echo '<option value="' . $key . '">' . $display . '</option>';
                }
            ?>
        </select>
    </div>
    <?php endif; ?>
    <?php if(isset($data['statuses'])) : ?>
    <span> </span>
    <div id="tableFilterStatusGroup" class="input-prepend">
        <span class="add-on">Status:</span>
        <select class="input-medium search-query" id="tableFilterStatus" placeholder="Status">
            <option value=""></option>
            <?php
                foreach($data['statuses'] as $key => $display) {
                    echo '<option value="' . $key . '">' . $display . '</option>';
                }
            ?>
        </select>
    </div>
    <?php endif; ?>
</form>

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
            . 'table-hover footable toggle-circle" '
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
            
            if(isset($header['link']) && isset($item[$header['link']]) && 
                    isset($item[$field]) && (!isset($header['linkArray']) || $header['linkArray'] == false)) {
                $td .= '<a target="_blank" href="' . $item[$header['link']] . '">';
                if(isset($header['linkText'])) {
                    $td .= $header['linkText'];
                } else {
                    $td .= $item[$field];
                }
                
                $td .= '</a></td>';
            } elseif(isset($header['link']) && isset($item[$header['link']]) && 
                    isset($item[$field]) && (isset($header['linkArray']) && $header['linkArray'] == true && is_array($item[$field]))) {
                $temp = $item[$field];
                $tempCount = count($temp);
                
                for($i = 0; $i < $tempCount; $i++) {
                    $td .= '<a target="_blank" href="' . $item[$header['link']][$i] . '">'
                        . $temp[$i] . '</a>';
                    
                    if($i + 1 < $tempCount) {
                        $td .= '<span>, </span>';
                    }
                }
                
                $td .= '</td>';
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

<?php if($doReady) : ?>
<?php
    Yii::app()->clientScript->registerScript(
            'doReady_Index',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . '_index.data = ' . json_encode($data) . ';'
            . '_index.headers = ' . json_encode($headers) . ';'
            . '_index.loadScriptFile = true;'
            . '_index.scriptFile = "' . $jsFile . '";'
            . '_index.onReady();',
            CClientScript::POS_READY
    );
?>
<?php else: ?>
<script type="text/javascript">
    $(document).ready(function () {
    utilities.urls.login = "<?php echo $this->createUrl('site/login'); ?>";
    utilities.urls.logout = "<?php echo $this->createUrl('site/logout'); ?>";
    utilities.urls.base = "<?php echo Yii::app()->request->baseUrl; ?>";
    utilities.urls.assets = "<?php echo $path; ?>";
    utilities.debug = <?php echo (defined('YII_DEBUG') ? 'true' : 'false'); ?>;
    _index.data = <?php echo json_encode($data); ?>;
    _index.headers = <?php echo json_encode($headers); ?>;
    _index.loadScriptFile = true;
    _index.scriptFile = "<?php echo $jsFile; ?>";
    _index.onReady();
});
</script>
<?php endif; ?>
<?php endif; ?>