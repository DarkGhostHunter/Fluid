<?php

namespace Tests;

use DarkGhostHunter\Fluid\Exceptions\InvalidAttributeException;
use DarkGhostHunter\Fluid\FluidFillable;
use PHPUnit\Framework\TestCase;

class FluidFillableTest extends TestCase
{

    public function testConstruct()
    {
        $fluid = new class extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };

        $fluid->foo = 'bar';
        $fluid->baz = 'qux';

        $this->assertEquals('bar', $fluid->foo);
        $this->assertEquals('qux', $fluid->baz);
    }

    public function testExceptionOnConstructWithUnfillable()
    {
        $this->expectException(InvalidAttributeException::class);

        $fluid = new class(['alpha' => 'bravo']) extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };
    }

    public function testFill()
    {
        $fluid = new class extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };

        $fluid->fill(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertEquals('bar', $fluid->foo);
        $this->assertEquals('qux', $fluid->baz);
    }

    public function testExceptionOnSetAttributesNotFillable()
    {
        $this->expectException(InvalidAttributeException::class);

        $fluid = new class extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };

        $fluid->fill(['foo' => 'bar', 'baz' => 'qux', 'alpha' => 'bravo']);
    }

    public function testSetAttribute()
    {
        $fluid = new class extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };

        $fluid->setAttribute('foo', 'bar');
        $fluid->setAttribute('baz', 'qux');

        $this->assertEquals('bar', $fluid->foo);
        $this->assertEquals('qux', $fluid->baz);
    }

    public function testExceptionOnSetAttributeNotFillable()
    {
        $this->expectException(InvalidAttributeException::class);

        $fluid = new class extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };

        $fluid->setAttribute('alpha', 'bravo');
    }

    public function testGetAndSetFillable()
    {
        $fluid = new class extends FluidFillable {
            protected $fillable = ['foo', 'baz'];
        };

        $this->assertEquals(['foo', 'baz'], $fluid->getFillable());

        $fluid->setFillable(['bar', 'qux']);

        $this->assertEquals(['bar', 'qux'], $fluid->getFillable());
    }
}
