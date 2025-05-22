<?php

namespace App\Models;

use CodeIgniter\Model;

class EventUserModel extends Model
{
    protected $table = 'event_user';
    protected $primaryKey = ['id_user', 'id_event'];
    protected $allowedFields = ['id_user', 'id_event'];
}
