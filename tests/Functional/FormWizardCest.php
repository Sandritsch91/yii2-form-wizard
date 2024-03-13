<?php

namespace sandritsch91\yii2\formwizard\tests\Functional;

use Codeception\Util\Locator;
use sandritsch91\yii2\formwizard\tests\Support\FunctionalTester;
use yii\helpers\Url;

class FormWizardCest
{
    public function _before(FunctionalTester $I)
    {

    }

    // tests
    public function render(FunctionalTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/index'));

        $I->see('Step One');
        $I->see('Schritt 2');
        $I->see('Schritt 3');

        $I->seeElement(Locator::find('a', ['class' => 'nav-link active', 'id' => 'step1-link']));
        $I->dontSeeElement(Locator::find('a', ['class' => 'nav-link active', 'id' => 'step2-link']));
        $I->dontSeeElement(Locator::find('a', ['class' => 'nav-link active', 'id' => 'step3-link']));

        $I->seeElement(Locator::find('input', ['name' => 'User[firstname]']));
        $I->seeElement(Locator::find('input', ['name' => 'User[lastname]']));
        $I->seeElement(Locator::find('input', ['name' => 'User[password]']));
        $I->seeElement(Locator::find('input', ['name' => 'User[password_validate]']));
        $I->seeElement(Locator::find('input', ['name' => 'User[email]']));

        $I->see('Weiter');
        $I->see('Zurück');
        $I->see('Abschliessen');

        $I->seeElement(Locator::find('button', ['data-formwizard' => 'next']));
        $I->seeElement(Locator::find('button', ['data-formwizard' => 'previous']));
        $I->dontSeeElement(Locator::find('button', ['class' => 'd-none', 'data-formwizard' => 'finish']));
    }
}
