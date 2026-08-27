<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cadet;
use App\Models\IdCardRenewalApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IdCardRenewalService
{
    public function eligible(Cadet $cadet): bool
    {
        if (!$cadet->id_card_expires_at) return false;
        $expiry=Carbon::parse($cadet->id_card_expires_at)->startOfDay();
        $now=now()->startOfDay();
        return $now->gte($expiry->copy()->subMonths(2)) && $now->lt($expiry);
    }

    public function apply(Cadet $cadet,?string $reason=null):IdCardRenewalApplication
    {
        if (!$this->eligible($cadet)) throw ValidationException::withMessages(['renewal'=>'ID card renewal can only be requested within two months of the current card expiry date.']);
        if (IdCardRenewalApplication::where('cadet_id',$cadet->service_number)->whereIn('status',['pending','payment_pending','paid','approved'])->exists()) throw ValidationException::withMessages(['renewal'=>'The cadet already has an active ID card renewal application.']);
        return IdCardRenewalApplication::create(['cadet_id'=>$cadet->service_number,'reference'=>'NACO-IDR-'.now()->format('YmdHis').'-'.bin2hex(random_bytes(3)),'current_card_expires_at'=>$cadet->id_card_expires_at,'status'=>'payment_pending','reason'=>$reason]);
    }

    public function verifyPayment(IdCardRenewalApplication $application,string $reference):IdCardRenewalApplication
    {
        if ($application->status!=='payment_pending') throw ValidationException::withMessages(['payment'=>'Payment is not expected for this application.']);
        $application->update(['status'=>'paid','payment_reference'=>$reference,'payment_verified_at'=>now()]);
        return $application->fresh();
    }

    public function approve(IdCardRenewalApplication $application,int $adminId):IdCardRenewalApplication
    {
        if ($application->status!=='paid') throw ValidationException::withMessages(['renewal'=>'Payment must be verified before approval.']);
        $application->update(['status'=>'approved','approved_by'=>$adminId,'approved_at'=>now()]);
        return $application->fresh();
    }

    public function issue(IdCardRenewalApplication $application):IdCardRenewalApplication
    {
        if ($application->status!=='approved') throw ValidationException::withMessages(['renewal'=>'The renewal must be approved before the new ID card can be issued.']);
        return DB::transaction(function()use($application):IdCardRenewalApplication{
            $application->refresh();
            if ($application->status!=='approved') throw ValidationException::withMessages(['renewal'=>'This renewal has already been processed.']);
            $application->cadet()->update(['id_card_expires_at'=>now()->addYears(2)->toDateString()]);
            $application->update(['status'=>'issued','issued_at'=>now()]);
            return $application->fresh();
        });
    }
}
