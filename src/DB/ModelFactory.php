<?php

namespace Yarak\DB;

use Faker\Generator;
use Yarak\Config\Config;
use Yarak\Helpers\Filesystem;
use Phalcon\Mvc\User\Component;

class ModelFactory extends Component
{
    use Filesystem;

    /**
     * Faker instance.
     *
     * @var Generator
     */
    protected $faker;

    /**
     * Array of user defined factory definitions.
     *
     * @var array
     */
    protected $definitions;

    /**
     * Construct.
     *
     * @param Generator $faker
     */
    public function __construct(Generator $faker)
    {
        $this->faker = $faker;

        $this->load();
    }

    /**
     * Make instances of class, overriding default values with attributes.
     *
     * @param string $class
     * @param array  $attributes
     * @param int    $times
     *
     * @return Phalcon\Mvc\Model|array
     */
    public function make($class, array $attributes = [], $times = null)
    {
        return $this->forClass($class)->times($times)->make($attributes);
    }

    /**
     * Make instances of class using name.
     *
     * @param string $class
     * @param string $name
     * @param array  $attributes
     * @param int    $times
     *
     * @return Phalcon\Mvc\Model|array
     */
    public function makeAs($class, $name, array $attributes = [], $times = null)
    {
        return $this->forClass($class, $name)->times($times)->make($attributes);
    }

    /**
     * Create instances of class, overriding default values with attributes.
     *
     * @param string $class
     * @param array  $attributes
     * @param int    $times
     *
     * @return Phalcon\Mvc\Model|array
     */
    public function create($class, array $attributes = [], $times = null)
    {
        return $this->forClass($class)->times($times)->create($attributes);
    }
    /**
     * Create instances of class using name.
     *
     * @param string $class
     * @param string $name
     * @param array  $attributes
     * @param int    $times
     *
     * @return Phalcon\Mvc\Model|array
     */
    public function createAs($class, $name, array $attributes = [], $times = null)
    {
        return $this->forClass($class, $name)->times($times)->create($attributes);
    }

    /**
     * Get instance of factory builder with set attributes.
     *
     * @param string $class
     * @param string $name
     *
     * @return ModelFactoryBuilder
     */
    public function forClass($class, $name = 'default')
    {
        return new ModelFactoryBuilder(
            $class,
            $name,
            $this->definitions,
            $this->faker
        );
    }

    /**
     * Load factory definitions onto object.
     *
     * @return this
     */
    protected function load()
    {
        $factory = $this;

        $config = Config::getInstance();

        $path = $config->getFactoryDirectory();

        $this->makeDirectoryStructure([
            $config->getDatabaseDirectory(),
            $path
        ]);

        $dir = new \DirectoryIterator($path);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                require $fileinfo->getRealPath();
            }
        }

        if (empty($this->definitions)) {
            throw new \Exception('No factory definitions found.');
        }

        return $factory;
    }

    /**
     * Define a class with a given set of attributes.
     *
     * @param string   $class
     * @param callable $attributes
     * @param string   $name
     *
     * @return this
     */
    public function define($class, callable $attributes, $name = 'default')
    {
        $this->definitions[$class][$name] = $attributes;

        return $this;
    }
}
