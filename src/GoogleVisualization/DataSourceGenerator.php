<?php

namespace GoogleVisualization;

class DataSourceGenerator
{

    public static function generate($objects, $columns)
    {
        if(!is_array($objects))
            throw new \InvalidArgumentException('Expected objects array for first parameter, got '.gettype($objects));
        if(!is_array($columns))
            throw new \InvalidArgumentException('Expected columns array for second parameter, got '.gettype($columns));

        $ds = new DataSource();
        // Create columns
        foreach ($columns as $id => $type) {
            $ds->cols[] = static::createColumn(['type'=>$type, 'id'=>$id]);
        }
        // Create rows
        foreach ($objects as $object) {
            $row = new \stdClass();
            $row->c = static::createCells($object, $columns);
            $ds->rows[] = $row;
        }

        return $ds;
    }

    public function generateJson($objects, $columns)
    {
        return Notation::encode(static::generate($objects, $columns));
    }

    public static function createColumn($columnParameters)
    {
        if(!is_array($columnParameters))
            throw new \InvalidArgumentException('Expected array for first parameter, got '.gettype($columnParameters));
        $column = new \stdClass();
        foreach ($columnParameters as $key => $value) {
            $column->{$key} = $value;
        }
        return $column;
    }

    public static function createCells($object, $columns)
    {
        $cells = [];
        // Iterate over columns array for correct order
        // Use key to extract value from object and create new cell
        $objectArr = (array)$object;
        foreach ($columns as $key => $type) {
            $cell = new \stdClass();
            switch($type){
                case 'number':
                    if(is_numeric($objectArr[$key]) || is_null($objectArr[$key]))
                    {
                        $cell->v = $objectArr[$key] + 0;
                    }
                    else
                    {
                        throw new \InvalidArgumentException("A field that was supposed to be interpreted as a number is not numeric");
                    }
                break;
                default:
                    $cell->v = $objectArr[$key];
                    break;
            }
            $cells[] = $cell;
        }

        return $cells;
    }
}
