<?php

use sandritsch91\yii2\formwizard\tests\Support\Data\models\User;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var User $model */
/** @var ActiveForm $form */
/** @var array $params */

echo $form->field($model, 'firstname')->textInput();
echo $form->field($model, 'lastname')->textInput();

echo Html::tag('p', $params['test']);
