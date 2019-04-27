<?php

namespace Tests;

use BadMethodCallException;
use DarkGhostHunter\Fluid\Fluid;
use PHPUnit\Framework\TestCase;

class FluidInstanceHelpersTest extends TestCase
{
    public function testMake()
    {
        $fluid = new class() extends Fluid {
            public function setFooAttribute()
            {
                $this->attributes['foo'] = 'notFoo';
            }
        };

        $fluid = $fluid::make(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertInstanceOf(Fluid::class, $fluid);
        $this->assertEquals(['foo' => 'notFoo', 'baz' => 'qux'], $fluid->toArray());
    }

    public function testMakeRaw()
    {
        $fluid = new class() extends Fluid {
            public function setFooAttribute()
            { $this->attributes['foo'] = 'notFoo'; }
        };

        $fluid = $fluid::makeRaw($array = ['foo' => 'bar', 'baz' => 'qux']);

        $this->assertInstanceOf(Fluid::class, $fluid);
        $this->assertEquals($array, $fluid->toArray());
    }

    public function testFromJson()
    {
        $fluid = new class() extends Fluid {
        };

        $fluid = $fluid::fromJson(json_encode($array = ['foo' => 'bar', 'baz' => 'qux']));

        $this->assertInstanceOf(Fluid::class, $fluid);
        $this->assertEquals($array, $fluid->toArray());
    }

    public function testBadStaticFunctionException()
    {
        $this->expectException(BadMethodCallException::class);

        $fluid = new class() extends Fluid {
        };

        $fluid::Anything('anything');
    }

    public function testCanOverrideInstanceHelpers()
    {
        $fluid = new class() extends Fluid {
            public static function make()
            {
                return 'overriden';
            }
            public static function makeRaw()
            {
                return 'overriden';
            }
            public static function fromJson()
            {
                return 'overriden';
            }
        };

        $this->assertEquals('overriden', $fluid::make());
        $this->assertEquals('overriden', $fluid::makeRaw());
        $this->assertEquals('overriden', $fluid::fromJson());
    }

}
