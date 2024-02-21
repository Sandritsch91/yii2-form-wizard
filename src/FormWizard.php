<?php

namespace sandritsch91\yii2\formwizard;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Tabs;
use yii\bootstrap5\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class FormWizard
 * @package sandritsch91\yii2\formwizard
 */
class FormWizard extends Widget
{
    /**
     * Container html options
     * The id will be set automatically
     * @var array
     */
    public $options = [];

    /**
     * The model to use in the form
     * @var Model
     */
    public Model $model;

    /**
     * If each step should be validated before the next step is shown
     *
     * Define the steps to validate in an array.
     * Each array element is an array of attribute names that should be validated together.
     *
     * Example:
     * ```php
     * 'validateSteps' => [
     *      ['name', 'email'],
     *      ['subject', 'body']
     *      // etc, define EVERY step here, leave empty if no validation is needed
     * ]
     * ```
     *
     * Be sure to define the rules for the attributes in the model.
     *
     * @see Model::rules()
     * @var array
     */
    public array $validateSteps;

    /**
     * Form options for ```yii\widgets\ActiveForm```
     * @var array
     */
    public array $formOptions = [];

    /**
     * Options for each step
     *
     * The steps are build with the Tab widget ```yii\bootstrap5\Tabs```
     * In addition to the default options, you can also specify the following options:
     * - items['view']: string, the view file to render. Passed parameters are $model and $form
     * - items['items'] is not allowed
     * - items['url'] is not allowed
     * @see Tabs
     * @var array
     */
    public array $tabOptions = [];

    /**
     * Default button html options
     * Contains 3 sub arrays for previous, next and finish button
     *
     * If you change the data-formwizard attribute, you have to change the clientOption selectors as well
     * @var array
     */
    public array $buttonOptions = [];

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        // check if necessary options are set
        if (empty($this->model)) {
            throw new InvalidConfigException('You must specify the model class.');
        }
        if (empty($this->tabOptions['items'])) {
            throw new InvalidConfigException('You must specify at least one step.');
        }

        // translations
        $this->registerTranslations();

        // Remove items and url from tabOptions
        foreach ($this->tabOptions['items'] as $key => &$item) {
            ArrayHelper::remove($item, 'items');
            ArrayHelper::remove($item, 'url');
            if (!isset($item['label'])) {
                $item['label'] = \Yii::t('sandritsch91/yii2-form-wizard', 'Step {0}', [$key + 1]);
            }
        }

        // Options
        $this->options = ArrayHelper::merge($this->options, [
            'class' => 'form-wizard'
        ]);
        $this->clientOptions['containerSelector'] = "#" . $this->options['id'];

        // Button Options
        $this->buttonOptions['previous'] = ArrayHelper::merge(
            $this->buttonOptions['previous'] ?? [],
            ['data' => ['formwizard' => 'previous']]
        );
        $this->buttonOptions['next'] = ArrayHelper::merge(
            $this->buttonOptions['next'] ?? [],
            ['data' => ['formwizard' => 'next']]
        );
        $this->buttonOptions['finish'] = ArrayHelper::merge(
            $this->buttonOptions['finish'] ?? [],
            ['data' => ['formwizard' => 'finish']]
        );
        if (!isset($this->buttonOptions['previous']['class'])) {
            $this->buttonOptions['previous']['class'] = ['btn', 'btn-primary'];
        }
        if (!isset($this->buttonOptions['next']['class'])) {
            $this->buttonOptions['next']['class'] = ['btn', 'btn-primary'];
        }
        if (!isset($this->buttonOptions['finish']['class'])) {
            $this->buttonOptions['finish']['class'] = ['btn', 'btn-primary'];
        }

        // Set default selectors
        if (!isset($this->clientOptions['previousSelector'])) {
            $this->clientOptions['previousSelector'] = '[data-' . trim(Html::renderTagAttributes($this->buttonOptions['previous']['data'])) . ']';
        }
        if (!isset($this->clientOptions['nextSelector'])) {
            $this->clientOptions['nextSelector'] = '[data-' . trim(Html::renderTagAttributes($this->buttonOptions['next']['data'])) . ']';
        }
        if (!isset($this->clientOptions['finishSelector'])) {
            $this->clientOptions['finishSelector'] = '[data-' . trim(Html::renderTagAttributes($this->buttonOptions['finish']['data'])) . ']';
        }

        // Pass validateSteps to clientOptions
        if (isset($this->validateSteps)) {
            $this->clientOptions['validateSteps'] = $this->validateSteps;
        }
    }

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function run(): string
    {
        // Build html
        echo Html::beginTag('div', $this->options);

        $activeForm = ActiveForm::begin($this->formOptions);

        // render views
        foreach ($this->tabOptions['items'] as &$item) {
            if (isset($item['view'])) {
                $item['content'] = $this->render($item['view'], [
                    'model' => $this->model,
                    'form' => $activeForm
                ]);
            }
        }

        // Nav
        $nav = Tabs::widget($this->tabOptions);

        // Add buttons
        $buttons = '';
        if (count($this->tabOptions['items']) > 1) {
            $buttons = Html::button(
                    \Yii::t('sandritsch91/yii2-form-wizard', 'Previous'),
                    $this->buttonOptions['previous']
                ) . "&nbsp;";
            $buttons .= Html::button(
                    \Yii::t('sandritsch91/yii2-form-wizard', 'Next'),
                    $this->buttonOptions['next']
                ) . "&nbsp;";
        }
        $buttons .= Html::submitButton(
            \Yii::t('sandritsch91/yii2-form-wizard', 'Finish'),
            $this->buttonOptions['finish']
        );

        // Render
        echo $nav . $buttons;
        $activeForm->end();
        echo Html::endTag('div');

        $this->registerClientScript();

        return '';
    }

    /**
     * {@inheritDoc}
     * @param bool $autoGenerate
     * @return string|null
     */
    public function getId($autoGenerate = true): ?string
    {
        return parent::getId($autoGenerate) . "_formWizard";
    }

    public function registerClientScript(): void
    {
        $view = $this->getView();
        FormWizardAsset::register($view);

        $id = $this->clientOptions['containerSelector'];
        $options = Json::encode($this->clientOptions);
        $this->clientEvents = Json::encode($this->clientEvents);
        $view->registerJs("window.formWizard = new FormWizard('$id', $options, $this->clientEvents);");
    }

    /**
     * Register translations
     */
    protected function registerTranslations(): void
    {
        \Yii::$app->i18n->translations['sandritsch91/yii2-form-wizard'] = [
            'class' => 'yii\i18n\GettextMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@sandritsch91/yii2/formwizard/messages',
            'forceTranslation' => true
        ];
    }
}
