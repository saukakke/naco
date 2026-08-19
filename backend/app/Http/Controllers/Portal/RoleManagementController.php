<?php
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;use App\Models\User;use Illuminate\Http\Request;use Illuminate\Validation\Rule;
class RoleManagementController extends Controller
{
 public function index(){abort_unless(auth()->user()->role==='super_admin',403);return view('portal.admin.roles',['users'=>User::whereNotIn('role',['super_admin'])->latest()->paginate(30)]);}
 public function update(Request $request,User $user){abort_unless(auth()->user()->role==='super_admin',403);abort_if($user->role==='super_admin',403);$data=$request->validate(['role'=>['required',Rule::in(['cadet','instructor','unit_commander','admin'])]]);$user->update(['role'=>$data['role']]);return back()->with('success','User role updated.');}
 public function revokeAdmin(User $user){abort_unless(auth()->user()->role==='super_admin',403);abort_if($user->role==='super_admin',403);if($user->role==='admin')$user->update(['role'=>'cadet']);return back()->with('success','Admin role revoked.');}
}
