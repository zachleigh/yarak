<?php

namespace Yarak\tests\unit;

use App\Models\Posts;
use App\Models\Users;

class FactoryTest extends \Codeception\Test\Unit
{
    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->factorySetUp();
    }

    /**
     * @test
     */
    public function it_makes_a_single_model_instance()
    {
        $factory = $this->tester->getModelFactory();

        $user = $factory->make(Users::class);

        $this->tester->assertUserInstanceMade($user);
    }

    /**
     * @test
     */
    public function it_doesnt_save_models_when_making_them()
    {
        $factory = $this->tester->getModelFactory();

        $user = $factory->make(Users::class);

        $this->tester->dontSeeRecord(Users::class, [
            'username' => $user->username,
        ]);
    }

    /**
     * @test
     */
    public function it_uses_user_defined_attributes_when_making_model()
    {
        $factory = $this->tester->getModelFactory();

        $attributes = [
            'username' => 'bobsmith',
            'email'    => 'bobsmsith@example.com',
        ];

        $user = $factory->make(Users::class, $attributes);

        $this->tester->assertUserHasAttributes($user, $attributes);
    }

    /**
     * @test
     */
    public function it_makes_multiple_instances_of_models()
    {
        $factory = $this->tester->getModelFactory();

        $users = $factory->make(Users::class, [], 3);

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->tester->assertUserInstanceMade($user);
        }
    }

    /**
     * @test
     */
    public function it_makes_classes_with_a_given_name()
    {
        $factory = $this->tester->getModelFactory();

        $user = $factory->makeAs(Users::class, 'myUser');

        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals('myUsername', $user->username);
    }

    /**
     * @test
     */
    public function it_creates_a_single_model_instance()
    {
        $factory = $this->tester->getModelFactory();

        $user = $factory->create(Users::class);

        $this->tester->assertUserInstanceCreated($user, $this->tester);
    }

    /**
     * @test
     */
    public function it_uses_user_defined_attributes_when_creating_model()
    {
        $factory = $this->tester->getModelFactory();

        $attributes = [
            'username' => 'bobsmith',
            'email'    => 'bobsmsith@example.com',
        ];

        $user = $factory->create(Users::class, $attributes);

        $this->assertInstanceOf(Users::class, $user);

        $this->tester->seeRecord(Users::class, $attributes);
    }

    /**
     * @test
     */
    public function it_creates_multiple_instances_of_models()
    {
        $factory = $this->tester->getModelFactory();

        $users = $factory->create(Users::class, [], 3);

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->tester->assertUserInstanceCreated($user, $this->tester);
        }
    }

    /**
     * @test
     */
    public function it_creates_classes_with_a_given_name()
    {
        $factory = $this->tester->getModelFactory();

        $user = $factory->createAs(Users::class, 'myUser');

        $this->assertInstanceOf(Users::class, $user);

        $this->tester->seeRecord(Users::class, [
            'username' => $user->username,
            'email'    => $user->email,
        ]);
    }

    /**
     * @test
     */
    public function it_handles_relationship_closures()
    {
        $factory = $this->tester->getModelFactory();

        $post = $factory->createAs(Posts::class, 'withUser');

        $this->tester->seeRecord(Users::class, ['id' => $post->users->id]);

        $this->tester->seeRecord(Posts::class, ['users_id' => $post->users->id]);
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FactoryNotFound
     * @expectedExceptionMessage Definition for class App\Models\Posts with name invalid does not exist.
     */
    public function it_throws_exception_for_invalid_factory()
    {
        $factory = $this->tester->getModelFactory();

        $post = $factory->createAs(Posts::class, 'invalid');
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FactoryNotFound
     * @expectedExceptionMessage No factory definitions found.
     */
    public function it_throws_exception_when_no_factories_found()
    {
        $factoryDir = $this->tester->getConfig()->getFactoryDirectory();

        $this->tester->getFilesystem()->remove($factoryDir);

        $factory = $this->tester->getModelFactory();

        $user = $factory->make(Users::class);
    }
}
