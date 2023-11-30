<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index()
    {
        $auditTrails = AuditTrail::with('user', 'activity')->orderBy('id', 'desc')->get();

        return response()->json([
            'auditTrails' => $auditTrails,
            'message'  => 'auditTrails',
            'code'     => 200,
        ]);
    }
}
