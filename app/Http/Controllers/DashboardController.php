<?php

namespace App\Http\Controllers;

use App\LoanApplication;

class DashboardController extends Controller
{
    public function index()
    {
        $loanApplications = LoanApplication::with('created_by')->get();
        $loanAmountsPerUser = $loanApplications->groupBy('created_by.id')
            ->map(function ($userLoans) {
                return [
                    'user_id' => $userLoans->first()->created_by->id,
                    'user_name' => $userLoans->first()->created_by->name,
                    'total_loan_amount' => $userLoans->sum('loan_amount'),
                ];
            });

//        dd($loanAmountsPerUser);

        return view('home', compact('loanAmountsPerUser'));
    }
}
