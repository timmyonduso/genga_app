@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.loanApplication.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.loan-applications.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="loan_amount">{{ trans('cruds.loanApplication.fields.loan_amount') }}</label>
                <input class="form-control {{ $errors->has('loan_amount') ? 'is-invalid' : '' }}" type="number" name="loan_amount" id="loan_amount" value="{{ old('loan_amount', '') }}" step="0.01" required>
                @if($errors->has('loan_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('loan_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.loanApplication.fields.loan_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="repayment_date">{{ trans('cruds.loanApplication.fields.repayment_date') }}</label>
                <input class="form-control {{ $errors->has('repayment_date') ? 'is-invalid' : '' }}" type="date" name="repayment_date" id="repayment_date" {{ old('repayment_date') }} required>

                @if($errors->has('repayment_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('repayment_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.loanApplication.fields.repayment_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.loanApplication.fields.description') }}</label>
                <textarea required class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description') }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.loanApplication.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
