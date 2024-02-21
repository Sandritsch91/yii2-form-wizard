<?php

namespace sandritsch91\yii2\formwizard\tests\Support\Data\controllers;

use sandritsch91\yii2\formwizard\tests\Support\Data\models\User;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex(): string
    {
        $model = new User();
        if (\Yii::$app->request->post() && $model->load(\Yii::$app->request->post()) && $model->validate()) {
            die;
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }
}
