<?php
$pluginPath = dirname(__FILE__).'/../../..';
include($pluginPath.'/test/bootstrap.php');
require_once($pluginPath.'/lib/helper/sfUoStringHelper.class.php');


$test = new lime_test(21, new lime_output_color());

$test->diag('sfUnobstrusiveWidgetPlugin : test sfUoStringHelper class');


$test->diag('sfUoStringHelper::camelizeLcFirst()');

$test->is(sfUoStringHelper::camelizeLcFirst('foo'), 'foo', "::camelizeLcFirst() camelize a string and set first character to lower case");
$test->is(sfUoStringHelper::camelizeLcFirst('fooBar'), 'fooBar', "::camelizeLcFirst() camelize a string and set first character to lower case");
$test->is(sfUoStringHelper::camelizeLcFirst('foo_bar'), 'fooBar', "::camelizeLcFirst() camelize a string and set first character to lower case");
$test->is(sfUoStringHelper::camelizeLcFirst('foo_Bar'), 'fooBar', "::camelizeLcFirst() camelize a string and set first character to lower case");
$test->is(sfUoStringHelper::camelizeLcFirst('foo_bar_foo_bar'), 'fooBarFooBar', "::camelizeLcFirst() camelize a string and set first character to lower case");


$test->diag('sfUoStringHelper::getJavascriptConfigurationCallback()');

$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('', ''), null, "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', ''), null, "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('', 'bar'), null, "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', array()),  null, "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', 'bar'),  'foo: "bar"', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', true),  'foo: true', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('bar', false),  'bar: false', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo()', 'bar'),  'foo: bar', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', 0),  'foo: 0', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', 5),  'foo: 5', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', array('bar')),  'foo: ["bar"]', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', array('foo'=>'bar')),  'foo: {foo: "bar"}', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', array('foo'=>'bar', 'foo1'=>'bar1')),  'foo: {foo: "bar", foo1: "bar1"}', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', array('foo'=>array('foo1'=>'bar1'))),  'foo: {foo: {foo1: "bar1"}}', "::getJavascriptConfigurationCallback() return a JavaScript configuration");
$test->is(sfUoStringHelper::getJavascriptConfigurationCallback('foo', array('data'=>array('test', 'test 2', 'foo', 'bar'))),   'foo: {data: ["test", "test 2", "foo", "bar"]}', "::getJavascriptConfigurationCallback() return a JavaScript configuration");


$test->diag('sfUoStringHelper::getJavascriptConfiguration()');
$test->is(
  sfUoStringHelper::getJavascriptConfiguration(
    array(
      'empty_string_toto'=>'',
      'bool'=>false,
      'int'=>345,
      'float'=>34.6,
      'function()'=>'aCallBack',
      'string'=>'foo bar',
      'array'=>array(
        'bool'=>false,
        'int'=>345,
        'float'=>34.6,
        'function()'=>'aCallBack',
        'string'=>'foo bar',
        'array'=>array(
          'bool'=>false,
          'empty_string_toto'=>'',
          'int'=>345,
          'float'=>34.6,
          'function()'=>'aCallBack',
          'string'=>'foo bar',
        ),
      ),
    )
  ),
  'bool: false, int: 345, float: 34.6, function: aCallBack, string: "foo bar", array: {bool: false, int: 345, float: 34.6, function: aCallBack, string: "foo bar", array: {bool: false, int: 345, float: 34.6, function: aCallBack, string: "foo bar"}}',
  "::getJavascriptConfiguration() return a complete JavaScript configuration");

