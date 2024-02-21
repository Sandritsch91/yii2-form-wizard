<?php


namespace sandritsch91\yii2\formwizard\tests\Unit;

use Codeception\Test\Unit;
use sandritsch91\yii2\formwizard\FormWizard;
use sandritsch91\yii2\formwizard\tests\Support\Data\models\User;
use sandritsch91\yii2\formwizard\tests\Support\UnitTester;
use yii\base\InvalidConfigException;
use yii\web\Application;

class FormWizardTest extends Unit
{
    protected UnitTester $tester;
    protected User $model;
    protected array $tabOptions = [
        'options' => [
            'class' => [
                'mb-3'
            ]
        ],
        'items' => [
            [
                'label' => 'Step 1',
                'content' => 'Just test-content'
            ],
            [
                'label' => 'Step 2',
                'content' => 'Just test-content'
            ],
            [
                'label' => 'Step 3',
                'content' => 'Just test-content'
            ]
        ],
        'navType' => 'nav-pills'
    ];

    /**
     * @throws InvalidConfigException
     */
    protected function _before(): void
    {
        $config = require __DIR__ . '/../Support/Data/config.php';
        $_SERVER['REQUEST_URI'] = '/test';

        \Yii::$app = new Application($config);
        $this->model = new User();
    }

    // tests

    public function testMissingProperties()
    {
        $this->tester->expectThrowable(InvalidConfigException::class, function () {
            new FormWizard();
        });
        $this->tester->expectThrowable(InvalidConfigException::class, function () {
            new FormWizard([
                'model' => $this->model
            ]);
        });
    }

    /**
     * @throws \Throwable
     * @throws InvalidConfigException
     */
    public function testConfig()
    {
        $wizard = new FormWizard([
            'model' => $this->model,
            'tabOptions' => $this->tabOptions
        ]);
        $this->tester->assertInstanceOf(FormWizard::class, $wizard);

        $wizard = new FormWizard([
            'model' => $this->model,
            'tabOptions' => $this->tabOptions,
            'options' => [
                'id' => 'newId'
            ],
            'buttonOptions' => [
                'previous' => [
                    'id' => 'previous',
                    'class' => ['btn', 'btn-warning'],
                ],
            ],
            'clientOptions' => [
                'previousSelector' => '#previous',
            ]
        ]);
        $this->tester->assertEquals('#newId', $wizard->clientOptions['containerSelector']);

        $this->tester->assertArrayHasKey('previous', $wizard->buttonOptions);
        $this->tester->assertArrayHasKey('next', $wizard->buttonOptions);
        $this->tester->assertArrayHasKey('finish', $wizard->buttonOptions);

        $this->tester->assertArrayHasKey('class', $wizard->buttonOptions['previous']);
        $this->tester->assertArrayHasKey('data', $wizard->buttonOptions['previous']);
        $this->tester->assertArrayHasKey('class', $wizard->buttonOptions['next']);
        $this->tester->assertArrayHasKey('data', $wizard->buttonOptions['next']);
        $this->tester->assertArrayHasKey('class', $wizard->buttonOptions['finish']);
        $this->tester->assertArrayHasKey('data', $wizard->buttonOptions['finish']);

        $this->tester->assertEquals(['btn', 'btn-warning'], $wizard->buttonOptions['previous']['class']);
        $this->tester->assertEquals(['btn', 'btn-primary'], $wizard->buttonOptions['next']['class']);

        $this->tester->assertEquals('#previous', $wizard->clientOptions['previousSelector']);
        $this->tester->assertEquals('[data-formwizard="next"]', $wizard->clientOptions['nextSelector']);
        $this->tester->assertEquals('[data-formwizard="finish"]', $wizard->clientOptions['finishSelector']);

        $html = $wizard->run();
        $this->tester->assertIsString($html);
    }
}
