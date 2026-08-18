<?php
namespace App\Console\Commands;
use App\Models\Warrant;
use App\Services\PersonnelService;
use Illuminate\Console\Command;
class ProcessWarrantExpiry extends Command
{
 protected $signature='naco:warrants:process-expiry'; protected $description='Notify cadets and instructors about expiring warrants and mark expired instructor warrants inactive.';
 public function handle(PersonnelService $service):int{$notified=0;$expired=0;Warrant::with('cadet.user','course')->whereNotNull('expires_at')->whereDate('expires_at','<=',now()->addDays(60))->get()->each(function($w)use($service,&$notified,&$expired){if($w->expires_at->isPast()){if($w->is_active){$w->update(['is_active'=>false]);$expired++;}if($w->cadet?->user_id)$service->notify($w->cadet->user_id,'warrant.expired','Instructor Warrant Expired','Your warrant for '.$w->course->name.' has expired. You are no longer an active instructor for this course until a new valid warrant is issued.',['warrant_id'=>$w->id]);}elseif($w->cadet?->user_id){$days=now()->diffInDays($w->expires_at);$service->notify($w->cadet->user_id,'warrant.expiry_warning','Instructor Warrant Expiry Warning','Your warrant for '.$w->course->name.' expires in '.$days.' days.',['warrant_id'=>$w->id,'expires_at'=>$w->expires_at->toDateString()]);$notified++;}});$this->info("Expired: {$expired}; warnings sent: {$notified}");return self::SUCCESS;}
}
