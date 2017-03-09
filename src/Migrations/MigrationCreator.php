<?php

namespace Yarak\Migrations;

use Phalcon\Exception;
use Yarak\Helpers\Str;
use Yarak\Config\Config;
use Yarak\Helpers\Paths;

class MigrationCreator
{
    use Paths;

    /**
     * Yarak config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Construct.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Create a migration file.
     *
     * @param string $name
     * @param string $create
     *
     * @return string
     */
    public function create($name, $create = false)
    {
        $className = $this->getClassName($name);

        $this->failIfClassExists($className);

        $this->makeDirectoryStructure([
            $this->config->getDatabaseDirectory(),
            $this->config->getMigrationDirectory()
        ]);

        try {
            file_put_contents(
                $path = $this->getSavePath($name),
                $this->getStub($className, $create)
            );
        } catch (\Exception $e) {
            throw new Exception($e);
        }

        return $path;
    }

    /**
     * Return StudlyCase class name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * If class name already exists, throw exception. Prone to failure due to
     * autoloading strategy.
     *
     * @param string $className
     *
     * @throws Phalcon\Exception
     */
    protected function failIfClassExists($className)
    {
        if (class_exists($className)) {
            throw new Exception(
                sprintf('Class "%s" already exists.', $className)
            );
        }
    }

    /**
     * Get stub with appropriate class name/table name.
     *
     * @param string $className
     *
     * @return string
     */
    protected function getStub($className, $create)
    {
        $stubFile = $create ? 'create.stub' : 'empty.stub';

        $stub = file_get_contents(__DIR__."/Stubs/{$stubFile}");

        return $this->populateStub($stub, $className, $create);
    }

    /**
     * Populate stub with class name and table name.
     *
     * @param string $stub
     * @param string $className
     * @param string $create
     *
     * @return string
     */
    protected function populateStub($stub, $className, $create)
    {
        if ($create) {
            $stub = str_replace('TABLENAME', $create, $stub);
        }

        return str_replace('CLASSNAME', $className, $stub);
    }

    /**
     * Get the full path to save file to.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getSavePath($name)
    {
        $fileName = $this->buildFileName($name);

        return $this->config->getMigrationDirectory().$fileName;
    }

    /**
     * Build file name for migration.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildFileName($name)
    {
        return $this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
}
