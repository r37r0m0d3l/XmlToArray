XML to Array
===========

## Convert XML to Array

This class can parse XML and return the document structure in an array.
It takes a string with a XML document and parses it using PHP SimpleXML or Expat extensions.
The class returns an nested array with the details of each XML document tag and data elements.

## Examples

#### Simple XML without attributes

```xml
<?xml version="1.0" encoding="utf-8"?>
<data>
	<extra1>1111</extra1>
	<extra2>2222</extra2>
	<extra3>3333</extra3>
</data>
```

```php
XmlToArray::xmlToArray($xml);

array(
	'data' => array(
		'extra1' => 1111,
		'extra2' => 2222,
		'extra3' => 3333,
	),
)
```

#### More complex XML

```xml
<?xml version="1.0" encoding="utf-8"?>
<data>
	<id>1001</id>
	<info att1="one" att2="two"></info>
	<extra>1111</extra>
	<extra>2222</extra>
	<extra>3333</extra>
</data>
```

#### More informative array

```php
XmlToArray::xmlToArray($xml);

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
```

#### Attributes is priority

```php
XmlToArray::xmlToArray($xml, true, false);

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
```

#### Attributes can be omitted

```php
XmlToArray::xmlToArray($xml, false, true);

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
```

#### Attributes will be omitted and tags will fulfill role of attributes

```php
XmlToArray::xmlToArray($xml, false, false);

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
```

