<?php

namespace Yarak\tests\unit;

use MyApp\Models\Posts;
use MyApp\Models\Users;

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
    public function factory_makes_a_single_model_instance()
    {
        $factory = $this->tester->getModelFactory();

        $this->tester->assertUserInstanceMade($factory->make(Users::class));
    }

    /**
     * @test
     */
    public function factory_doesnt_save_models_when_making_them()
    {
        $factory = $this->tester->getModelFactory();

        $this->tester->dontSeeRecord(Users::class, [
            'username' => $factory->make(Users::class)->username,
        ]);
    }

    /**
     * @test
     */
    public function factory_uses_user_defined_attributes_when_making_model()
    {
        $factory = $this->tester->getModelFactory();

        $attributes = [
            'username' => 'bobsmith',
            'email'    => 'bobsmsith@example.com',
        ];

        $this->tester->assertUserHasAttributes(
            $factory->make(Users::class, $attributes),
            $attributes
        );
    }

    /**
     * @test
     */
    public function factory_makes_multiple_instances_of_models()
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
    public function factory_makes_classes_with_a_given_name()
    {
        $factory = $this->tester->getModelFactory();

        $user = $factory->makeAs(Users::class, 'myUser');

        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals('myUsername', $user->username);
    }

    /**
     * @test
     */
    public function factory_creates_a_single_model_instance()
    {
        $factory = $this->tester->getModelFactory();

        $this->tester->assertUserInstanceCreated(
            $factory->create(Users::class),
            $this->tester
        );
    }

    /**
     * @test
     */
    public function factory_uses_user_defined_attributes_when_creating_model()
    {
        $factory = $this->tester->getModelFactory();

        $attributes = [
            'username' => 'bobsmith',
            'email'    => 'bobsmsith@example.com',
        ];

        $this->assertInstanceOf(
            Users::class,
            $factory->create(Users::class, $attributes)
        );

        $this->tester->seeRecord(Users::class, $attributes);
    }

    /**
     * @test
     */
    public function factory_creates_multiple_instances_of_models()
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
    public function factory_creates_classes_with_a_given_name()
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
    public function factory_handles_relationship_closures()
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
     * @expectedExceptionMessage Definition for class MyApp\Models\Posts with name invalid does not exist.
     */
    public function factory_throws_exception_for_invalid_factory()
    {
        $this->tester->getModelFactory()->createAs(Posts::class, 'invalid');
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FactoryNotFound
     * @expectedExceptionMessage No factory definitions found.
     */
    public function factory_throws_exception_when_no_factories_found()
    {
        $this->tester->getFilesystem()->remove(
            $this->tester->getConfig()->getFactoryDirectory()
        );

        $this->tester->getModelFactory()->make(Users::class);
    }
}
