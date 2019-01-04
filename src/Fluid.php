<?php

namespace DarkGhostHunter\Fluid;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use JsonSerializable;

class Fluid implements ArrayAccess, JsonSerializable, Countable
{
    use Concerns\HasArrayAccess;

    /**
     * Attributes this class holds
     *
     * @var array
     */
    protected $attributes;

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
    protected $shouldHide = false;

    /**
     * Fluid constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Get all the raw attributes
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set a Raw Attribute
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns an attribute
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        if (method_exists($this, $method = 'get' . ucfirst($key) . 'Attribute')) {
            return $this->{$method}();
        }

        if ($value = $this->attributes[$key] ?? null) return $value;

        return is_callable($default) ? $default() : $default;
    }

    /**
     * Sets an attribute
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function setAttribute(string $key, $value)
    {
        if (method_exists($this, $method = 'set' . ucfirst($key) . 'Attribute')) {
            $this->{$method}($value);
            return;
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Get the attributes to hide
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the attributes to hide
     *
     * @param array $hidden
     */
    public function setHidden(array $hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * If this in instance should hide attributes on serialization
     *
     * @return bool
     */
    public function isHiding()
    {
        return $this->shouldHide;
    }

    /**
     * Should hide the attributes on serialization
     *
     * @param bool $shouldHide
     */
    public function shouldHide(bool $shouldHide = true)
    {
        $this->shouldHide = $shouldHide;
    }

    /**
     * Should not hide the attributes on serialization
     *
     * @return void
     */
    public function shouldNotHide()
    {
        $this->shouldHide = false;
    }

    /**
     * Return only selected attributes
     *
     * @param array $only
     * @return array
     */
    public function only(array $only)
    {
        return array_intersect_key($this->attributes, array_flip($only));
    }

    /**
     * Return all the attributes except those indicated
     *
     * @param array $except
     * @return array
     */
    public function except(array $except)
    {
        return array_diff_key($this->attributes, array_flip($except));
    }

    /**
     * Returns an array representation of this instance.
     *
     * @return array
     */
    public function toArray()
    {
        // If we're hiding attributes, then return those not hidden
        $array = $this->shouldHide && is_array($this->hidden)
            ? $this->except($this->hidden)
            : $this->attributes;

        // Use the getter, if its set, for each attribute
        foreach ($array as $key => $value) {
            $array[$key] = method_exists($this, $method = 'get' . ucfirst($key) . 'Attribute')
                ? $this->{$method}()
                : $value;
        }

        return $array;
    }

    /**
     * Returns a JSON representation of the instance
     *
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Dynamically get an attribute
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * Dynamically set an attribute
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Returns a string representation of this instance
     *
     * @return false|string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Check if the attribute is set
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Unset an attribute
     *
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }


    /**
     * Dynamically set an attribute as a fluent method
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (count($arguments) === 1) {
            $this->setAttribute($name, $arguments[0]);
            return $this;
        }

        throw new BadMethodCallException(
            "Method [$name] does not exist in " . get_class($this)
        );
    }

    /**
     * Create a new Fluid instance
     *
     * @param array $attributes
     * @return Fluid
     */
    public static function make(array $attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Create a new Fluid instance with raw attributes
     *
     * @param array $attributes
     * @return Fluid
     */
    public static function makeRaw(array $attributes = [])
    {
        $fluent = new static;

        $fluent->setAttributes($attributes);

        return $fluent;
    }

    /**
     * Create a new Fluid instance from a JSON string
     *
     * @param string $json
     * @return Fluid
     */
    public static function fromJson(string $json)
    {
        return static::make(json_decode($json, true));
    }
}