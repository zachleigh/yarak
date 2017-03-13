<?php

namespace Yarak\Tests;

use Faker\Factory;
use App\Models\Users;
use Yarak\Config\Config;
use Yarak\Helpers\Filesystem;
use Yarak\DB\Factories\ModelFactory;

class FactoryTestCase extends TestCase
{
    use Filesystem;

    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $config = $this->getConfig();

        $this->createAllPaths($config);

        $this->copyStubs($config);

        $this->createMigration();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $this->getMigrator()->run();

        require_once __DIR__.'/TestHelper.php';
    }

    /**
     * Assert that given user is instance of Users and properties are set.
     *
     * @param Users $user
     */
    protected function assertUserInstanceMade(Users $user)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->assertTrue(is_string($user->username));

        $this->assertTrue(is_string($user->email));

        $this->assertTrue(is_string($user->password));
    }

    /**
     * Assert that given user is instance of user and saved in database.
     *
     * @param Users $user
     */
    protected function assertUserInstanceCreated(Users $user)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->seeInDatabase('users', [
            'username' => $user->username,
            'email'    => $user->email,
        ]);
    }

    /**
     * Assert that user object has given attributes.
     *
     * @param Users $user
     * @param array $attributes
     */
    protected function assertUserHasAttributes(Users $user, array $attributes)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals($attributes['username'], $user->username);

        $this->assertEquals($attributes['email'], $user->email);

        $this->assertTrue(is_string($user->password));
    }

    /**
     * Create all paths necessary for seeding.
     *
     * @param Config $config
     */
    protected function createAllPaths(Config $config)
    {
        $directories = $config->getAllDatabaseDirectories();

        $directories[] = __DIR__.'/app/models';

        $this->makeDirectoryStructure($directories);
    }

    /**
     * Copy stubs to test app.
     *
     * @param Config $config
     */
    protected function copyStubs(Config $config)
    {
        $this->copyModelStub('usersModel', 'Users');

        $this->copyModelStub('postsModel', 'Posts');

        $this->writeFile(
            $config->getFactoryDirectory('ModelFactory.php'),
            file_get_contents(__DIR__.'/Stubs/factory.stub')
        );
    }

    /**
     * Copy a model stub to the test app directory.
     *
     * @param string $stubName
     * @param string $fileName
     */
    protected function copyModelStub($stubName, $fileName)
    {
        $this->writeFile(
            __DIR__."/app/models/{$fileName}.php",
            file_get_contents(__DIR__."/Stubs/{$stubName}.stub")
        );
    }

    /**
     * Get an instance of ModelFactory.
     *
     * @return ModelFactory
     */
    protected function getModelFactory()
    {
        return new ModelFactory(Factory::create());
    }
}
