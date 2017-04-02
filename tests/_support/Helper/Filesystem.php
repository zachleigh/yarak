<?php

namespace Helper;

use Yarak\Config\Config;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends \Codeception\Module
{
    /**
     * Symfony filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Setup filesystem on object.
     */
    public function setFilesystem()
    {
        $this->filesystem = new SymfonyFilesystem();
    }

    /**
     * Remove test app database directory.
     */
    public function removeDatabaseDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getDatabaseDirectory());
    }

    /**
     * Remove test app migration directory.
     */
    public function removeMigrationDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getMigrationDirectory());
    }

    /**
     * Remove test app seed directory.
     */
    public function removeSeedDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getSeedDirectory());
    }

    /**
     * Remove test app factory directory.
     */
    public function removeFactoryDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getFactoryDirectory());
    }

    /**
     * Remove test app console directory.
     */
    public function removeConsoleDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getConsoleDirectory());
    }

    /**
     * Return symfony filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Get a file name from a path.
     *
     * @param string $path
     * @param string $extension
     *
     * @return string
     */
    public function getFileNameFromPath($path, $extension = '.php')
    {
        $pathArray = explode('/', $path);

        return str_replace($extension, '', array_pop($pathArray));
    }

    /**
     * Create all paths necessary for seeding.
     *
     * @param Config $config
     */
    public function createAllPaths(Config $config)
    {
        $directories = $config->getAllDatabaseDirectories();

        $directories[] = __DIR__.'/../../../app/models';

        $this->makeDirectoryStructure($directories);
    }

    /**
     * Create all directories listed in directories array.
     *
     * @param array $directories
     */
    public function makeDirectoryStructure(array $directories)
    {
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory);
            }
        }
    }

    /**
     * Write contents to path.
     *
     * @param string $path
     * @param string $contents
     *
     * @throws WriteError
     */
    public function writeFile($path, $contents)
    {
        try {
            file_put_contents($path, $contents);
        } catch (\Exception $e) {
            throw WriteError::fileWriteFailed($e, $path);
        }
    }

    /**
     * Copy all seeder file stubs to test app.
     */
    public function copySeeders()
    {
        $this->copySeederStub('usersTableSeeder');

        $this->copySeederStub('postsTableSeeder');

        $this->copySeederStub('databaseSeeder');
    }

    /**
     * Copy stubs to test app.
     *
     * @param Config $config
     */
    public function copyStubs(Config $config)
    {
        $this->copyModelStub('usersModel', 'Users');

        $this->copyModelStub('postsModel', 'Posts');

        $this->getModule('\Helper\Filesystem')->writeFile(
            $config->getFactoryDirectory('ModelFactory.php'),
            file_get_contents(__DIR__.'/../../_data/Stubs/factory.stub')
        );
    }

    /**
     * Copy a model stub to the test app directory.
     *
     * @param string $stubName
     * @param string $fileName
     */
    public function copyModelStub($stubName, $fileName)
    {
        $this->getModule('\Helper\Filesystem')->writeFile(
            __DIR__."/../../../app/models/{$fileName}.php",
            file_get_contents(__DIR__."/../../_data/Stubs/{$stubName}.stub")
        );
    }

    /**
     * Copy a seed file to the database/seed directory.
     *
     * @param string $stubName
     */
    public function copySeederStub($stubName)
    {
        $fileName = ucfirst($stubName);

        $this->getModule('\Helper\Filesystem')->writeFile(
            __DIR__."/../../../app/database/seeds/{$fileName}.php",
            file_get_contents(__DIR__."/../../_data/Stubs/{$stubName}.stub")
        );
    }
}
