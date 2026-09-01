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

class ApiAuthorizationMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_matrix_for_state_lga_ward_and_national_users(): void
    {
        [$stateA,$lgaA,$wardA,$wardB,$unit,$rank]= $this->structure();
        $stateB=State::create(['name'=>'State B','code'=>'SB']);
        $lgaB=Lga::create(['state_id'=>$stateB->id,'name'=>'LGA B']);
        $wardC=Ward::create(['lga_id'=>$lgaB->id,'name'=>'Ward C','code'=>'WC']);
        foreach ([['A','NACO-A',$wardA],['B','NACO-B',$wardB],['C','NACO-C',$wardC]] as [$n,$sn,$ward]) {
            Cadet::create(['service_number'=>$sn,'first_name'=>$n,'last_name'=>'Cadet','unit_id'=>$unit->id,'ward_id'=>$ward->id,'rank_id'=>$rank->id]);
        }
        $state=User::create(['name'=>'State','email'=>'state@example.com','password'=>'password123','role'=>'state_controller','state_id'=>$stateA->id]);
        $lga=User::create(['name'=>'LGA','email'=>'lga@example.com','password'=>'password123','role'=>'chairman_self_reliance','lga_id'=>$lgaA->id,'state_id'=>$stateA->id]);
        $hcs=User::create(['name'=>'HCS','email'=>'hcs@example.com','password'=>'password123','role'=>'hcs','ward_id'=>$wardA->id,'lga_id'=>$lgaA->id,'state_id'=>$stateA->id]);
        $national=User::create(['name'=>'National','email'=>'national@example.com','password'=>'password123','role'=>'national']);
        $auth=app(AuthorizationService::class);
        $this->assertCount(2,$auth->cadetQuery($state)->get());
        $this->assertCount(2,$auth->cadetQuery($lga)->get());
        $this->assertCount(1,$auth->cadetQuery($hcs)->get());
        $this->assertCount(3,$auth->cadetQuery($national)->get());
    }

    public function test_rank_and_promotion_business_rules_are_directional(): void
    {
        [$state,$lga,$ward,$unit,$rank]=array_values($this->structure());
        $category=RankCategory::first();
        $higher=Rank::create(['rank_category_id'=>$category->id,'name'=>'Corporal','slug'=>'corporal','order'=>2]);
        $cadet=Cadet::create(['service_number'=>'NACO-P','first_name'=>'Promo','last_name'=>'Cadet','unit_id'=>$unit->id,'ward_id'=>$ward->id,'rank_id'=>$rank->id]);
        $admin=User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>'password123','role'=>'admin']);
        $this->actingAs($admin)->postJson('/api/cadets/'.$cadet->service_number.'/promote',['new_rank_id'=>$higher->id,'promotion_date'=>now()->toDateString()])->assertCreated();
        $this->assertSame($higher->id,$cadet->fresh()->rank_id);
    }

    private function structure(): array
    {
        $state=State::create(['name'=>'State A','code'=>'SA']);
        $lga=Lga::create(['state_id'=>$state->id,'name'=>'LGA A']);
        $wardA=Ward::create(['lga_id'=>$lga->id,'name'=>'Ward A','code'=>'WA']);
        $wardB=Ward::create(['lga_id'=>$lga->id,'name'=>'Ward B','code'=>'WB']);
        $unit=Unit::create(['code'=>'U1','name'=>'Unit 1']);
        $category=RankCategory::create(['name'=>'Other Ranks','slug'=>'other-ranks','order'=>1]);
        $rank=Rank::create(['rank_category_id'=>$category->id,'name'=>'Private','slug'=>'private','order'=>1]);
        return [$state,$lga,$wardA,$wardB,$unit,$rank];
    }
}
