<?php

/** @var User $model */

use sandritsch91\yii2\formwizard\tests\Support\Data\models\User;

echo \sandritsch91\yii2\formwizard\FormWizard::widget([
    'model' => $model,
    'tabOptions' => [
        'items' => [
            [
                'label' => 'Step 1',
                'view' => '@app/Support/Data/views/site/step1'
            ],
            [
                'label' => 'Step 2',
                'view' => '@app/Support/Data/views/site/step2'
            ],
            [
                'label' => 'Step 3',
                'view' => '@app/Support/Data/views/site/step3'
            ]
        ],
        'navType' => 'nav-pills'
    ],
    'validateSteps' => [
        ['firstname', 'lastname'],
        ['username', 'password', 'password_validate'],
        ['email']
    ],
]);
