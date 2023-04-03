<?php
session_start();

require 'CaptchaGenerator.php';

$captchaInstance = new CaptchaGenerator('lilita', 'jpeg', true);

$captchaInstance->generate(6);