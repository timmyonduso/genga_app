<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLoanApplicationRequest;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\LoanApplication;
use App\Mail\MailNotify;
use App\Role;
use App\Services\AuditLogService;
use App\Status;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class LoanApplicationsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('loan_application_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $loanApplications = LoanApplication::with('status', 'analyst', 'cfo')->get();

        foreach ($loanApplications as $loanApplication) {
            $loanApplication->checkOverdue();
            $loanApplication->calculateInterest();
        }

        $overdueLoans = $loanApplications->filter(function ($loanApplication) {
            return $loanApplication->overdue;
        });

        foreach ($overdueLoans as $loanApplication) {
            $loanApplication->applyPenalty();
        }

        $defaultStatus    = Status::find(1);
        $user             = auth()->user();

        return view('admin.loanApplications.index', compact('loanApplications', 'defaultStatus', 'user', 'overdueLoans'));
    }

    public function create()
    {
        abort_if(Gate::denies('loan_application_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.loanApplications.create');
    }

    public function store(StoreLoanApplicationRequest $request)
    {
        $loanApplication = LoanApplication::create($request->only('loan_amount', 'description', 'repayment_date'));

        // Get the user who applied for the loan
        $loaner = $loanApplication->created_by;

        $data = [
            'subject' => "Loan Application by $loaner->name",
            'body' => "I am requesting a loan of {$loanApplication->loan_amount} to be repaid by {$loanApplication->repayment_date->format('Y-m-d')}"

        ];
        try {
            Mail::to('timmyonduso85@gmail.com')->send(new MailNotify($data));
            return redirect()->route('admin.loan-applications.index')->with('message', 'Submitted. Admin will be in touch shortly');
        } catch (Exception $th) {
            return redirect()->route('admin.loan-applications.index')->with('message', 'Submitted. Admin will be in touch shortly');
        }
    }

    public function edit(LoanApplication $loanApplication)
    {
        abort_if(
            Gate::denies('loan_application_edit') || !in_array($loanApplication->status_id, [3, 6, 7, 8]),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $statuses = Status::whereIn('id', [1, 8, 9, 10])->pluck('name', 'id');

        $loanApplication->load('status');

        return view('admin.loanApplications.edit', compact('statuses', 'loanApplication'));
    }

    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loanApplication)
    {
        $loanApplication->update($request->only('loan_amount', 'description', 'repayment_date', 'status_id'));

        $sendEmail = in_array($loanApplication->status_id, [8, 9, 10]);

        if ($sendEmail) {
            // Get the user who applied for the loan
            $currentActiveStatus = Status::find($loanApplication->status_id);

            $data = [
                'subject' => "Your application has been $currentActiveStatus->name",
                'body' => "The status of your application has changed."
            ];

            try {
                Mail::to($loanApplication->created_by->email)->send(new MailNotify($data));
                return redirect()->route('admin.loan-applications.index')->with('message', 'Submitted. Admin will be in touch shortly');
            } catch (Exception $th) {
                return redirect()->route('admin.loan-applications.index')->with('message', 'Failed. Please try again to send your notification');
            }
        }

        return redirect()->route('admin.loan-applications.index')->with('message', 'Submitted. Admin will be in touch shortly');
    }


    public function show(LoanApplication $loanApplication)
    {
        abort_if(Gate::denies('loan_application_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loanApplication->load('status', 'analyst', 'cfo', 'created_by', 'logs.user', 'comments');
        $defaultStatus = Status::find(1);
        $user          = auth()->user();
//        $logs          = AuditLogService::generateLogs($loanApplication);

        return view('admin.loanApplications.show', compact('loanApplication', 'defaultStatus', 'user'));
    }

    public function destroy(LoanApplication $loanApplication)
    {
        abort_if(Gate::denies('loan_application_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loanApplication->delete();

        return back();
    }

    public function massDestroy(MassDestroyLoanApplicationRequest $request)
    {
        LoanApplication::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function showSend(LoanApplication $loanApplication)
    {
        abort_if(!auth()->user()->is_admin, Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($loanApplication->status_id == 1) {
            $role = 'Analyst';
            $users = Role::find(3)->users->pluck('name', 'id');
        } else if (in_array($loanApplication->status_id, [3,4])) {
            $role = 'CFO';
            $users = Role::find(4)->users->pluck('name', 'id');
        } else {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        return view('admin.loanApplications.send', compact('loanApplication', 'role', 'users'));
    }

    public function send(Request $request, LoanApplication $loanApplication)
    {
        abort_if(!auth()->user()->is_admin, Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($loanApplication->status_id == 1) {
            $column = 'analyst_id';
            $users  = Role::find(3)->users->pluck('id');
            $status = 2;
        } else if (in_array($loanApplication->status_id, [3,4])) {
            $column = 'cfo_id';
            $users  = Role::find(4)->users->pluck('id');
            $status = 5;
        } else {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $request->validate([
            'user_id' => 'required|in:' . $users->implode(',')
        ]);

        $loanApplication->update([
            $column => $request->user_id,
            'status_id' => $status
        ]);

        // Get the user who applied for the loan
        $loaner = $loanApplication->created_by;

        $data = [
            'subject' => "Loan Application to be analyzed for $loaner->name",
            'body' => "Please conduct proper screening of the loan application to check if $loaner->name is viable"

        ];
        try {
            Mail::to('timmyonduso85@gmail.com')->send(new MailNotify($data));
            return redirect()->route('admin.loan-applications.index')->with('message', 'Loan application has been sent for analysis');
        } catch (Exception $th) {
            return redirect()->route('admin.loan-applications.index')->with('message', 'Loan application has been sent for analysis');
        }
    }

    public function showAnalyze(LoanApplication $loanApplication)
    {
        $user = auth()->user();

        abort_if(
            (!$user->is_analyst || $loanApplication->status_id != 2) && (!$user->is_cfo || $loanApplication->status_id != 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return view('admin.loanApplications.analyze', compact('loanApplication'));
    }

    public function analyze(Request $request, LoanApplication $loanApplication)
    {
        $user = auth()->user();

        if ($user->is_analyst && $loanApplication->status_id == 2) {
            $status = $request->has('approve') ? 3 : 4;
        } else if ($user->is_cfo && $loanApplication->status_id == 5) {
            $status = $request->has('approve') ? 6 : 7;
        } else {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $request->validate([
            'comment_text' => 'required'
        ]);

        // Get the user who applied for the loan
        $loaner = $loanApplication->created_by;

        $loanApplication->comments()->create([
            'comment_text' => $request->comment_text,
            'user_id'      => $user->id
        ]);

        $loanApplication->update([
            'status_id' => $status
        ]);

        $data = [
            'subject' => "Analysis of loan application for $loaner->name ",
            'body' => "$loaner->name's analysis has been submitted with comment '$request->comment_text'"

        ];
        try {
            Mail::to('timmyonduso85@gmail.com')->send(new MailNotify($data));
            return redirect()->route('admin.loan-applications.index')->with('message', 'Analysis has been submitted');
        } catch (Exception $th) {
            return redirect()->route('admin.loan-applications.index')->with('message', 'Analysis has been submitted');
        }

    }
}
