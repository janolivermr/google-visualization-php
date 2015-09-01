<?php

namespace GoogleVisualization;

class Notation
{

    public static function encode($content)
    {
        switch (gettype($content)) {
            case 'string':
                $result = json_encode($content);
                break;
            case 'integer':
            case 'double':
                $result = (string)$content;
                break;
            case 'boolean':
                $result = $content ? 'true' : 'false';
                break;
            case 'array':
                if (range(0, count($content) - 1) === array_keys($content)) {
                    $elements = [];
                    foreach ($content as $value) {
                        $elements[] = static::encode($value);
                    }
                    $result = '[' . implode(', ', $elements) . ']';
                    unset($elements);
                } else {
                    $result = static::encode((object)$content);
                }
                break;
            case 'object':
                if (is_a($content, '\DateTime')) {

                    $result = 'new Date('
                        . $content->format('Y, ')
                        . ($content->format('n') - 1)
                        . $content->format(', j, G, ')
                        . intval($content->format('i')) . ', '
                        . intval($content->format('s'))
                        . ')';
                } else {
                    $elements = [];
                    foreach (get_object_vars($content) as $key => $value) {
                        $elements[] = $key . ': ' . static::encode($value);
                    }
                    $result = '{' . implode(', ', $elements) . '}';
                    unset($elements);
                }
                break;
            default:
                $result = null;
        }

        return $result;

    }
}
