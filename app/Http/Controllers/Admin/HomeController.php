<?php

namespace App\Http\Controllers\Admin;

use App\LoanApplication;
use App\Status;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index()
    {

        $loanAmountsPerUser = LoanApplication::select('created_by_id', DB::raw('SUM(loan_amount) as total_loan_amount'))
            ->where('status_id', 8)
            ->groupBy('created_by_id')
            ->get();

        $totalLoanAmount = LoanApplication::where('status_id', 8)
                                            ->sum('loan_amount'); // Get the total loan amount

        $activeLoans = LoanApplication::where('status_id', '=', 8)->count();

        $numberOfApplicationsNotInStatus8 = LoanApplication::where('status_id', '!=', 8)
            ->where('status_id', '!=', 9)
            ->where('status_id', '!=', 10)
            ->count();

        $approvedApplications = LoanApplication::where('status_id', '=', 8)->count(); // Replace 'approved_status_id' with the actual ID representing approved status
        $totalApplications = LoanApplication::count();

// Safeguard against division by zero
        $approvalPercentage = $totalApplications > 0
            ? ($approvedApplications / $totalApplications) * 100
            : 0; // If no applications, set percentage to 0

        $totalLoanValue = LoanApplication::sum('loan_amount'); // Replace 'loan_amount' with the actual column name for loan amount
        $numberOfLoans = LoanApplication::count();

// Prevent division by zero
        $averageLoanValue = $numberOfLoans > 0
            ? $totalLoanValue / $numberOfLoans
            : 0; // Set to 0 if no loans exist

        // Retrieve pending loan applications efficiently
        $pendingLoanApplications = LoanApplication::where('status_id', '!=', 8)
            ->with('created_by')
            ->with('status')
            ->orderBy('status_id', 'asc')  // Add the orderBy clause here
            ->get();

        $pendingPayment = LoanApplication::where('status_id', '=', 8)
            ->with('created_by')
            ->with('status')
            ->orderBy('repayment_date', 'asc')
            ->orderBy('loan_amount', 'desc')
            ->get();

// Retrieve overdue loan applications
        $loanApplications = LoanApplication::with('status', 'analyst', 'cfo')->get();

        foreach ($loanApplications as $loanApplication) {
            $loanApplication->checkOverdue();
            $loanApplication->calculateInterest();
        }

        $overdueLoans = $loanApplications->filter(function ($loanApplication) {
            return $loanApplication->overdue;
        })->sortBy('repayment_date');

        foreach ($overdueLoans as $loanApplication) {
            $loanApplication->applyPenalty();
        }

        $defaultStatus = Status::find(1);
        $user = auth()->user();

        return view('home', compact('loanApplications','defaultStatus','overdueLoans','loanAmountsPerUser', 'user', 'totalLoanAmount', 'numberOfApplicationsNotInStatus8', 'activeLoans', 'approvalPercentage', 'averageLoanValue', 'pendingLoanApplications', 'pendingPayment'));
    }

}
