<?php

namespace Yarak\Tests\Unit;

use App\Models\Posts;
use App\Models\Users;
use Yarak\Helpers\Filesystem;
use Yarak\Tests\FactoryTestCase;

class FactoryTest extends FactoryTestCase
{
    use Filesystem;

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

        $this->tester->dontSeeRecord(Users::class, [
            'username' => $user->username,
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
            'email'    => 'bobsmsith@example.com',
        ];

        $user = $factory->make(Users::class, $attributes);

        $this->assertUserHasAttributes($user, $attributes);
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
        $factory = $this->getModelFactory();

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
        $factory = $this->getModelFactory();

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
        $factoryDir = $this->getConfig()->getFactoryDirectory();

        $this->filesystem->remove($factoryDir);

        $factory = $this->getModelFactory();

        $user = $factory->make(Users::class);
    }
}
