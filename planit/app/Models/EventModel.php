<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'date',
        'direccion',
        'category_id',
        'capacity',
        'price',
        'image',
        'user_id',
        'hour',
        'canceled',
        'city'
    ];
}
