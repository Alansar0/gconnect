<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class RouterSetting extends Model
{
protected $guarded = [];


protected $casts = [
'global_sold_out_until' => 'datetime',
];


public function reseller()
{
return $this->belongsTo(Reseller::class);
}
}
