<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Tabs/Logs/Index', [
            'filters' => $request->all('search'),
            'sorts' => ["sortBy" => $request->sortBy ?? "created_at", "sortType" => $request->sortType ?? "desc"],
            'paginate' => Activity::where('log_name', $request->only('log_name'))
                ->orderBy($request->sortBy ?? 'created_at', $request->sortType ?? 'desc')
                ->paginate($request->per_page ?? 10)
                ->withQueryString()
                ->through(fn($log) => [
                    'subject_id' => $log->subject && $log->subject_type == "App\\Models\\Wish" ?
                        $log->subject->occasion_id : $log->subject_id,
                    'subject_type' => $log->subject_type,
                    'subject_name' => $log->subject && $log->subject->full_name ? $log->subject->full_name : ($log->subject && $log->subject->title ? $log->subject->title : null),
                    'causer_id' => $log->causer_id,
                    'causer_type' => $log->causer_type,
                    'causer_name' => $log->causer->full_name,
                    'log_name' => $log->log_name,
                    'description' => $log->description,
                    'event' => $log->event,
                    'changes' => count($log->changes()) ? $log->changes() : null,
                    'created_at' => $log->created_at,
                ]),
        ]);
    }
    public function clear()
    {
        \Illuminate\Support\Facades\Artisan::call('activitylog:clean --force');
        return to_route('logs.index');
    }
}
