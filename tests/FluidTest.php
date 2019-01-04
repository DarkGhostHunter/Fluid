<?php

namespace Tests;

use BadMethodCallException;
use DarkGhostHunter\Fluid\Fluid;
use PHPUnit\Framework\TestCase;

class FluidTest extends TestCase
{

    public function test__construct()
    {
        $fluid = new Fluid();
        $this->assertInstanceOf(Fluid::class, $fluid);

        $fluid = new Fluid(['foo' => 'bar']);
        $this->assertEquals('bar', $fluid->getAttribute('foo'));
    }

    public function testSetAndGetAttributes()
    {
        $fluid = new class extends Fluid {
            public function setFooAttribute($value)
            { $this->attributes['foo'] = 'notBar'; }
            public function getFooAttribute()
            { return 'alsoNotBar'; }
        };

        $fluid->setAttributes([
            'foo' => 'bar'
        ]);

        $this->assertEquals('alsoNotBar', $fluid->getAttribute('foo'));

        $this->assertEquals(['foo' => 'bar'], $fluid->getAttributes());
    }

    public function testSetAndGetAttribute()
    {
        $fluid = new class(['baz' => 'qux']) extends Fluid {
            public function setFooAttribute($value)
            { $this->attributes['foo'] = 'notBar'; }
            public function getFooAttribute()
            { return 'alsoNotBar'; }
        };

        $this->assertEquals('qux', $fluid->getAttribute('baz'));

        $fluid->setAttribute('foo', 'anything');

        $this->assertEquals('alsoNotBar', $fluid->getAttribute('foo'));

        $this->assertNull($fluid->getAttribute('undefinedKey'));
        $this->assertEquals('default', $fluid->getAttribute('undefinedKey', 'default'));

        $this->assertEquals('callable', $fluid->getAttribute('undefinedKey', function () {
            return 'callable';
        }));
    }

    public function testSetAndGetHidden()
    {
        $fluid = new Fluid();

        $fluid->setHidden(['foo']);
        $this->assertEquals(['foo'], $fluid->getHidden());
    }

    public function testIsHiding()
    {
        $fluid = new Fluid();

        $fluid->shouldHide(true);
        $this->assertTrue($fluid->isHiding());

        $fluid->shouldHide(false);
        $this->assertFalse($fluid->isHiding());
    }

    public function testHidesAttributes()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        $fluid->setHidden(['foo']);

        $this->assertFalse($fluid->isHiding());

        $this->assertArrayHasKey('foo', $fluid->toArray());
        $this->assertArrayHasKey('baz', $fluid->toArray());

        $fluid->shouldHide();

        $this->assertArrayNotHasKey('foo', $fluid->toArray());
        $this->assertArrayHasKey('baz', $fluid->toArray());

        $fluid->shouldHide(false);

        $this->assertArrayHasKey('foo', $fluid->toArray());
        $this->assertArrayHasKey('baz', $fluid->toArray());
    }

    public function testOnly()
    {
        $fluid = new class(['foo' => 'bar', 'baz' => 'qux']) extends Fluid {
            public function getFooAttribute()
            {
                return 'quuz';
            }
        };

        $array = $fluid->only(['foo']);

        $this->assertArrayNotHasKey('baz', $array);
        $this->assertEquals('bar', $array['foo']);
    }


    public function testExcept()
    {
        $fluid = new class(['foo' => 'bar', 'baz' => 'qux']) extends Fluid {
            public function getFooAttribute()
            {
                return 'quuz';
            }
        };

        $this->assertEquals(['foo' => 'bar'], $fluid->except(['baz']));
    }


    public function testToArray()
    {
        $fluid = new Fluid($array = ['foo' => 'bar', 'baz' => 'qux']);

        $this->assertEquals($array, $fluid->toArray());
    }

    public function testToArrayHidesAttributes()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        $fluid->setHidden(['foo']);
        $fluid->shouldHide();

        $this->assertEquals(['baz' => 'qux'], $fluid->toArray());
    }

    public function testToDoesntHidesAttributes()
    {
        $fluid = new Fluid($array = ['foo' => 'bar', 'baz' => 'qux']);

        $fluid->setHidden(['foo']);
        $fluid->shouldNotHide();

        $this->assertEquals($array, $fluid->toArray());
    }

    public function testToJson()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        $fluid->setHidden(['foo']);
        $fluid->shouldHide();

        $this->assertJson($fluid->toJson());
        $this->assertStringNotContainsString('foo', $fluid->toJson());
    }

    public function testDynamicGetAndSet()
    {
        $fluid = new Fluid();

        $fluid->foo = 'bar';

        $this->assertEquals('bar', $fluid->foo);
    }

    public function testToString()
    {
        $fluid = new class(['foo' => 'bar', 'baz' => 'qux']) extends Fluid {
            public function getFooAttribute()
            {
                return 'quuz';
            }
        };

        $fluid->setHidden(['baz']);
        $fluid->shouldHide();

        $this->assertJson((string)$fluid);
        $this->assertStringNotContainsString('bar', (string)$fluid);
        $this->assertStringNotContainsString('baz', (string)$fluid);
    }

    public function testIsSet()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertTrue(isset($fluid->foo));
        $this->assertFalse(isset($fluid->undefinedKey));
    }

    public function testUnset()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        unset($fluid->foo);

        $this->assertNull($fluid->foo);
    }

    public function testJsonSerialize()
    {
        $fluid = new class(['foo' => 'bar', 'baz' => 'qux', 'quuz' => 'quux']) extends Fluid {
            public function getFooAttribute()
            {
                return 'notFoo';
            }
        };

        $fluid->setHidden(['quuz']);
        $fluid->shouldHide();

        $this->assertJson(json_encode($fluid));
        $this->assertStringContainsString('notFoo', json_encode($fluid));
        $this->assertStringNotContainsString('quuz', json_encode($fluid));
    }

    public function testOffsetExists()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertTrue(isset($fluid['foo']));
        $this->assertFalse(isset($fluid['invalidKey']));
    }

    public function testOffsetGetAndSet()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertEquals('bar', $fluid['foo']);

        $fluid['baz'] = 'notQux';

        $this->assertEquals('notQux', $fluid['baz']);
    }

    public function testOffsetUnset()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux']);

        unset($fluid['foo']);

        $this->assertNull($fluid['foo']);
        $this->assertEquals('qux', $fluid['baz']);
    }

    public function testDynamicFluentMethod()
    {
        $fluid = new Fluid();

        $fluid->foo('bar')->baz('qux');

        $this->assertEquals('bar', $fluid->foo);
        $this->assertEquals('qux', $fluid->baz);
    }

    public function testDynamicFluentMethodInvalidOnMoreParameters()
    {
        $this->expectException(BadMethodCallException::class);

        $fluid = new Fluid();

        $fluid->foo('bar', 'shouldNotHaveSecondParameter');
    }

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
        $fluid = Fluid::fromJson(json_encode(['foo' => 'bar', 'baz' => 'qux']));

        $this->assertInstanceOf(Fluid::class, $fluid);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $fluid->toArray());
    }

    public function testCount()
    {
        $fluid = new Fluid(['foo' => 'bar', 'baz' => 'qux', 'quuz' => 'quux']);

        $this->assertCount(3, $fluid);
    }

}
