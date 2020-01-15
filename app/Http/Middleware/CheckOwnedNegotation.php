<?php

namespace App\Http\Middleware;

use Closure;
use App\StaffNegotation;

class CheckOwnedNegotation
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
        $staff_negotation_id = $request->route()->parameter('negotations');
        if($staff_negotation_id) {
            $notice = StaffNegotation::findOrFail($staff_negotation_id);
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
