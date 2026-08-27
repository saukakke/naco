<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cadet;
use App\Models\Lga;
use App\Models\Rank;
use App\Models\RankCategory;
use App\Models\State;
use App\Models\Unit;
use App\Models\User;
use App\Models\Ward;
use App\Services\AuthorizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_hcs_can_only_access_cadets_in_their_ward(): void
    {
        $state=State::create(['name'=>'State A','code'=>'SA']);
        $lga=Lga::create(['state_id'=>$state->id,'name'=>'LGA A']);
        $wardA=Ward::create(['lga_id'=>$lga->id,'name'=>'Ward A','code'=>'WA']);
        $wardB=Ward::create(['lga_id'=>$lga->id,'name'=>'Ward B','code'=>'WB']);
        $unit=Unit::create(['code'=>'A','name'=>'Unit A']);
        $category=RankCategory::create(['name'=>'Other Ranks','slug'=>'other-ranks','order'=>1]);
        $rank=Rank::create(['rank_category_id'=>$category->id,'name'=>'Private','slug'=>'private','order'=>1]);
        $cadetA=Cadet::create(['service_number'=>'NACO-001','first_name'=>'A','last_name'=>'Cadet','unit_id'=>$unit->id,'ward_id'=>$wardA->id,'rank_id'=>$rank->id]);
        $cadetB=Cadet::create(['service_number'=>'NACO-002','first_name'=>'B','last_name'=>'Cadet','unit_id'=>$unit->id,'ward_id'=>$wardB->id,'rank_id'=>$rank->id]);
        $user=User::create(['name'=>'HCS','email'=>'hcs@example.com','password'=>'password123','role'=>'hcs','ward_id'=>$wardA->id]);
        $authorization=app(AuthorizationService::class);

        $this->assertTrue($authorization->canAccessCadet($user,$cadetA));
        $this->assertFalse($authorization->canAccessCadet($user,$cadetB));
        $this->assertSame([$cadetA->service_number],$authorization->cadetQuery($user)->pluck('service_number')->all());
    }

    public function test_cadet_relationships_use_service_number_as_the_owner_key(): void
    {
        $state=State::create(['name'=>'State A','code'=>'SA']);
        $lga=Lga::create(['state_id'=>$state->id,'name'=>'LGA A']);
        $ward=Ward::create(['lga_id'=>$lga->id,'name'=>'Ward A','code'=>'WA']);
        $unit=Unit::create(['code'=>'A','name'=>'Unit A']);
        $category=RankCategory::create(['name'=>'Other Ranks','slug'=>'other-ranks','order'=>1]);
        $rank=Rank::create(['rank_category_id'=>$category->id,'name'=>'Private','slug'=>'private','order'=>1]);
        $cadet=Cadet::create(['service_number'=>'NACO-100','first_name'=>'Test','last_name'=>'Cadet','unit_id'=>$unit->id,'ward_id'=>$ward->id,'rank_id'=>$rank->id]);
        $user=User::create(['name'=>'Cadet','email'=>'cadet@example.com','password'=>'password123','role'=>'cadet','cadet_id'=>$cadet->service_number]);

        $this->assertSame($cadet->service_number,$user->cadet->service_number);
    }
}
