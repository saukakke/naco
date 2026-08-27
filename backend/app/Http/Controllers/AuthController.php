<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const ROLES=['super_admin','admin','national','instructor','unit_commander','cadet','hcs','chairman_self_reliance','state_controller'];
    public function login(): View { return view('auth.login'); }
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials=$request->validate(['login'=>['required','string','max:150'],'password'=>['required','string'],'role'=>['required','in:'.implode(',',self::ROLES)]]);
        $field=filter_var($credentials['login'],FILTER_VALIDATE_EMAIL)?'email':'cadet_id';
        if(!Auth::attempt([$field=>$credentials['login'],'password'=>$credentials['password'],'role'=>$credentials['role']],$request->boolean('remember'))) return back()->withErrors(['login'=>'The supplied credentials are invalid.'])->onlyInput('login');
        $request->session()->regenerate();
        return redirect()->intended(route('portal.dashboard'));
    }
    public function logout(Request $request): RedirectResponse { Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('portal.login'); }
}
