<?php

/** @var User $model */

use sandritsch91\yii2\formwizard\FormWizard;
use sandritsch91\yii2\formwizard\tests\Support\Data\models\User;

echo FormWizard::widget([
    'model' => $model,
    'tabOptions' => [
        'items' => [
            [
                'label' => 'Step 1',
                'view' => '@app/Support/Data/views/site/step1',
                'linkOptions' => [
                    'id' => 'step1-link',
                ]
            ],
            [
                'label' => 'Step 2',
                'view' => '@app/Support/Data/views/site/step2',
                'linkOptions' => [
                    'id' => 'step2-link',
                ]
            ],
            [
                'label' => 'Step 3',
                'view' => '@app/Support/Data/views/site/step3',
                'linkOptions' => [
                    'id' => 'step3-link',
                ]
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
