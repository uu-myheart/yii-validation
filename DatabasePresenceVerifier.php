<?php

namespace Curia\YiiValidation;

use Closure;
use yii\db\Connection;
use yii\db\Query;
use Curia\Collect\Str;

class DatabasePresenceVerifier
{
    /**
     * The database connection instance.
     *
     * @var \yii\db\Connection
     */
    protected $db;

    public $defaultDb;

    /**
     * Create a new database presence verifier.
     *
     * @param  \yii\db\Connection  $db
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  string  $value
     * @param  int|null  $excludeId
     * @param  string|null  $idColumn
     * @param  array  $extra
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $query = $this->table($collection)->where([$column => $value]);

        if (! is_null($excludeId) && $excludeId !== 'NULL') {
            $query->andWhere(['<>', $idColumn ?: 'id', $excludeId]);
        }

        return $this->addConditions($query, $extra)->count('*', $this->getDb());
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  array   $values
     * @param  array   $extra
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $query = $this->table($collection)->andWhere(['in', $column, $values]);

        return $this->addConditions($query, $extra)->count('*', $this->getDb());
    }

    /**
     * Add the given conditions to the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $conditions
     * @return \Illuminate\Database\Query\Builder
     */
    protected function addConditions($query, $conditions)
    {
        foreach ($conditions as $key => $value) {
            if ($value instanceof Closure) {
                $query->where(function ($query) use ($value) {
                    $value($query);
                });
            } else {
                $this->addWhere($query, $key, $value);
            }
        }

        return $query;
    }

    /**
     * Add a "where" clause to the given query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $key
     * @param  string  $extraValue
     * @return void
     */
    protected function addWhere($query, $key, $extraValue)
    {
        if ($extraValue === 'NULL') {
            $query->whereNull($key);
        } elseif ($extraValue === 'NOT_NULL') {
            $query->whereNotNull($key);
        } elseif (Str::startsWith($extraValue, '!')) {
            $query->where($key, '!=', mb_substr($extraValue, 1));
        } else {
            $query->where($key, $extraValue);
        }
    }

    /**
     * Get a query builder for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table($table)
    {
        // return $this->db->connection($this->connection)->table($table)->useWritePdo();
        
        return (new Query)->from($table);
    }

    /**
     * Set the connection to be used.
     *
     * @param  string  $connection
     * @return void
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function setDefaultDb($db)
    {
        $this->defaultDb = $db;
    }

    public function getDb()
    {
        if (! $this->db) {
            return \Yii::$app->{$this->defaultDb};
        }

        return \Yii::$app->{$this->db};
    }
}
