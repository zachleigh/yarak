<?php

namespace Yarak\Tests\Unit;

use Faker\Factory;
use App\Models\Users;
use Yarak\Config\Config;
use Yarak\Tests\TestCase;
use Yarak\DB\ModelFactory;
use Yarak\Helpers\Filesystem;

class FactoryTest extends TestCase
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

        $this->getMigrator()->run();

        require_once __DIR__.'/../TestHelper.php';
    }

    /**
     * @test
     */
    public function it_makes_a_single_model_instance()
    {
        $factory = $this->getModelFactory();

        $user = $factory->make(Users::class);

        $this->assertUserInstanceMade($user);
    }

    /**
     * @test
     */
    public function it_doesnt_save_models_when_making_them()
    {
        $factory = $this->getModelFactory();

        $user = $factory->make(Users::class);

        $this->dontSeeInDatabase('users', [
            'id' => 1
        ]);
    }

    /**
     * @test
     */
    public function it_uses_user_defined_attributes_when_making_model()
    {
        $factory = $this->getModelFactory();

        $attributes = [
            'username' => 'bobsmith',
            'email'    => 'bobsmsith@example.com'
        ];

        $user = $factory->make(Users::class, $attributes);

        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals($attributes['username'], $user->username);

        $this->assertEquals($attributes['email'], $user->email);

        $this->assertTrue(is_string($user->password));
    }

    /**
     * @test
     */
    public function it_makes_multiple_instances_of_models()
    {
        $factory = $this->getModelFactory();

        $users = $factory->make(Users::class, [], 3);

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->assertUserInstanceMade($user);
        }
    }

    /**
     * @test
     */
    public function it_makes_classes_with_a_given_name()
    {
        $factory = $this->getModelFactory();

        $user = $factory->makeAs(Users::class, 'myUser');

        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals('myUsername', $user->username);
    }

    /**
     * @test
     */
    public function it_creates_a_single_model_instance()
    {
        $factory = $this->getModelFactory();

        $user = $factory->create(Users::class);

        $this->assertUserInstanceCreated($user);
    }

    /**
     * @test
     */
    public function it_uses_user_defined_attributes_when_creating_model()
    {
        $factory = $this->getModelFactory();

        $attributes = [
            'username' => 'bobsmith',
            'email'    => 'bobsmsith@example.com'
        ];

        $user = $factory->create(Users::class, $attributes);

        $this->assertInstanceOf(Users::class, $user);

        $this->seeInDatabase('users', $attributes);
    }

    /**
     * @test
     */
    public function it_creates_multiple_instances_of_models()
    {
        $factory = $this->getModelFactory();

        $users = $factory->create(Users::class, [], 3);

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->assertUserInstanceCreated($user);
        }
    }

    /**
     * @test
     */
    public function it_creates_classes_with_a_given_name()
    {
        $factory = $this->getModelFactory();

        $user = $factory->createAs(Users::class, 'myUser');

        $this->assertInstanceOf(Users::class, $user);

        $this->seeInDatabase('users', [
            'username' => $user->username,
            'email'    => $user->email
        ]);
    }



    /**
     * Assert that given user is instance of Users and properties are set.
     *
     * @param  Users $user
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
     * @param  Users  $user
     */
    protected function assertUserInstanceCreated(Users $user)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->seeInDatabase('users', [
            'username' => $user->username,
            'email'    => $user->email
        ]);
    }

    /**
     * Create all paths necessary for seeding.
     *
     * @param  Config $config
     */
    protected function createAllPaths(Config $config)
    {
        $directories = $config->getAllDatabaseDirectories();

        $directories[] = __DIR__.'/../app/models';

        $this->makeDirectoryStructure($directories);
    }

    /**
     * Copy stubs to test app.
     *
     * @param  Config $config
     */
    protected function copyStubs(Config $config)
    {
        $this->writeFile(
            __DIR__.'/../app/models/Users.php',
            file_get_contents(__DIR__.'/../Stubs/userModel.stub')
        );

        $this->writeFile(
            $config->getFactoryDirectory('ModelFactory.php'),
            file_get_contents(__DIR__.'/../Stubs/factory.stub')
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
