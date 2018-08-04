<?php

require 'vendor/autoload.php';

use Curia\Validation\Factory;
use Curia\Validation\Translator;
use Curia\Validation\ArrayLoader;
use Curia\Validation\DatabasePresenceVerifier;

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$translator = new Translator(new ArrayLoader(), 'zh');
$factory = new Factory($translator);
$factory->setPresenceVerifier(
    new DatabasePresenceVerifier
);
// $factory = new Factory;

$validator = $factory->make([
	'username' => '1',
	'email' => '123',
	'phone' => 123,
	'password' => '123'
],[
    'username' => 'required|min:2|max:20|string',
    'email' => 'required|unique:users|email',
    'phone' => 'digits_between:5,20|unique:users',
    'password' => 'required|string|min:4|confirmed',
] /*[
    'username.required' => '请输入姓名',
    'username.min' => '姓名至少2个字符',
    'username.max' => '姓名太长了吧',
    'phone.digits_between' => '请输入正确手机号',
    'password.min' => '密码至少4个字符',
    'password.max' => '密码太长了吧',
]*/);

dd($validator->errors());
