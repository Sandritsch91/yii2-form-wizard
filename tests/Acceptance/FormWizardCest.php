<?php

namespace sandritsch91\yii2\formwizard\tests\Acceptance;

use Codeception\Util\Locator;
use sandritsch91\yii2\formwizard\tests\Support\AcceptanceTester;
use yii\helpers\Url;

class FormWizardCest
{
    public function _before(AcceptanceTester $I)
    {

    }

    // tests
    public function render(AcceptanceTester $I): void
    {
        $I->amOnPage(Url::toRoute(['/site/test']));

        $I->see('Step 1');
        $I->seeElement(Locator::find('a', ['class' => 'nav-link active', 'id' => 'step1-link']));
        $I->see('Step 2');
        $I->see('Step 3');

        $I->seeElement(Locator::find('input', ['name' => 'User[firstname]']));
        $I->seeElement(Locator::find('input', ['name' => 'User[lastname]']));

        $I->see('Next');
        $I->see('Previous');

        $I->seeElement(Locator::find('button', ['data-formwizard' => 'next']));
        $I->seeElement(Locator::find('button', ['data-formwizard' => 'previous']));
        $I->dontSeeElement(Locator::find('button', ['class' => 'd-none', 'data-formwizard' => 'finish']));

        $I->see('some test variable');
    }

    public function navigation(AcceptanceTester $I): void
    {
        $I->amOnPage(Url::toRoute('/site/test'));

        $I->seeElement('button', ['data-formwizard' => 'next']);
        $I->seeElement('button', ['data-formwizard' => 'previous']);
        $I->dontSeeElement('button', ['data-formwizard' => 'finish']);

        $I->click('Next');
        $I->wait(1);
        $I->seeValidationError('Firstname cannot be blank.');
        $I->seeValidationError('Lastname cannot be blank.');

        $I->fillField('User[firstname]', 'John');
        $I->fillField('User[lastname]', 'Doe');
        $I->click('Next');
        $I->wait(1);

        $I->seeElement(Locator::find('a', ['class' => 'nav-link active', 'id' => 'step2-link']));

        $I->click('Next');
        $I->wait(1);
        $I->seeValidationError('Username cannot be blank.');
        $I->seeValidationError('Password cannot be blank.');
        $I->seeValidationError('Password Validate cannot be blank.');

        $I->fillField('User[username]', 'johndoe');
        $I->fillField('User[password]', 'password');
        $I->fillField('User[password_validate]', 'password');
        $I->click('Next');
        $I->wait(1);

        $I->seeElement(Locator::find('a', ['class' => 'nav-link active', 'id' => 'step3-link']));
        $I->seeElement('button', ['data-formwizard' => 'previous']);
        $I->dontSeeElement('button', ['data-formwizard' => 'next']);
        $I->seeElement('button', ['data-formwizard' => 'finish']);
        $I->wait(1);

        $I->scrollTo(Locator::find('button', ['data-formwizard' => 'finish']), 0, 150);
        $I->wait(2);

        $position = $I->executeJS('return window.formWizard.positions[3]');
        $I->assertGreaterThan(0, $position);

        $I->click('Previous');
        $I->wait(1);
        $I->click('Next');
        $I->wait(1);

        $position = $I->executeJS('return window.scrollY');
        $I->assertGreaterThan(0, $position);

        $I->scrollTo(Locator::find('button', ['data-formwizard' => 'finish']), 0, 150);
        $I->wait(3);

        $I->click('Finish');
        $I->wait(1);
        $I->seeValidationError('Email cannot be blank.');

        $I->fillField('User[email]', 'john.doe@example.com');

        $I->scrollTo(Locator::find('button', ['data-formwizard' => 'finish']), 0, 150);
        $I->wait(3);

        $I->click('Finish');
        $I->wait(1);
        $I->see('success');
    }
}
