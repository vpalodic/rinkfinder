<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="row-fluid">
    <div class="span3">
        <div id="sidebar">
        <?php
            $this->beginWidget('zii.widgets.CPortlet',
                               array('title' => 'Operations',
                                    )
                              );
                $this->widget('bootstrap.widgets.TbNav',
                              array('type' => TbHtml::NAV_TYPE_LIST,
                                    'activateParents' => true,
                                    'items' => $this->menu,
                                    'htmlOptions' => array('class' => 'bs-docs-sidenav'),
                                   )
                             );
            $this->endWidget();
        ?>
        </div><!-- sidebar -->
    </div>
    <div class="span9">
        <div id="content">
            <?php echo $content; ?>
        </div><!-- content -->
    </div>
</div>
<?php $this->endContent(); ?>