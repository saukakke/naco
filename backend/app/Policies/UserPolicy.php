<?php

declare(strict_types=1);
namespace App\Policies;
use App\Models\User;
class UserPolicy
{
 public function manageAdmins(User $actor):bool{return $actor->role==='super_admin';}
 public function manage(User $actor,User $target):bool{return $actor->role==='super_admin'||($actor->role==='admin'&&$target->role!=='super_admin');}
 public function view(User $actor,User $target):bool{if($actor->role==='super_admin'||$actor->role==='admin')return true;if($actor->id===$target->id)return true;if($actor->role==='instructor')return $actor->cadet_id!==null&&$target->cadet?->id===$actor->cadet_id;return false;}
}
