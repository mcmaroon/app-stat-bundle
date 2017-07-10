<?php

namespace App\StatBundle\Helper;

interface InterfaceStatCommand {

    public function fetchMethods($methodName = null);
}
