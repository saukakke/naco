<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cadet;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Lga;
use App\Models\Rank;
use App\Models\State;
use App\Models\Unit;
use App\Models\User;
use App\Models\Ward;
use App\Models\Warrant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        if (! filter_var(env('DEMO_ACCOUNTS_ENABLED', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->command?->info('Demo accounts disabled. Set DEMO_ACCOUNTS_ENABLED=true to create them.');
            return;
        }

        $password = (string) env('DEMO_ACCOUNTS_PASSWORD', '');
        if ($password === '' || strlen($password) < 12) {
            throw new RuntimeException('DEMO_ACCOUNTS_PASSWORD must be set and contain at least 12 characters when demo accounts are enabled.');
        }

        $unit = Unit::firstOrCreate(['code' => 'A'], ['name' => 'Demo Unit A']);
        $state = State::firstOrCreate(['code' => 'DM'], ['name' => 'Demo State']);
        $lga = Lga::firstOrCreate(['state_id' => $state->id, 'name' => 'Demo LGA']);
        $ward = Ward::firstOrCreate(['lga_id' => $lga->id, 'name' => 'Demo Ward'], ['code' => 'DM-01']);
        $private = Rank::where('slug', 'private')->firstOrFail();

        $this->account('Super Administrator', 'super_admin', 'demo.superadmin@naco.test', null, null, null, null, $password);
        $this->account('National Administrator', 'national', 'demo.national@naco.test', null, null, null, null, $password);
        $this->account('Unit Commander', 'unit_commander', 'demo.unit@naco.test', null, $unit->id, null, null, $password);
        $this->account('State Controller', 'state_controller', 'demo.state@naco.test', null, null, null, $state->id, $password);
        $this->account('Chairman Self-Reliance', 'chairman_self_reliance', 'demo.chairman@naco.test', null, null, $lga->id, $state->id, $password);
        $this->account('Ward Commander / HCS', 'hcs', 'demo.hcs@naco.test', null, null, $lga->id, $state->id, $password, $ward->id);

        $cadet = Cadet::firstOrCreate(
            ['service_number' => 'NACO-DEMO-0001'],
            ['first_name' => 'Demo', 'middle_name' => 'Portal', 'last_name' => 'Cadet', 'email' => 'demo.cadet@naco.test', 'gender' => 'other', 'unit_id' => $unit->id, 'ward_id' => $ward->id, 'rank_id' => $private->id, 'status' => 'active']
        );
        $this->account('Demo Cadet', 'cadet', 'demo.cadet@naco.test', $cadet->service_number, $unit->id, $lga->id, $state->id, $password, $ward->id);

        $instructorCadet = Cadet::firstOrCreate(
            ['service_number' => 'NACO-DEMO-0002'],
            ['first_name' => 'Demo', 'middle_name' => 'Portal', 'last_name' => 'Instructor', 'email' => 'demo.instructor@naco.test', 'gender' => 'other', 'unit_id' => $unit->id, 'ward_id' => $ward->id, 'rank_id' => $private->id, 'status' => 'active']
        );
        $this->account('Demo Instructor', 'instructor', 'demo.instructor@naco.test', $instructorCadet->service_number, $unit->id, $lga->id, $state->id, $password, $ward->id);
        Instructor::firstOrCreate(['cadet_id' => $instructorCadet->service_number], ['status' => 'active']);
        $course = Course::where('code', 'DRILL')->firstOrFail();
        Warrant::firstOrCreate(['warrant_number' => 'DEMO-WARRANT-0001'], ['cadet_id' => $instructorCadet->service_number, 'course_id' => $course->id, 'type' => 'instructor', 'issued_at' => today(), 'expires_at' => today()->addYear(), 'status' => 'active']);
    }

    private function account(string $name, string $role, string $email, ?string $cadetId, ?int $unitId, ?int $lgaId, ?int $stateId, string $password, ?int $wardId = null): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password), 'role' => $role, 'cadet_id' => $cadetId, 'unit_id' => $unitId, 'ward_id' => $wardId, 'lga_id' => $lgaId, 'state_id' => $stateId, 'email_verified_at' => now()]
        );
    }
}
