<?php

namespace Curia\Validation;

class ArrayLoader
{
    /**
     * All of the translation messages.
     *
     * @var array
     */
    protected $messages = [];

    public function __construct()
    {
        $this->messages = require('./zh.php');
    }

    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        return $this->messages;
        
        $namespace = $namespace ?: '*';

        if (isset($this->messages[$namespace][$locale][$group])) {
            return $this->messages[$namespace][$locale][$group];
        }

        return [];
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        //
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addJsonPath($path)
    {
        //
    }

    /**
     * Add messages to the loader.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  array  $messages
     * @param  string|null  $namespace
     * @return $this
     */
    public function addMessages($locale, $group, array $messages, $namespace = null)
    {
        $namespace = $namespace ?: '*';

        $this->messages[$namespace][$locale][$group] = $messages;

        return $this;
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        return [];
    }
}
