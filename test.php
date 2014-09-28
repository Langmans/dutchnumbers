#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$args = $argv;
array_shift($args);

echo call_user_func_array(array('DutchNumbers', 'format'), $args), PHP_EOL;
