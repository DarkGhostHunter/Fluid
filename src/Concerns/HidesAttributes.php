<?php

namespace DarkGhostHunter\Fluid\Concerns;

trait HidesAttributes
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
    protected $shouldHide = false;

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
     * If this instance should hide attributes on serialization
     *
     * @return bool
     */
    public function isHiding()
    {
        return $this->shouldHide;
    }

    /**
     * If this instance should hide the attributes on serialization
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
}