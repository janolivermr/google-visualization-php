<?php

namespace spec\GoogleVisualization;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataSourceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('GoogleVisualization\DataSource');
    }

    function it_has_a_columns_property()
    {
        $this->cols->shouldBeArray();
    }

    function it_has_a_rows_property()
    {
        $this->rows->shouldBeArray();
    }
}
