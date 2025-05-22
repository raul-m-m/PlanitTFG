<?php
namespace App\Models;

use CodeIgniter\Model;

class PraiseModel extends Model
{
    protected $table = 'praises';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_praiser', 'id_praised'];
}