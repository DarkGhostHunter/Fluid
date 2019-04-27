<?php

namespace DarkGhostHunter\Fluid;

class FluidInstanceHelper
{
    /**
     * Create a new Fluid instance
     *
     * @param string $class
     * @param array $attributes
     * @return Fluid
     */
    public static function make(string $class, array $attributes = [])
    {
        return new $class($attributes);
    }

    /**
     * Create a new Fluid instance with raw attributes
     *
     * @param string $class
     * @param array $attributes
     * @return Fluid
     */
    public static function makeRaw(string $class, array $attributes = [])
    {
        $fluent = new $class;

        $fluent->setAttributes($attributes);

        return $fluent;
    }

    /**
     * Create a new Fluid instance from a JSON string
     *
     * @param string $class
     * @param string $json
     * @return Fluid
     */
    public static function fromJson(string $class, string $json)
    {
        return static::make($class, json_decode($json, true));
    }
}