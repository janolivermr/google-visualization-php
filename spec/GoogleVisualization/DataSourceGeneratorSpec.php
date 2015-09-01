<?php

namespace spec\GoogleVisualization;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataSourceGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('GoogleVisualization\DataSourceGenerator');
    }

    function it_returns_a_DataSource_object()
    {
        $this->generate([],[])->shouldReturnAnInstanceOf('GoogleVisualization\DataSource');
    }

    function it_can_return_a_JSON_string()
    {
        $this->generateJson([],[])->shouldMatch('/\{.*\}/i');
        // This function just uses the Notation class, see more tests in that spec.
    }

    function it_requires_two_array_arguments()
    {
        $this->shouldThrow('\InvalidArgumentException')->duringGenerate('','');
    }

    function it_can_create_a_column(){
        $result = $this->createColumn(['type'=>'string','id'=>'name']);
        $result->type->shouldBeLike('string');
        $result->id->shouldBeLike('name');
    }

    function it_returns_valid_column_information_for_one_column()
    {
        $inputData = json_decode('[{"name":"Michael"},{"name":"John"}]');
        $outputData = $this->generate($inputData,["name"=>"string"]);
        $outputData->cols->shouldHaveCount(1);
        $outputData->cols[0]->type->shouldBeLike('string');
        $outputData->cols[0]->id->shouldBeLike('name');
    }

    function it_returns_valid_column_information_for_multiple_columns()
    {
        $inputData = json_decode('[{"gender":"male", "age":27},{"gender":"female", "age":24}]');
        $outputData = $this->generate($inputData,["gender"=>"string", "age"=>"number"]);
        $outputData->cols->shouldHaveCount(2);
        $outputData->cols[0]->type->shouldBeLike('string');
        $outputData->cols[0]->id->shouldBeLike('gender');
        $outputData->cols[1]->type->shouldBeLike('number');
        $outputData->cols[1]->id->shouldBeLike('age');
    }

    function it_creates_valid_cells_from_a_single_object()
    {
        $inputData = json_decode('[{"gender":"male", "age":27},{"gender":"female", "age":24}]');
        $outputData = $this->createCells($inputData[0],["gender"=>"string"]);
        $outputData->shouldBeArray();
        $outputData->shouldHaveCount(1);
        $outputData[0]->v->shouldBeLike('male');
    }

    function it_creates_multiple_valid_cells_from_a_single_object()
    {
        $inputData = json_decode('[{"gender":"male", "age":27},{"gender":"female", "age":24}]');
        $outputData = $this->createCells($inputData[0],["gender"=>"string", "age"=>"number"]);
        $outputData->shouldBeArray();
        $outputData->shouldHaveCount(2);
        $outputData[0]->v->shouldBeLike('male');
        $outputData[1]->v->shouldBeLike(27);
    }

    function it_creates_a_valid_row_for_a_single_object()
    {
        $inputData = json_decode('[{"gender":"male", "age":27}]');
        $outputData = $this->generate($inputData,["gender"=>"string"]);
        $outputData->rows->shouldHaveCount(1);
        $outputData->rows[0]->c[0]->v->shouldBeLike('male');
    }

    function it_converts_strings_to_numbers_where_necessary()
    {
        $inputData = json_decode('[{"gender":"male", "age":"27"}]');
        $outputData = $this->generate($inputData,["age"=>"number"]);
        $outputData->rows->shouldHaveCount(1);
        $outputData->rows[0]->c[0]->v->shouldBeInteger();
    }

    function it_converts_dates_to_datetime_objects_where_necessary()
    {
        date_default_timezone_set('UTC');
        $inputData = json_decode('[{"date":"2015-10-21 07:28:30"}]');
        $outputData = $this->generate($inputData,["date"=>"datetime"]);
        $outputData->rows->shouldHaveCount(1);
        $outputData->rows[0]->c[0]->v->shouldReturnAnInstanceOf('\DateTime');
        $outputData->rows[0]->c[0]->v->format('yoda')->shouldBeLike('15201521am');
    }
}
