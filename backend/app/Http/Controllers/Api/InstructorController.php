<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cadet;
use App\Models\Course;
use App\Services\InstructorQualificationService;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function show(Request $request, Cadet $cadet)
    {
        abort_unless($request->user()->isAdmin() || (int) $request->user()->cadet_id === (int) $cadet->id, 403);
        return response()->json([
            'cadet' => $cadet->load('instructor'),
            'is_instructor' => $cadet->isInstructor(),
            'valid_warrants' => $cadet->warrants()->where('status', 'active')->whereDate('issued_at', '<=', today())->whereDate('expires_at', '>', today())->with('course')->orderByDesc('expires_at')->get(),
        ]);
    }

    public function enroll(Request $request, Cadet $cadet, Course $course, InstructorQualificationService $service)
    {
        abort_unless($request->user()->isAdmin() || (int) $request->user()->cadet_id === (int) $cadet->id, 403);
        $service->enroll($cadet, $course);
        return response()->json(['message' => 'Course enrollment created.', 'cadet' => $cadet->fresh()->load('courses')], 201);
    }

    public function payment(Request $request, Cadet $cadet, Course $course, InstructorQualificationService $service)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['payment_reference' => ['required', 'string', 'max:150']]);
        $service->verifyPayment($cadet, $course, $data['payment_reference']);
        return response()->json(['message' => 'Course payment verified.']);
    }

    public function result(Request $request, Cadet $cadet, Course $course, InstructorQualificationService $service)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['passed' => ['required', 'boolean']]);
        $service->recordResult($cadet, $course, (bool) $data['passed']);
        return response()->json(['message' => 'Course result recorded.']);
    }

    public function issueWarrant(Request $request, Cadet $cadet, Course $course, InstructorQualificationService $service)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['validity_months' => ['nullable', 'integer', 'min:1', 'max:60']]);
        $warrant = $service->issueWarrant($cadet, $course, (int) ($data['validity_months'] ?? 24));
        return response()->json($warrant, 201);
    }
}
