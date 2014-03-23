<?php
    /* @var $this SiteController */
    /* @var $managers User[] */

    $this->pageTitle = Yii::app()->name . ' - Administration';
    $this->breadcrumbs = array(
        'Administration'
    );
?>

<h2 class="sectionHeader">Administration</h2>
<?php

                        array(
                            'label'=> 'Site Administration',
                            //'url' => array('/user/admin'),
                            //'visible'=>Yii::app()->getModule('user')->isAdmin(),
                            'items' => array(
                                array('label' => 'User Options'),
                                array(
                                    'label' => 'Create New User',
                                    'url' => array('/user/create'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'List Users',
                                    'url' => array('/user'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'Manage Users',
                                    'url' => array('/user/admin'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                TbHtml::menuDivider(),
                                array('label' => 'Profile Field Options'),
                                array(
                                    'label' => 'Create New Profile Field',
                                    'url' => array('/profileField/create'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'Manage Profile Fields',
                                    'url' => array('/profileField/admin'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                TbHtml::menuDivider(),
                                array(
                                    'label' => 'Authorization & Access Control',
                                    'url' => array('/rbam'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                            )
                        );
?>