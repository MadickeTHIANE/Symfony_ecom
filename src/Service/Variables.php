<?php

namespace App\Service;

class Variables
{
        public function getVariables($arg)
        {
                $test = "Ceci est un test afin de voir si le service Variables fonctionne. $arg";
                return $test;
        }
}
