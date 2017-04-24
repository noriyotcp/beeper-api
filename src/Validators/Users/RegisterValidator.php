<?php

namespace BeeperApi\Validators\Users;

use BeeperApi\Validators\Validator;

class RegisterValidator extends Validator
{
    protected function rules()
    {
        $this->validator->rule('required', 'username');
        $this->validator->rule('lengthBetween', 'username', 3, 30);
        $this->validator->rule('notIn', 'username', ['me']);

        $this->validator->rule('required', 'email');
        $this->validator->rule('email', 'email');

        $this->validator->rule('required', 'password');
        $this->validator->rule('lengthBetween', 'password', 5, 100);
    }
}