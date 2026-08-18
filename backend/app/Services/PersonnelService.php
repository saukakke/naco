<?php

declare(strict_types=1);
namespace App\Services;
use App\Mail\NacoNotificationMail;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\PersonnelDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
class PersonnelService
{
 public function notify(int $userId,string $type,string $title,string $message,array $data=[]):Notification{$notification=Notification::create(['user_id'=>$userId,'type'=>$type,'title'=>$title,'message'=>$message,'data'=>$data]);$user=$notification->user()->first();if($user?->email){Mail::to($user->email)->queue(new NacoNotificationMail($notification));}return $notification;}
 public function audit(?int $userId,string $action,$model,string $description,array $old=[],array $new=[]):AuditLog{return AuditLog::create(['user_id'=>$userId,'action'=>$action,'auditable_type'=>$model::class,'auditable_id'=>$model->getKey(),'description'=>$description,'old_values'=>$old,'new_values'=>$new,'ip_address'=>request()->ip(),'user_agent'=>request()->userAgent()]);}
 public function storeDocument($cadet,UploadedFile $file,string $type,string $title,?string $number=null,$issuedAt=null,$expiresAt=null,array $metadata=[]):PersonnelDocument{$path=$file->store('personnel/'.$cadet->id,'private');return PersonnelDocument::create(['cadet_id'=>$cadet->id,'document_type'=>$type,'title'=>$title,'file_path'=>$path,'document_number'=>$number,'issued_at'=>$issuedAt,'expires_at'=>$expiresAt,'metadata'=>$metadata]);}
 public function deleteDocument(PersonnelDocument $document):void{Storage::disk('private')->delete($document->file_path);$document->delete();}
}
