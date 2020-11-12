<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class OtherBrowserSessionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (config('session.driver') !== 'database') {
            return response()->json();
        }

        return response()->json(
            collect(
                DB::table(config('session.table', 'sessions'))
                    ->where('user_id', $request->user()->getAuthIdentifier())
                    ->orderBy('last_activity', 'desc')
                    ->get()
            )->map(
                function ($session) use ($request) {
                    $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($session->user_agent));

                    return [
                        'agent'           => [
                            'platform' => $agent->platform(),
                            'browser'  => $agent->browser(),
                        ],
                        'ip'              => $session->ip_address,
                        'isCurrentDevice' => $session->id === $request->session()->getId(),
                        'lastActive'      => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ];
                }
            )->toArray()
        );
    }

    public function destroy(Request $request, StatefulGuard $guard): JsonResponse
    {
        if (! Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages(
                [
                    'password' => [__('This password does not match our records.')],
                ]
            )->errorBag('logoutOtherBrowserSessions');
        }

        $guard->logoutOtherDevices($request->password);

        $this->deleteOtherSessionRecords($request);

        return response()->json();
    }

    protected function deleteOtherSessionRecords(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
