<?php
namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
class AuditLogController extends Controller
{
 public function index(Request $request){abort_unless($request->user()->isAdmin(),403);$query=AuditLog::with('user')->latest();if($request->filled('action'))$query->where('action',$request->string('action'));if($request->filled('user_id'))$query->where('user_id',$request->integer('user_id'));return view('portal.audit-logs.index',['logs'=>$query->paginate(30)->withQueryString()]);}
}
