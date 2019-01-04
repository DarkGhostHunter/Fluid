![
Aaron Burden - Unsplash (UL) #Kp9z6zcUfGw](https://images.unsplash.com/photo-1471879832106-c7ab9e0cee23?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/fluid/v/stable)](https://packagist.org/packages/darkghosthunter/fluid) [![License](https://poser.pugx.org/darkghosthunter/fluid/license)](https://packagist.org/packages/darkghosthunter/fluid)
![](https://img.shields.io/packagist/php-v/darkghosthunter/fluid.svg) [![Build Status](https://travis-ci.com/DarkGhostHunter/Fluid.svg?branch=master)](https://travis-ci.com/DarkGhostHunter/Fluid) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Fluid/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Fluid?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/0138e0686180120e68c5/maintainability)](https://codeclimate.com/github/DarkGhostHunter/Fluid/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/0138e0686180120e68c5/test_coverage)](https://codeclimate.com/github/DarkGhostHunter/Fluid/test_coverage)


# Fluid

A flexible class based on the famous Laravel's [Fluent](https://github.com/Illuminate/Support/blob/master/Fluent.php) and [Eloquent Model](https://github.com/laravel/framework/blob/master/src/Illuminate/Database/Eloquent/Model.php) class.

Fluid will allow you to flexible manipulate a class as a bag of properties (or array keys), and allow simple serialization.

## Installation

Fire up composer and require it into your project.

```bash
composer require darkghosthunter/fluid
```

Otherwise, you can just download this as a ZIP file and require it manually in your code:

```php
<?php

require_once 'path/to/fluid/Fluid.php';

// Optionally, these two together
require_once 'path/to/fluid/FluidFillable.php';
require_once 'path/to/fluid/Exceptions/InvalidAttributeException.php';
```

## Usage

The Fluid class is a class that can be accessed as a normal object or array. It can be serialized to an array, string or JSON.

You can intance Fluid like the normal way, or just using `make()`:

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

$emptyFluid = new Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$otherFluid = Fluid::make(['foo' => 'bar']);

$otherEmptyFluid = Fluid::make();
```

You can also use `fromJson()` if you need to make an instance from a JSON string:

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid::fromJson('"{"foo":"bar"}"');

echo $fluid->foo; // 'bar'
```
 

### Attributes

Every attribute lies inside a protected array called `$attributes`, and each of one can be set as a property or an array.

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$fluid->foo = 'notBar';

echo $fluid->foo; // 'notBar';

$fluid['baz'] = 'qux';

echo $fluid['baz']; // 'qux'
```

### Serializing

Serializing means taking the class to another representation, like an array or string.

To serialize as an array, use the `toArray()` method. Since there is no magic for `(array)$fluid`, using the latter will serialize every property, so you would want to avoid that.

```php
<?php 

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$array = $fluid->toArray();

echo $fluid['foo']; // 'bar'
```

Serializing to a string will output JSON, as with `toJson()`

```php
<?php 

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$json = (string)$fluid;
$moreJson = $fluid->toJson();

echo $json; // "{"foo":"bar"}"
echo $moreJson; // "{"foo":"bar"}"
```

### Hidding

Sometimes is handy to hide attributes from serialization, like application keys, API secrets or certificate locations.

You can turn this on using `shouldHide()` methd, and set the attributes to hide in `$hidden` when extending Fluid or using `setHidden()` afterwards.

```php
<?php 

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

$fluid->setHidden(['baz']);

$fluid->shouldHide();

echo $fluid->baz; // 'qux'

print_r($fluid->toArray()); // ['foo' => 'bar']
echo (string)$fluid; // "{"foo":"bar"}"
```

### Fillable

Sometimes you want to ensure the user doesn't fill anything more than some predetermined attributes. You can use the `FluentFillable` class to enforce this.

You can put the fillable attributes in the `$fillable` array or use `setFillable()` afterwards.

```php
<?php

use DarkGhostHunter\Fluid\FluidFillable;

$fluid = new FluidFillable(['foo' => 'bar', 'baz' => 'qux']);

$fluid->setFillable(['foo', 'baz']);

$fluid->alpha = 'bravo';

/*
 * [!] DarkGhostHunter\Fluid\Exceptions\InvalidAttributeException
 * 
 * Attribute [foo] cannot be set
 */
```

The user will get a `InvalidAttributeException` when trying to set an attribute which is not fillable.

## License

This package is licenced by the [MIT License](LICENSE).