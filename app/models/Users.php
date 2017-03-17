<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class Users extends Model
{
    public function initialize()
    {
        $this->hasMany(
            'id',
            'App\Models\Posts',
            'users_id',
            ['alias' => 'posts']
        );
    }

    public function getID() {
        return $this->id;
    }
}
