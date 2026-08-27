<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\FourMonthlyReport;
use App\Models\Lga;
use App\Models\ReportPeriod;
use App\Models\State;
use App\Models\User;
use App\Models\Ward;
use App\Services\ReportWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FourMonthlyReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_hcs_submission_moves_report_to_lga(): void
    {
        [$state,$lga,$ward]=$this->location('A');
        $hcs=User::create(['name'=>'HCS','email'=>'hcs-a@example.com','password'=>'password123','role'=>'hcs','ward_id'=>$ward->id,'lga_id'=>$lga->id,'state_id'=>$state->id]);
        $period=$this->period();
        $report=FourMonthlyReport::create(['ward_id'=>$ward->id,'report_period_id'=>$period->id,'status'=>'draft','summary'=>'Ward report','submitted_by'=>$hcs->id]);
        app(ReportWorkflowService::class)->submit($report,$hcs);
        $this->assertDatabaseHas('four_monthly_reports',['id'=>$report->id,'status'=>'submitted_to_lga']);
    }

    public function test_lga_cannot_review_report_from_another_lga(): void
    {
        [$stateA,$lgaA,$wardA]=$this->location('A');
        [$stateB,$lgaB,$wardB]=$this->location('B');
        $chairman=User::create(['name'=>'Chairman','email'=>'chairman@example.com','password'=>'password123','role'=>'chairman_self_reliance','lga_id'=>$lgaA->id,'state_id'=>$stateA->id]);
        $period=$this->period();
        $report=FourMonthlyReport::create(['ward_id'=>$wardB->id,'report_period_id'=>$period->id,'status'=>'submitted_to_lga','summary'=>'Ward report']);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(ReportWorkflowService::class)->review($report,$chairman,'lga','approve');
    }

    public function test_national_finalizes_only_state_approved_report(): void
    {
        [$state,$lga,$ward]=$this->location('A');
        $national=User::create(['name'=>'National','email'=>'national@example.com','password'=>'password123','role'=>'national']);
        $period=$this->period();
        $report=FourMonthlyReport::create(['ward_id'=>$ward->id,'report_period_id'=>$period->id,'status'=>'approved_by_state','summary'=>'Ward report']);
        app(ReportWorkflowService::class)->review($report,$national,'national','approve');
        $this->assertDatabaseHas('four_monthly_reports',['id'=>$report->id,'status'=>'finalized']);
    }

    private function location(string $suffix): array
    {
        $state=State::create(['name'=>'State '.$suffix,'code'=>'S'.$suffix]);
        $lga=Lga::create(['state_id'=>$state->id,'name'=>'LGA '.$suffix]);
        $ward=Ward::create(['lga_id'=>$lga->id,'name'=>'Ward '.$suffix,'code'=>'W'.$suffix]);
        return [$state,$lga,$ward];
    }

    private function period(): ReportPeriod
    {
        return ReportPeriod::create(['year'=>2026,'period'=>1,'starts_on'=>'2026-01-01','ends_on'=>'2026-04-30','due_on'=>'2026-05-10','status'=>'open']);
    }
}
