<?php

namespace sandritsch91\yii2\formwizard\tests\Support\Data\models;

use yii\base\Model;

class User extends Model
{
    public string $firstname = '';
    public string $lastname = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_validate = '';

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['firstname', 'lastname', 'username', 'email', 'password', 'password_validate'], 'required'],
            ['email', 'email'],
            ['password_validate', 'compare', 'compareAttribute' => 'password'],
        ];
    }
}
