<?php

namespace GoogleVisualization;

class DataSourceGenerator
{

    /**
     * Generates a PHP Object with the required structure
     *
     * This function takes an array of objects (datasets) that should be turned into rows.
     * The second parameter contains an associative array which takes the columns and their type.
     * This type should be of according to the DataSource allowed types.
     *
     * @param $objects
     * @param $columns
     *
     * @return DataSource
     */
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

    /**
     * Creates a Json like string to be passed to Google Visualization
     *
     * This function takes runs the generate function and then parses the result in the Json like format required
     * by Google Visualization.
     *
     * @param $objects
     * @param $columns
     *
     * @return string
     */
    public static function generateJson($objects, $columns)
    {
        return Notation::encode(static::generate($objects, $columns));
    }

    /**
     * Creates a column definition object
     *
     * @param $columnParameters
     *
     * @return \stdClass
     */
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

    /**
     * Creates the data cells for a row from an object
     *
     * @param $object
     * @param $columns
     *
     * @return array
     */
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
