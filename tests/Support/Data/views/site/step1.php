<?php

use sandritsch91\yii2\formwizard\tests\Support\Data\models\User;
use yii\bootstrap5\ActiveForm;

/** @var User $model */
/** @var ActiveForm $form */

echo $form->field($model, 'firstname')->textInput();
echo $form->field($model, 'lastname')->textInput();
