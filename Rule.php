<?php

namespace Curia\YiiValidation;

class Rule
{
    /**
     * Get a dimensions constraint builder instance.
     *
     * @param  array  $constraints
     * @return \Curia\YiiValidation\Rules\Dimensions
     */
    public static function dimensions(array $constraints = [])
    {
        return new Rules\Dimensions($constraints);
    }

    /**
     * Get a exists constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \Curia\YiiValidation\Rules\Exists
     */
    public static function exists($table, $column = 'NULL')
    {
        return new Rules\Exists($table, $column);
    }

    /**
     * Get an in constraint builder instance.
     *
     * @param  array|string  $values
     * @return \Curia\YiiValidation\Rules\In
     */
    public static function in($values)
    {
        return new Rules\In(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a not_in constraint builder instance.
     *
     * @param  array|string  $values
     * @return \Curia\YiiValidation\Rules\NotIn
     */
    public static function notIn($values)
    {
        return new Rules\NotIn(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a unique constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \Curia\YiiValidation\Rules\Unique
     */
    public static function unique($table, $column = 'NULL')
    {
        return new Rules\Unique($table, $column);
    }
}
