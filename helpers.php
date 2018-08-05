<?php

if (! function_exists('request')) {
    function request()
    {
        return new \Curia\YiiValidation\Request;
    }
}
