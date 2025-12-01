<?php

namespace App\Modules\Admin\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Domain\Models\Company;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalCompanies = Company::count();
        $pendingCompanies = Company::where('status', 'pending')->count();
        $activeCompanies = Company::where('status', 'active')->count();
        $declinedCompanies = Company::where('status', 'declined')->count();

        return view('admin.dashboard', compact(
            'totalCompanies',
            'pendingCompanies',
            'activeCompanies',
            'declinedCompanies'
        ));
    }
}
