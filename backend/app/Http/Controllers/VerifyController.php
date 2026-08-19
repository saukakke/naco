<?php

declare(strict_types=1);
namespace App\Http\Controllers;
use App\Models\Cadet;
use Illuminate\Http\Request;
class VerifyController extends Controller
{
 public function index(){return view('pages.verify');}
 public function search(Request $request){$data=$request->validate(['service_number'=>'required|string|max:50']);$cadet=Cadet::with(['rank','unit','ward','warrants.course'])->where('service_number',$data['service_number'])->first();return view('pages.verify',['cadet'=>$cadet,'searched'=>true,'serviceNumber'=>$data['service_number']]);}
}
