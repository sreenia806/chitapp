<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Hash;


class Member extends Model
{
    //
    protected $fillable = ['first_name', 'last_name', 'idproof', 'mobile', 'address', 'reference'];


    public function getNameAttribute() {
        return ucwords($this->last_name . ' ' . $this->first_name);
    }
}
