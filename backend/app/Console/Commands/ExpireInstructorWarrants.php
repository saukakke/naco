<?php

declare(strict_types=1);
namespace App\Console\Commands;
use App\Models\Warrant;
use App\Services\PersonnelService;
use Illuminate\Console\Command;
class ExpireInstructorWarrants extends Command
{
 protected $signature='naco:instructors:sync-warrants';
 protected $description='Synchronize instructor eligibility with warrant validity and notify affected cadets.';
 public function handle(PersonnelService $notifications):int
 {
  $expired=0;$warnings=0;
  Warrant::with(['cadet.user','course'])->where('status','active')->whereNotNull('expires_at')->get()->each(function(Warrant $w)use($notifications,&$expired,&$warnings){
   if($w->expires_at->isPast()){$w->update(['status'=>'expired']);$expired++;if($w->cadet?->user_id)$notifications->notify($w->cadet->user_id,'warrant.expired','Instructor warrant expired','Your '.$w->course->name.' warrant has expired. You are no longer an active instructor for this course until a new valid warrant is issued.',['warrant_id'=>$w->id]);return;}
   if($w->expires_at->lte(now()->addDays(30))&&$w->cadet?->user_id){$warnings++;$notifications->notify($w->cadet->user_id,'warrant.expiry_warning','Instructor warrant expires soon','Your '.$w->course->name.' warrant expires on '.$w->expires_at->format('d M Y').'.',['warrant_id'=>$w->id,'expires_at'=>$w->expires_at->toDateString()]);}
  });
  $this->info("Expired: {$expired}; warnings: {$warnings}");return self::SUCCESS;
 }
}
