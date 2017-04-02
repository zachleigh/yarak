<?php

namespace Yarak\Migrations\FileDate;

use Yarak\Helpers\Str;
use Yarak\Helpers\Creator;
use Yarak\Exceptions\WriteError;
use Yarak\Migrations\MigrationCreator;

class FileDateMigrationCreator extends Creator implements MigrationCreator
{
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

        if (!class_exists($className)) {
            $this->createDirectories();

            $this->writeFile(
                $path = $this->getSavePath($name),
                $this->getStub($className, $create)
            );

            $this->output->writeInfo("Created migration {$name}.");

            return $path;
        }

        throw WriteError::classExists($className);
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
     * Create necessary directories for migrations.
     */
    protected function createDirectories()
    {
        $created = $this->makeDirectoryStructure([
            'database'   => $this->config->getDatabaseDirectory(),
            'migrations' => $this->config->getMigrationDirectory(),
        ]);

        foreach ($created as $key => $value) {
            $this->output->writeInfo("Created {$key} directory.");
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

        $stub = file_get_contents(__DIR__."/../Stubs/{$stubFile}");

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

        return $this->config->getMigrationDirectory($fileName);
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
