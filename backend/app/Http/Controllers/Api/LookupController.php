<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function units(): JsonResponse { return response()->json(Unit::orderBy('name')->get()); }
    public function ranks(): JsonResponse { return response()->json(Rank::with('category')->orderBy('order')->get()); }
    public function posts(): JsonResponse { return response()->json(Post::orderBy('level')->orderBy('name')->get()); }
    public function courses(): JsonResponse { return response()->json(Course::orderBy('name')->get()); }
}
