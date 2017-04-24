<?php

namespace BeeperApi\Validators;

use BeeperApi\Exceptions\ApiException;
use Http\Request;

abstract class Validator
{
    protected $validator;

    public function __construct(Request $request)
    {
        $this->validator = new \Valitron\Validator(array_merge($request->getParameters(), $_FILES));
        $this->rules();
    }

    public function validate()
    {
        if (!$this->validator->validate()) {
            $errors = [];
            //loop through nested array and get values
            $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->validator->errors()));
            foreach($it as $e) {
                $errors[] = $e;
            }
            throw new ApiException(422, $errors);
        }
    }

    abstract protected function rules();

}