@extends('layouts.admin')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection


@section('content')
    <div class="row gy-4">
        <!-- Welcome card -->
        <div class="col-md-12 col-lg-4">
            <div class="card">
                <div class="card-body ">
                    <h4 class="card-title mb-1">Welcome <span>{{ Auth::user()->name }} 🎉</span></h4>
                    @if($user->is_admin || $user->is_analyst || $user->is_cfo)
                    <p class="pb-0">Total Amount Borrowed</p>
                        <h4>
                            @if ($loanAmountsPerUser->isEmpty())
                                <span class="text-danger">No user loan information available.</span>
                            @else
                                ksh {{ number_format($totalLoanAmount, 0) }}
                            @endif
                        </h4>
                    @else
                        <p class="pb-0">Your outstanding loan amount</p>
                        <h4>
                            @if ($loanAmountsPerUser->isEmpty())
                                <span class="text-danger fs-4">No user loan information available.</span>
                            @else
                                @foreach ($loanAmountsPerUser as $userLoanData)
                                    ksh {{ number_format($userLoanData->total_loan_amount, 0) }}
                                @endforeach
                            @endif
                        </h4>
                    @endif
{{--                    <p class="mb-2 pb-1">Less than 0% of members</p>--}}
                    @if ($loanAmountsPerUser->isEmpty())
                        <a href="{{ route("admin.loan-applications.index") }}" class="btn btn-sm bg-primary text-white">Make an application</a>
                    @else
                        <a href="{{ route("admin.loan-applications.index") }}" class="btn btn-sm bg-primary text-white">View applications</a>
                    @endif
                </div>
                    <img src="{{asset('assets/img/icons/misc/triangle-light.png')}}" class="scaleX-n1-rtl position-absolute bottom-0 end-0" width="166" alt="triangle background">
                    <img src="{{asset('assets/png/welcome.png')}}" class="scaleX-n1-rtl position-absolute bottom-0 end-0 me-4 mb-4 pb-2 mt-5" width="60" alt="view sales">
            </div>
        </div>
        <!--/ Welcome card -->

        <!-- Transactions -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Transactions</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                <a class="dropdown-item" href="javascript:void(0);">Update</a>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3"><span class="fw-medium">Summary of processes</span> this month</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow">
                                        <i class="mdi mdi-trending-up mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Active loans</div>
                                    <h5 class="mb-0">{{ $activeLoans }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow">
                                        <i class="mdi mdi-account-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">% of approved loans</div>
                                    <h5 class="mb-0">{{ number_format($approvalPercentage, 0) }}%</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow">
                                        <i class="mdi mdi-cellphone-link mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Incomplete applications</div>
                                    <h5 class="mb-0">{{ $numberOfApplicationsNotInStatus8 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="mdi mdi-currency-usd mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Average loan amount</div>
                                    <h5 class="mb-0">ksh {{ number_format($averageLoanValue, 0) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Transactions -->

        <!-- Members loan details -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Members to be given loan</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="saleStatus" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical mdi-24px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="saleStatus">
                            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($pendingLoanApplications as $loanApplication)
                        @if (!in_array($loanApplication->status_id, [8, 10]))
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-label-success rounded-circle">{{ substr($loanApplication->created_by->name, 0, 1) }}</div>

                                        {{-- <div class="avatar-initial bg-label-{{ $loanApplication->status_color }} rounded-circle">{{ $loanApplication->user->initials }}</div>--}}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{ $loanApplication->created_by->name }}</h6>
                                        </div>
                                        <small>{{ $loanApplication->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">Id: {{ $loanApplication->id ?? '' }} </h6>
                                    @if(in_array($loanApplication->status_id, [1,2,3,4,5,6,7]))
                                        <small class="btn btn-xs bg-info text-white">Processing</small>
                                    @elseif($loanApplication->status_id = 9)
                                        <small class="btn btn-xs bg-danger text-white">Rejected</small>
                                    @endif
                                </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <!--/  Members loan details  -->

        <!-- Pending & Overdue payments -->
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-body row g-2">
                            <div class="col-12 col-md-6 card-separator pe-0 pe-md-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                    <h5 class="m-0 me-2">Pending payments</h5>
                                    <a class="fw-medium" href="javascript:void(0);">View all</a>
                                </div>
                                @foreach ($pendingPayment as $loanApplication)
                                    @if ($loanApplication->status_id = 8 && !$loanApplication->isOverdue())
                                        <div class="pt-2">
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">{{ $loanApplication->created_by->name }}</h6>
                                                    <small>{{ $loanApplication->repayment_date->format('M d, Y') }}</small>
                                                </div>
                                                <h6 class="text-success mb-0">ksh {{ number_format($loanApplication->interest_rate, 0) }}</h6>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-12 col-md-6 pe-0 pe-md-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                    <h5 class="m-0 me-2">Overdue payments</h5>
                                    <a class="fw-medium" href="javascript:void(0);">View all</a>
                                </div>
                                <div class="pt-2">
                                    <ul class="p-0 m-0">
                                        @foreach ($overdueLoans as $loan)
                                            <li class="d-flex mb-4 align-items-center pb-2">
                                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-0">{{ $loan->created_by->name }}</h6> <small>{{ $loan->repayment_date->format('M d, Y') }}</small>
                                                    </div>
                                                    <h6 class="text-danger mb-0">ksh {{ number_format($loan->penalty_amount, 2) }}</h6>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                </div>
            </div>
        </div>
        <!--  Pending & Overdue payments  -->


        <!-- Pending payments and priority levels -->
        <!--/ Pending payments and priority levels -->


        <!-- Data Tables -->
        <div class="card">
            <div class="card-header">
                {{ trans('cruds.loanApplication.title_singular') }} {{ trans('global.list') }}
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatable datatable-LoanApplication">
                            <thead class="table-light">
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.loanApplication.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.loanApplication.fields.loan_amount') }}
                                </th>
                                <th>
                                    {{ trans('cruds.loanApplication.fields.repayment_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.loanApplication.fields.interest') }}
                                </th>
                                <th>
                                    {{ trans('cruds.loanApplication.fields.penalty') }}
                                </th>
                                {{--                        <th>--}}
                                {{--                            {{ trans('cruds.loanApplication.fields.description') }}--}}
                                {{--                        </th>--}}
                                <th>
                                    {{ trans('cruds.loanApplication.fields.overdue') }}
                                </th>
                                <th>
                                    {{ trans('cruds.loanApplication.fields.status') }}
                                </th>
                                {{--                        @if($user->is_admin)--}}
                                {{--                            <th>--}}
                                {{--                                {{ trans('cruds.loanApplication.fields.analyst') }}--}}
                                {{--                            </th>--}}
                                {{--                            <th>--}}
                                {{--                                {{ trans('cruds.loanApplication.fields.cfo') }}--}}
                                {{--                            </th>--}}
                                {{--                        @endif--}}
                                <th>
                                    &nbsp;
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($loanApplications as $key => $loanApplication)
                                <tr data-entry-id="{{ $loanApplication->id }}">
                                    <td>
                                        <div class="avatar me-3">
                                            <div class="avatar-initial bg-label-success rounded-circle">{{ substr($loanApplication->created_by->name, 0, 1) }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{ $loanApplication->id ?? '' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{ $loanApplication->loan_amount ?? '' }}</td>
                                    <td class="text-truncate">{{ $loanApplication->repayment_date ? $loanApplication->repayment_date->format('Y-m-d') : '' }}</td>
                                    <td class="text-truncate">{{  $loanApplication->interest_rate ?? '' }}</td>
                                    <td class="text-truncate">{{ number_format($loanApplication->penalty_amount, 2) }}</td>
                                    <td class="text-truncate">
                                        @if ($loanApplication->overdue)
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </td>
                                    <td class="text-truncate">{{ $user->is_user && $loanApplication->status_id < 8 ? $defaultStatus->name : $loanApplication->status->name }}</td>
                                    <td class="text-truncate">
                                        @if($user->is_admin && in_array($loanApplication->status_id, [1, 3, 4]))
                                            <a class="btn btn-xs btn-success" href="{{ route('admin.loan-applications.showSend', $loanApplication->id) }}">
                                                Send to
                                                @if($loanApplication->status_id == 1)
                                                    Secretary
                                                @else
                                                    Secretary 2
                                                @endif
                                            </a>
                                        @elseif(($user->is_analyst && $loanApplication->status_id == 2) || ($user->is_cfo && $loanApplication->status_id == 5))
                                            <a class="btn btn-xs btn-success" href="{{ route('admin.loan-applications.showAnalyze', $loanApplication->id) }}">
                                                Submit analysis
                                            </a>
                                        @endif

                                        @can('loan_application_show')
                                            <a class="btn btn-xs bg-primary text-white" href="{{ route('admin.loan-applications.show', $loanApplication->id) }}">
                                                {{ trans('global.view') }}
                                            </a>
                                        @endcan

                                        @if(Gate::allows('loan_application_edit') && in_array($loanApplication->status_id, [3,6,7,8]))
                                            <a class="btn btn-xs btn-info" href="{{ route('admin.loan-applications.edit', $loanApplication->id) }}">
                                                {{ trans('global.edit') }}
                                            </a>
                                        @endif

                                        @can('loan_application_delete')
                                            <form action="{{ route('admin.loan-applications.destroy', $loanApplication->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Data Tables -->
    </div>
@endsection
