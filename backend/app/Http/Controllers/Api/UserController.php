<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
 private function guard(Request $r):void{abort_unless($r->user()->isAdmin(),403,'Administrator access required.');}
 public function index(Request $r){$this->guard($r);$q=User::with(['cadet','ward','lga','state'])->select(['id','name','email','role','cadet_id','ward_id','lga_id','state_id','created_at']);return response()->json($q->latest()->paginate(20));}
 public function store(Request $r){$this->guard($r);$d=$r->validate(['name'=>'required|string|max:150','email'=>'required|email|max:255|unique:users,email','password'=>'required|string|min:8','role'=>'required|string|max:50','cadet_id'=>'nullable|exists:cadets,id','ward_id'=>'nullable|exists:wards,id','lga_id'=>'nullable|exists:lgas,id','state_id'=>'nullable|exists:states,id']);$d['password']=Hash::make($d['password']);$u=User::create($d);return response()->json($u->makeHidden(['password','remember_token']),201);}
 public function show(Request $r,User $user){$this->guard($r);return response()->json($user->load(['cadet','ward','lga','state'])->makeHidden(['password','remember_token']));}
 public function update(Request $r,User $user){$this->guard($r);$d=$r->validate(['name'=>'sometimes|required|string|max:150','email'=>'sometimes|required|email|max:255|unique:users,email,'.$user->id,'password'=>'nullable|string|min:8','role'=>'sometimes|required|string|max:50','cadet_id'=>'nullable|exists:cadets,id','ward_id'=>'nullable|exists:wards,id','lga_id'=>'nullable|exists:lgas,id','state_id'=>'nullable|exists:states,id']);if(!empty($d['password']))$d['password']=Hash::make($d['password']);else unset($d['password']);$user->update($d);return response()->json($user->fresh()->makeHidden(['password','remember_token']));}
 public function destroy(Request $r,User $user){$this->guard($r);abort_if($r->user()->id===$user->id,422,'You cannot delete your own account.');$user->delete();return response()->noContent();}
}
