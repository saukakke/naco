<?php
namespace Tests\Feature;
use App\Models\FourMonthlyReport;
use App\Models\ReportPeriod;
use App\Models\Ward;
use App\Models\User;
use App\Services\ReportWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class FourMonthlyReportWorkflowTest extends TestCase
{
 use RefreshDatabase;
 public function test_hcs_submission_moves_report_to_lga():void{$hcs=User::factory()->create();$ward=Ward::factory()->create();$hcs->cadet()->update(['ward_id'=>$ward->id]);$report=FourMonthlyReport::factory()->create(['ward_id'=>$ward->id,'report_period_id'=>ReportPeriod::factory(),'status'=>'draft']);app(ReportWorkflowService::class)->submit($report->fresh(),$hcs);$this->assertDatabaseHas('four_monthly_reports',['id'=>$report->id,'status'=>'submitted_to_lga']);}
 public function test_lga_cannot_review_report_from_another_lga():void{$chairman=User::factory()->create();$ward=Ward::factory()->create();$chairman->cadet()->update(['ward_id'=>$ward->id]);$other=Ward::factory()->create();$report=FourMonthlyReport::factory()->create(['ward_id'=>$other->id,'report_period_id'=>ReportPeriod::factory(),'status'=>'submitted_to_lga']);$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);app(ReportWorkflowService::class)->review($report,$chairman,'lga','approve');}
 public function test_national_finalizes_only_state_approved_report():void{$admin=User::factory()->create();$report=FourMonthlyReport::factory()->create(['status'=>'approved_by_state']);app(ReportWorkflowService::class)->review($report,$admin,'national','approve');$this->assertDatabaseHas('four_monthly_reports',['id'=>$report->id,'status'=>'finalized']);}
}
