<?php

declare(strict_types=1);
namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class User extends Authenticatable {
 use HasApiTokens,Notifiable;
 protected $fillable=['name','email','password','role','cadet_id','ward_id','lga_id','state_id'];
 protected $hidden=['password','remember_token'];
 protected function casts():array{return ['email_verified_at'=>'datetime','password'=>'hashed'];}
 public function cadet():BelongsTo{return $this->belongsTo(Cadet::class);}
 public function ward():BelongsTo{return $this->belongsTo(Ward::class);}
 public function lga():BelongsTo{return $this->belongsTo(Lga::class);}
 public function state():BelongsTo{return $this->belongsTo(State::class);}
 public function isAdmin():bool{return in_array($this->role,['admin','administrator'],true);}
}
