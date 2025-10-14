<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BusinessUnit;

class CheckBusinessUnitAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Super admin can access everything
        if ($user->hasRole('super admin')) {
            return $next($request);
        }

        // Get business unit ID from route parameter
        $businessUnitId = $request->route('business_unit') ?? $request->route('id');

        if ($businessUnitId) {
            $businessUnit = BusinessUnit::find($businessUnitId);

            if (!$businessUnit) {
                abort(404, 'Unit bisnis tidak ditemukan.');
            }

            // Check if user has access to this business unit
            $userBusinessUnits = $user->getBusinessUnits();
            if (!$userBusinessUnits->contains('id', $businessUnitId)) {
                abort(403, 'Anda tidak memiliki akses ke unit bisnis ini.');
            }
        }

        return $next($request);
    }
}
