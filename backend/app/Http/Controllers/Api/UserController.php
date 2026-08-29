<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lga;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private const ROLES=['super_admin','admin','instructor','unit_commander','cadet','hcs','chairman_self_reliance','state_controller','national'];

    public function index(Request $request)
    {
        $this->authorize('manage', $request->user());
        $q=User::with(['cadet','unit','ward','lga','state'])->select(['id','name','email','role','cadet_id','unit_id','ward_id','lga_id','state_id','created_at']);
        return response()->json($q->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data=$this->validatedUserData($request);
        $target=new User(['role'=>$data['role']??null]);
        if ($target->isSuperAdmin()) $this->authorize('manageAdmins', User::class);
        else $this->authorize('manage', $target);
        $user=User::create($data);
        return response()->json($user->load(['cadet','unit','ward','lga','state'])->makeHidden(['password','remember_token']),201);
    }

    public function show(Request $request,User $user)
    {
        $this->authorize('manage',$user);
        return response()->json($user->load(['cadet','unit','ward','lga','state'])->makeHidden(['password','remember_token']));
    }

    public function update(Request $request,User $user)
    {
        $data=$this->validatedUserData($request,$user);
        if ($user->isSuperAdmin() || (($data['role']??$user->role)==='super_admin')) $this->authorize('manageAdmins', User::class);
        else $this->authorize('manage',$user);
        $user->update($data);
        return response()->json($user->fresh()->load(['cadet','unit','ward','lga','state'])->makeHidden(['password','remember_token']));
    }

    public function destroy(Request $request,User $user)
    {
        $this->authorize('manage',$user);
        abort_if($request->user()->id===$user->id,422,'You cannot delete your own account.');
        $user->delete();
        return response()->noContent();
    }

    private function validatedUserData(Request $request,?User $existing=null):array
    {
        $create=$existing===null;
        $rules=['name'=>[$create?'required':'sometimes','string','max:150'],'email'=>[$create?'required':'sometimes','email','max:255',Rule::unique('users','email')->ignore($existing?->id)],'password'=>[$create?'required':'nullable','string',Password::min(10)->mixedCase()->numbers()],'role'=>[$create?'required':'sometimes',Rule::in(self::ROLES)],'cadet_id'=>['sometimes','nullable','exists:cadets,service_number'],'unit_id'=>['sometimes','nullable','exists:units,id'],'ward_id'=>['sometimes','nullable','exists:wards,id'],'lga_id'=>['sometimes','nullable','exists:lgas,id'],'state_id'=>['sometimes','nullable','exists:states,id']];
        $data=$request->validate($rules);
        if(array_key_exists('password',$data)&&blank($data['password']))unset($data['password']);
        $role=$data['role']??$existing?->role;
        foreach(['cadet_id','unit_id','ward_id','lga_id','state_id'] as $field)$data[$field]=$data[$field]??$existing?->{$field};
        $this->normalizeRoleScope($data,$role);
        foreach(['cadet_id','unit_id','ward_id','lga_id','state_id'] as $field)if(array_key_exists($field,$data)&&$data[$field]===null)unset($data[$field]);
        return $data;
    }

    private function normalizeRoleScope(array &$data,?string $role):void
    {
        if($role==='unit_commander')abort_unless(!empty($data['unit_id']),422,'A Unit Commander must be assigned to a unit.');
        elseif($role==='hcs'){abort_unless(!empty($data['ward_id']),422,'An HCS must be assigned to a ward.');$ward=Ward::with('lga.state')->findOrFail($data['ward_id']);$data['lga_id']=$ward->lga_id;$data['state_id']=$ward->lga->state_id;}
        elseif($role==='chairman_self_reliance'){abort_unless(!empty($data['lga_id']),422,'A Chairman Self-Reliance must be assigned to an LGA.');$lga=Lga::findOrFail($data['lga_id']);$data['state_id']=$lga->state_id;$data['ward_id']=null;}
        elseif($role==='state_controller'){abort_unless(!empty($data['state_id']),422,'A State Controller must be assigned to a state.');$data['ward_id']=null;$data['lga_id']=null;}
        elseif(in_array($role,['cadet','instructor'],true))abort_unless(!empty($data['cadet_id']),422,'This role must be linked to a cadet.');
    }
}
