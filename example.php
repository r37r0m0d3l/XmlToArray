<?php
include 'xml_into_array.php';
# Simple xml without attributes
$xml = '<?xml version="1.0" encoding="utf-8"?>
<data>
	<extra1>1111</extra1>
	<extra2>2222</extra2>
	<extra3>3333</extra3>
</data>';
# Full parsing, array have root element
$array = xml_into_array::xml_to_array($xml);
/*
array(
	'data' => array(
		'extra1' => 1111,
		'extra2' => 2222,
		'extra3' => 3333,
	),
)
*/
# Far more complex xml
$xml = '<?xml version="1.0" encoding="utf-8"?>
<data>
	<id>1001</id>
	<info att1="one" att2="two"></info>
	<extra>1111</extra>
	<extra>2222</extra>
	<extra>3333</extra>
</data>';
# More informative array
$array = xml_into_array::xml_to_array($xml);
/*
array(
	'data' => array(
		'id' => 1001,
		'info' => null,
		'@info' => array(
			'att1' => 'one',
			'att2' => 'two',
		),
		'extra' => array(
			0 => 1111,
			1 => 2222,
			2 => 3333,
		),
	),
)
*/
# Attributes is priority
$array = xml_into_array::xml_to_array($xml, true, false);
/*
array(
	'data' => array(
		'id' => array(
			'value' => 1001,
		),
		'info' => array(
			'@attributes' => array(
				'att1' => 'one',
				'att2' => 'two',
			),
		),
		'extra' => array(
			0 => array(
				'value' => 1111,
			),
			1 => array(
				'value' => 2222,
			),
			2 => array(
				'value' => 3333,
			),
		),
	),
)
*/
# Attributes will be omitted
$array = xml_into_array::xml_to_array($xml, false, true);
/*
array(
	'data' => array(
		'id' => 1001,
		'info' => null,
		'extra' => array(
			0 => 1111,
			1 => 2222,
			2 => 3333,
		),
	),
)
*/
# Attributes will be omitted, tags will be fulfill role of attributes
$array = xml_into_array::xml_to_array($xml, false, false);
/*
array(
	'data' => array(
		'id' => array(
			'value' => 1001,
		),
		'info' => null,
		'extra' => array(
			0 => array(
				'value' => 1111,
			),
			1 => array(
				'value' => 2222,
			),
			2 => array(
				'value' => 3333,
			),
		),
	),
)
*/