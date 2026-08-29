<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleManagementController extends Controller
{
    private const ROLES=['admin','instructor','unit_commander','cadet'];
    public function index(Request $request){abort_unless($request->user()->isSuperAdmin(),403);return view('portal.admin.roles',['users'=>User::where('role','!=','super_admin')->latest()->paginate(30)]);}
    public function update(Request $request,User $user){abort_unless($request->user()->isSuperAdmin(),403);abort_if($user->isSuperAdmin(),403);$data=$request->validate(['role'=>['required',Rule::in(self::ROLES)]]);$user->update(['role'=>$data['role']]);return back()->with('success','User role updated.');}
    public function revokeAdmin(Request $request,User $user)
    {
        abort_unless($request->user()->isSuperAdmin(),403);
        abort_if($user->isSuperAdmin(),403);
        if($user->isAdmin()){
            if($user->cadet_id===null){
                return back()->withErrors(['role'=>'This user has no linked cadet record and cannot be assigned the cadet role; choose a different role or link a cadet record first.']);
            }
            $user->update(['role'=>'cadet']);
        }
        return back()->with('success','Admin role revoked.');
    }
}
