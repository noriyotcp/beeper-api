<?php

namespace BeeperApi\Validators\Beeps;

use BeeperApi\Validators\Validator;

class CreateBeepValidator extends Validator
{
    protected function rules()
    {
        $this->validator->rule('required', 'text');
        $this->validator->rule('lengthBetween', 'text', 3, 320);
    }
}