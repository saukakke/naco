<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Notification;
use App\Models\PersonnelDocument;
use App\Services\AuthorizationService;
use App\Services\PersonnelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonnelController extends Controller
{
    public function show(Request $request,Cadet $cadet,AuthorizationService $authorization){abort_unless($authorization->canAccessCadet($request->user(),$cadet),403);return view('portal.personnel.show',['cadet'=>$cadet->load(['unit','ward.lga.state','rank.category','courses','warrants.course','promotions.toRank','demotions.toRank','postAssignments.post','idCardRenewals']),'documents'=>PersonnelDocument::where('cadet_id',$cadet->service_number)->latest()->get()]);}
    public function uploadDocument(Request $request,Cadet $cadet,PersonnelService $service){abort_unless($request->user()->hasGlobalAccess(),403);$data=$request->validate(['document'=>['required','file','max:10240','mimes:pdf,jpg,jpeg,png,webp'],'document_type'=>['required','string','max:80'],'title'=>['required','string','max:150'],'document_number'=>['nullable','string','max:100'],'issued_at'=>['nullable','date'],'expires_at'=>['nullable','date','after_or_equal:issued_at']]);$service->storeDocument($cadet,$data['document'],$data['document_type'],$data['title'],$data['document_number']??null,$data['issued_at']??null,$data['expires_at']??null);$service->audit($request->user()->id,'document.upload',$cadet,'Personnel document uploaded');return back()->with('success','Personnel document uploaded.');}
    public function downloadDocument(Request $request,PersonnelDocument $document,AuthorizationService $authorization){$document->loadMissing('cadet');abort_unless($authorization->canAccessCadet($request->user(),$document->cadet),403);abort_unless(Storage::disk('private')->exists($document->file_path),404);return Storage::disk('private')->download($document->file_path,$document->title);}
    public function destroyDocument(Request $request,PersonnelDocument $document,PersonnelService $service){abort_unless($request->user()->hasGlobalAccess(),403);$service->deleteDocument($document);$service->audit($request->user()->id,'document.delete',$document,'Personnel document deleted');return back()->with('success','Personnel document deleted.');}
    public function notifications(Request $request){return view('portal.notifications.index',['notifications'=>Notification::where('user_id',$request->user()->id)->latest()->paginate(20)]);}
    public function readNotification(Request $request,Notification $notification){abort_unless((int)$notification->user_id===(int)$request->user()->id,403);$notification->update(['read_at'=>now()]);return back();}
}
