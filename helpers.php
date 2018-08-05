<?php

if (! function_exists('request')) {
    function request()
    {
        return new Curia\YiiValidation\Request;
    }
}

if (! function_exists('validator')) {
	function validator(array $data, array $rules, array $messages = [])
	{
		return \Yii::$app->validator->make($data, $rules, $messages);
	}
}

if (! function_exists('resolve')) {
	function resolve($name)
	{
		return \Yii::$container->get($name);
	}
}
