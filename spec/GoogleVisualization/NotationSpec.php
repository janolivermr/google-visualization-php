<?php

namespace spec\GoogleVisualization;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('GoogleVisualization\Notation');
    }

    function it_encodes_a_string_like_json()
    {
        $this->encode('foo')->shouldBeString();
        $this->encode('foo')->shouldBeLike('"foo"');
    }

    function it_encodes_a_number_like_json()
    {
        $this->encode(5)->shouldBeString();
        $this->encode(5)->shouldBeLike('5');
    }

    function it_encodes_a_float_like_json()
    {
        $this->encode(5.1)->shouldBeString();
        $this->encode(5.1)->shouldBeLike('5.1');
    }

    function it_encodes_a_boolean_like_json()
    {
        $this->encode(true)->shouldBeString();
        $this->encode(true)->shouldBeLike('true');
    }

    function it_encodes_kv_keys_without_quotes()
    {
        $this->encode(json_decode('{"foo":"bar"}'))->shouldBeString();
        $this->encode(json_decode('{"foo":"bar"}'))->shouldBeLike('{foo: "bar"}');
    }

    function it_encodes_arrays_like_json()
    {
        $this->encode(['one', 2, true])->shouldBeString();
        $this->encode(['one', 2, true])->shouldBeLike('["one", 2, true]');
    }

    function it_encodes_associative_arrays_as_objects()
    {
        $this->encode(['foo'=>'bar','bar'=>'baz'])->shouldBeString();
        $this->encode(['foo'=>'bar','bar'=>'baz'])->shouldBeLike('{foo: "bar", bar: "baz"}');
    }

    function it_encodes_datetime_as_new_js_date_object()
    {
        $this->encode(new \DateTime("2015-10-21 07:28:30", new \DateTimeZone('UTC')))->shouldBeString();
        $this->encode(new \DateTime("2015-10-21 07:28:05", new \DateTimeZone('UTC')))->shouldBeLike('new Date(2015, 9, 21, 7, 28, 5)');
    }
}
