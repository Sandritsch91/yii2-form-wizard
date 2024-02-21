<?php

declare(strict_types=1);

namespace sandritsch91\yii2\formwizard\tests\Support;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * Define custom actions here
     */
    public function seeValidationError($message): void
    {
        $this->see($message, '.invalid-feedback');
    }

    public function dontSeeValidationError($message): void
    {
        $this->dontSee($message, '.invalid-feedback');
    }
}
