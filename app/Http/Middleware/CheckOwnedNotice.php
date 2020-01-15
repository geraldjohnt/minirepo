<?php

namespace App\Http\Middleware;

use Closure;
use App\StaffNotice;

class CheckOwnedNotice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $staff_notice_id = $request->route()->parameter('notices');
        if($staff_notice_id) {
            $notice = StaffNotice::findOrFail($staff_notice_id);
            if($request->user()->id !== $notice->staff->user->id) {
                return response([
                    'message' => 'Unauthorized',
                    'status_code' => 401
                ], 401);
            }
        }

        return $next($request);
    }
}
