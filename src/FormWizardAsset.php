<?php

namespace sandritsch91\yii2\formwizard;

use yii\web\AssetBundle;

class FormWizardAsset extends AssetBundle
{
    public $sourcePath = '@sandritsch91/yii2/formwizard/assets';

    public $js = [
        'js/form-wizard.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
}
