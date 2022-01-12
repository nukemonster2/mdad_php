<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'RoleID', 'CompanyID', 'RoleName', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
