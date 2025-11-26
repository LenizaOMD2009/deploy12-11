<?php

namespace app\controller;

use app\trait\Template;

abstract class Base
{
    use Template;
    public function calculateBirthDate()
    {
        return date('Y-m-d');
    }
}
