<?php

namespace Yarak\DB;

use Faker\Generator;
use Phalcon\Mvc\Model;

class ModelFactoryBuilder
{
    /**
     * Name of class.
     *
     * @var string
     */
    protected $class;

    /**
     * Definition name.
     *
     * @var string
     */
    protected $name;

    /**
     * Array of all definitions.
     *
     * @var array
     */
    protected $definitions;

    /**
     * Faker instance.
     * 
     * @var Generator
     */
    protected $faker;

    /**
     * Number of times to run factory.
     *
     * @var int|null
     */
    protected $times;

    /**
     * Construct.
     *
     * @param string    $class
     * @param string    $name
     * @param array     $definitions
     * @param Generator $faker
     */
    public function __construct($class, $name, array $definitions, Generator $faker)
    {
        $this->class = $class;
        $this->name = $name;
        $this->definitions = $definitions;
        $this->faker = $faker;
    }

    /**
     * Make an instance of class.
     *
     * @param array $attributes
     *
     * @return Phalcon\Mvc\Model|array
     */
    public function make(array $attributes = [])
    {
        if ($this->times === null || $this->times < 1) {
            return $this->makeInstance($attributes);
        }

        $instances = [];

        foreach (range(1, $this->times) as $time) {
            $instances[] = $this->makeInstance($attributes);
        }

        return $instances;
    }

    /**
     * Create an instance of class and persist in database.
     *
     * @param array $attributes
     *
     * @return Phalcon\Mvc\Model|array
     */
    public function create(array $attributes = [])
    {
        $made = $this->make($attributes);

        if ($made instanceof Model) {
            $made->save();
        }

        if (is_array($made)) {
            return array_map(function (Model $model) {
                $model->save();

                return $model;
            }, $made);
        }

        return $made;
    }

    /**
     * Set the number of times to run the make/create the class.
     *
     * @param int|null $times
     *
     * @return this
     */
    public function times($times = null)
    {
        $this->times = $times;

        return $this;
    }

    /**
     * Make an instance of the class with the given attributes.
     *
     * @param array $attributes
     *
     * @return Phalcon\Mvc\Model
     */
    protected function makeInstance(array $attributes = [])
    {
        if (!isset($this->definitions[$this->class][$this->name])) {
            throw new \Exception(
                "Definition with name {$this->name} does not exist."
            );
        }

        $fakerAttributes = call_user_func(
            $this->definitions[$this->class][$this->name],
            $this->faker
        );

        $finalAttributes = array_merge($fakerAttributes, $attributes);

        return new $this->class($finalAttributes);
    }
}
