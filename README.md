![
Aaron Burden - Unsplash (UL) #Kp9z6zcUfGw](https://images.unsplash.com/photo-1471879832106-c7ab9e0cee23?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/fluid/v/stable)](https://packagist.org/packages/darkghosthunter/fluid) [![License](https://poser.pugx.org/darkghosthunter/fluid/license)](https://packagist.org/packages/darkghosthunter/fluid)
![](https://img.shields.io/packagist/php-v/darkghosthunter/fluid.svg) [![Build Status](https://travis-ci.com/DarkGhostHunter/Fluid.svg?branch=master)](https://travis-ci.com/DarkGhostHunter/Fluid) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Fluid/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Fluid?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/75d03e2ee12a047b8a02/maintainability)](https://codeclimate.com/github/DarkGhostHunter/Fluid/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/75d03e2ee12a047b8a02/test_coverage)](https://codeclimate.com/github/DarkGhostHunter/Fluid/test_coverage)


# Fluid

A flexible class based on the famous Laravel's [Fluent](https://github.com/Illuminate/Support/blob/master/Fluent.php) and [Eloquent Model](https://github.com/laravel/framework/blob/master/src/Illuminate/Database/Eloquent/Model.php) class.

Fluid will allow you to flexible manipulate a class as a bag of properties (or array keys), and allow simple serialization while hiding sensible data from your users.

## Installation

Fire up composer and require it into your project.

```bash
composer require darkghosthunter/fluid
```

Otherwise, you can just download this as a ZIP file and require it manually in your code:

```php
<?php

require_once 'path/to/fluid/Fluid.php';
require_once 'path/to/fluid/Concerns/HasArrayAccess.php';
require_once 'path/to/fluid/Concerns/HidesAttributes.php';
require_once 'path/to/fluid/Concerns/HasInstanceHelpers.php';

// Optionally, these two together too
require_once 'path/to/fluid/FluidFillable.php';
require_once 'path/to/fluid/Exceptions/InvalidAttributeException.php';
```

## Usage

The Fluid class is a class that can be accessed as a normal object or array. It can be serialized to an array, string or JSON.

You can instance Fluid like the normal way, or just using `make()`:

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

$fluid = Fluid::fromJson('"{"foo":"bar"}"');

echo $fluid->foo; // 'bar'
```

To be totally safe to use, these static helper methods will return your class that extended the `Fluid` instead of the base class. So using `Oil::make()` will return an instance of `Oil`. 

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

class Water extends Fluid
{
    // ...
}

$water = Water::make();

get_class($water); // 'Water'
```

#### Override the static helpers

Fluid uses the magic of `__callStatic` to create a new instance. In previous versions you could not override the static methods with your own logic, but now you can:

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

class Water extends Fluid
{
    /**
     * My Custom "Make" static method
     * 
     * @return \DarkGhostHunter\Fluid\Fluid|string
     */
    public static function make()
    {
        return 'My Custom Logic';
    }
}

echo Water::make(); // 'My Custom Logic'
```

### Attributes

Every attribute lies inside a protected array called `$attributes`, and each of these can be set as it was a property or an array.

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$fluid->foo = 'notBar';
$fluid['baz'] = 'qux';

echo $fluid->foo; // 'notBar';
echo $fluid['baz']; // 'qux'

echo $fluid['thisAttributeDoesNotExists']; // null
echo $fluid->thisAlsoDoesNotExists; // null
```

For convenience, if a property or array key doesn't exists it will return null.

### Serializing

Serializing means taking the class to another representation, like an array or string.

To serialize as an array, use the `toArray()` method.

```php
<?php 

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$array = $fluid->toArray();

echo $fluid['foo']; // 'bar'
```

> Since there is no magic for using `(array)$fluid`, the latter will serialize every property, so to avoid that.

Serializing to a string will output JSON, as with the `toJson()` method.

```php
<?php 

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar']);

$json = (string)$fluid;
$moreJson = $fluid->toJson();

echo $json; // "{"foo":"bar"}"
echo $moreJson; // "{"foo":"bar"}"
```

### Hiding attributes from serialization

Sometimes is handy to hide attributes from serialization, like application keys, API secrets, user credentials or certificate locations.

You can turn this on using `shouldHide()` method, or if you're extending `Fluid`, setting `$shouldHide` to false.

```php
<?php

use DarkGhostHunter\Fluid\Fluid;

class Water extends Fluid
{
    /**
     * Attributes to hide on serialization
     *
     * @var array
     */
    protected $hidden;
    
    /**
     * Should hide attributes on serialization
     *
     * @var bool
     */
    protected $shouldHide = true;
    
    // ...
}

$fluid = new Fluid;

$fluid->shouldHide();

```
 
Set the attributes to hide inside the `$hidden` property. Alternatively, you can use the  `setHidden()` method after is instanced. 

```php
<?php 

use DarkGhostHunter\Fluid\Fluid;

$fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

$fluid->setHidden(['baz']);

$fluid->shouldHide();

echo $fluid->baz; // 'qux'
echo $fluid['baz']; // 'qux'

print_r($fluid->toArray()); // Array( ['foo' => 'bar'] )
echo (string)$fluid; // "{"foo":"bar"}"
```

### Fillable

Sometimes you want to ensure the user doesn't fill anything more than some predetermined attributes. You can use the `FluidFillable` class to enforce this.

You can put the attributes allowed to be set in the `$fillable` array or use `setFillable()` afterwards.

```php
<?php

use DarkGhostHunter\Fluid\FluidFillable;

$fluid = new FluidFillable(['foo' => 'bar', 'baz' => 'qux']);

$fluid->setFillable(['foo', 'baz']);

$fluid->alpha = 'bravo';

/*
 * [!] DarkGhostHunter\Fluid\Exceptions\InvalidAttributeException
 * 
 * Attribute [foo] in not set as fillable in FluidFillable.
 */
```

The user will get a `InvalidAttributeException` when trying to set an attribute which is not fillable in the class.

You can use this to force developers to only allow certain attributes inside an instance, allowing you to displace any filtering logic once the instance is processed in your application.

## License

This package is licenced by the [MIT License](LICENSE).