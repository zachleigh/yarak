<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class Posts extends Model
{
    public function initialize()
    {
        $this->hasOne(
            'users_id',
            'App\Models\Users',
            'id',
            ['alias' => 'users']
        );
    }
}
