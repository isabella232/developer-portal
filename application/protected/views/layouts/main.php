<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css?2016-09" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/prism.css" />
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle . ' - ' . \Yii::app()->name); ?></title>
    <?php
    // Load the Yii-Bootstrap CSS/JS.
    Yii::app()->bootstrap->init();
    ?>
</head>

<body>
<?php 

// Set up the menu.
$this->widget('bootstrap.widgets.TbNavbar', array(
    'type' => 'inverse',
    'brand' => CHtml::encode(Yii::app()->name),
    'brandUrl' => $this->createAbsoluteUrl('/'),
    'collapse' => false, // requires bootstrap-responsive.css
    'items' => array(
        array(
            'class' => 'bootstrap.widgets.TbMenu',
            'items' => array(
                array(
                    'label' => 'Home',
                    'url' => array('/dashboard/'),
                    'active' => ($this->id == 'dashboard'),
                    'visible' => ( ! Yii::app()->user->isGuest),
                ),
                array(
                    'label' => 'Browse APIs',
                    'url' => array('api/'),
                    'active' => (($this->id == 'api') && ($this->route != 'api/playground')),
                ),
                array(
                    'label' => 'Keys',
                    'url' => array('key/'),
                    'active' => ($this->id == 'key'),
                    'visible' => ( ! (Yii::app()->user->isGuest || Yii::app()->user->checkAccess('admin'))),
                ),
                array(
                    'label' => 'Keys',
                    'items' => array(
                        array(
                            'label' => 'Active Keys',
                            'url' => array('/key/active/'),
                            'active' => ($this->route == 'key/active'),
                        ),
                        array(
                            'label' => 'Pending Keys',
                            'url' => array('/key/pending/'),
                            'active' => ($this->route == 'key/pending'),
                        ),
                        array(
                            'label' => 'My Keys',
                            'url' => array('/key/mine/'),
                            'active' => ($this->route == 'key/mine'),
                        ),
                    ),
                    'visible' => Yii::app()->user->role == 'admin',
                    'active' => ($this->id == 'key'),
                ),
                array(
                    'label' => 'Users',
                    'url' => array('/user/'),
                    'active' => ($this->id == 'user'),
                    'visible' => Yii::app()->user->checkAccess('admin'),
                ),
                array(
                    'label' => 'Playground',
                    'url' => array('/api/playground/'),
                    'active' => ($this->route == 'api/playground'),
                    'visible' => ( ! Yii::app()->user->isGuest),
                ),
                array(
                    'label' => 'FAQs',
                    'url' => array('/faq/'),
                    'active' => ($this->id == 'faq'),
                ),
                array(
                    'label' => 'Site Text',
                    'url' => array('/site-text/'),
                    'active' => ($this->id == 'siteText'),
                    'visible' => Yii::app()->user->checkAccess('admin'),
                ),
                array(
                    'label' => 'Event Log',
                    'url' => array('/event/'),
                    'active' => ($this->id == 'event'),
                    'visible' => Yii::app()->user->checkAccess('admin'),
                ),
            ),
        ),
        (
            Yii::app()->user->isGuest ?
            '<a class="btn btn-primary pull-right" href="' . $this->createUrl('/auth/login') . '">Login</a>' : 
            array(
                'class' => 'bootstrap.widgets.TbMenu',
                'htmlOptions' => array('class' => 'pull-right'),
                'items' => array(
                    array('label' => CHtml::encode(Yii::app()->user->name),
                        'visible' => !Yii::app()->user->isGuest,
                        'items' => array(
                            array('label' => 'Logout', 'url' => $this->createUrl('/auth/logout')),
                        ),
                    ),
                ),
            )
        ),
    ),
));

?>
<div class="container">
    <?php

    // Show the breadcrumbs (if any).
    if (isset($this->breadcrumbs)) {
        $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
            'homeLink' => false,
            'links' => $this->breadcrumbs,
        ));
    }

    // If there are any flash messages (alerts), show them.
    $this->widget('bootstrap.widgets.TbAlert', array());

    // Show the view content.
    echo $content;

    ?>
    <div class="clear"></div>
    <hr>
    <div id="footer">
        <img src="/img/logos/site-logo.png" class="pull-left footer-logo" />
        &copy; <?php echo date('Y'); ?> by SIL International Inc. | All Rights Reserved.<br/>
        Delivered by GTIS, USA Studio<br />
        <?php if ( ! Yii::app()->user->isGuest): ?>
            Built <?= \CHtml::encode(Utils::getApplicationBuildDate()); ?><br />
        <?php endif; ?>
        <a href="<?php echo Yii::app()->createUrl('/site/privacy-policy'); ?>">Privacy Policy</a>
        <?php if (( ! \Yii::app()->user->isGuest) && ( ! empty(\Yii::app()->params['contactUsUrl']))): ?>
            | <a href="<?= \CHtml::encode(\Yii::app()->params['contactUsUrl']); ?>">Contact Us</a>
        <?php endif; ?>
    </div><!-- footer -->
</div><!-- page -->
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/js/prism.js'; ?>"></script>
<?php
$this->renderPartial('//partials/google-analytics', array(
    'user' => Yii::app()->user
));

if ( ! Yii::app()->user->isGuest) {
    ?><script type="text/javascript" src="https://www.jira.insitehome.org/s/en_USly0wxi-1988229788/6100/10/1.4.0-m3/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=04a5241e"></script>
    <?php
}
?>
</body>
</html>
