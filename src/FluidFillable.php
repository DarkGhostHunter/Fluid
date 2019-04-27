<?php

namespace DarkGhostHunter\Fluid;

use DarkGhostHunter\Fluid\Exceptions\InvalidAttributeException;

class FluidFillable extends Fluid
{
    /**
     * Attributes that can be filled
     *
     * @var array
     */
    protected $fillable;

    /**
     * Set the fillable attributes
     *
     * @param array $attributes
     * @throws InvalidAttributeException
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            if (! in_array($key, $this->fillable, true)) {
                throw new InvalidAttributeException($key, $this);
            }
        }

        $this->setAttributes($attributes);
    }

    /**
     * Sets a fillable attribute
     *
     * @param string $key
     * @param $value
     * @return void
     * @throws InvalidAttributeException
     */
    public function setAttribute(string $key, $value)
    {
        if (! in_array($key, $this->fillable, true)) {
            throw new InvalidAttributeException($key, $this);
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Get the fillable attributes
     *
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * Set the fillable attributes
     *
     * @param array $fillable
     */
    public function setFillable(array $fillable)
    {
        $this->fillable = $fillable;
    }


}