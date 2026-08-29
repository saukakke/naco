<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cadet;
use App\Models\Course;
use App\Models\Lga;
use App\Models\Rank;
use App\Models\RankCategory;
use App\Models\State;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warrant;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_audit_logs(): void
    {
        $superAdmin=User::create(['name'=>'Super Admin','email'=>'super-admin@example.com','password'=>'Password123','role'=>'super_admin']);
        $this->actingAs($superAdmin)->get(route('portal.audit-logs.index'))->assertOk();
    }

    public function test_user_policy_allows_regular_admin_to_manage_non_super_admin_but_not_super_admin(): void
    {
        $admin=User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>'Password123','role'=>'admin']);
        $user=User::create(['name'=>'User','email'=>'user@example.com','password'=>'Password123','role'=>'cadet','cadet_id'=>'NACO-001']);
        $superAdmin=User::create(['name'=>'Super Admin','email'=>'super@example.com','password'=>'Password123','role'=>'super_admin']);

        $this->actingAs($admin)->getJson(route('users.show',$user))->assertOk();
        $this->actingAs($admin)->getJson(route('users.show',$superAdmin))->assertForbidden();
        $this->actingAs($superAdmin)->getJson(route('users.show',$admin))->assertOk();
    }

    public function test_only_super_admin_can_assign_super_admin_role(): void
    {
        $admin=User::create(['name'=>'Admin','email'=>'admin2@example.com','password'=>'Password123','role'=>'admin']);
        $target=User::create(['name'=>'Target','email'=>'target@example.com','password'=>'Password123','role'=>'cadet','cadet_id'=>'NACO-001']);
        $this->actingAs($admin)->putJson(route('users.update',$target),['role'=>'super_admin'])->assertForbidden();
        $this->assertDatabaseHas('users',['id'=>$target->id,'role'=>'cadet']);
    }

    public function test_revoke_admin_rejects_user_without_linked_cadet(): void
    {
        $superAdmin=User::create(['name'=>'Super Admin','email'=>'super3@example.com','password'=>'Password123','role'=>'super_admin']);
        $admin=User::create(['name'=>'Admin','email'=>'admin3@example.com','password'=>'Password123','role'=>'admin']);

        $this->actingAs($superAdmin)->patch(route('portal.admin.roles.revoke',$admin))
            ->assertSessionHasErrors('role');
        $this->assertDatabaseHas('users',['id'=>$admin->id,'role'=>'admin','cadet_id'=>null]);
    }

    public function test_only_one_warrant_expiry_command_exists_and_it_updates_status(): void
    {
        $commands=Artisan::all();
        $expiryCommands=array_values(array_filter(array_keys($commands),fn(string $name):bool=>str_contains($name,'warrant')&&str_contains($name,'expir')));
        $this->assertSame(['naco:instructors:sync-warrants'],$expiryCommands);

        $unit=Unit::create(['code'=>'A','name'=>'Unit A']);
        $category=RankCategory::create(['name'=>'Other Ranks','slug'=>'other-ranks','order'=>1]);
        $rank=Rank::create(['rank_category_id'=>$category->id,'name'=>'Private','slug'=>'private','order'=>1]);
        $cadet=Cadet::create(['service_number'=>'NACO-W001','first_name'=>'Warrant','last_name'=>'Test','unit_id'=>$unit->id,'rank_id'=>$rank->id]);
        $course=Course::create(['code'=>'INS-001','name'=>'Instructor Course']);
        $warrant=Warrant::create(['cadet_id'=>$cadet->service_number,'course_id'=>$course->id,'warrant_number'=>'W-001','type'=>'instructor','issued_at'=>today()->subYear(),'expires_at'=>today()->subDay(),'status'=>'active']);

        Artisan::call('naco:instructors:sync-warrants');
        $this->assertDatabaseHas('warrants',['id'=>$warrant->id,'status'=>'expired']);
    }
}
