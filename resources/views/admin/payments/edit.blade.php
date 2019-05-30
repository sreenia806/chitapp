@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.ledger-entry.title')</h3>

    {!! Form::model($ledger_entry, ['method' => 'PUT', 'route' => ['admin.ledger_entries.update', $ledger_entry->id], 'id' => 'ledger_entry']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('ledger_category_id', trans('quickadmin.ledger-entry.fields.ledger-category').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('ledger_category_id', $ledger_categories, old('ledger_category_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('ledger_category_id'))
                        <p class="help-block">
                            {{ $errors->first('ledger_category_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('description', 'Description*', ['class' => 'control-label']) !!}
                    {!! Form::text('description', old('description'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if ($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('entry_date', trans('quickadmin.ledger-entry.fields.entry-date').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('entry_date', old('entry_date'), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('entry_date'))
                        <p class="help-block">
                            {{ $errors->first('entry_date') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('amount', trans('quickadmin.ledger-entry.fields.amount').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('amount',
                        $ledger_entry->ledger_currency->symbol  . number_format($ledger_entry->amount, 2, $ledger_entry->ledger_currency->money_format_decimal, $ledger_entry->ledger_currency->money_format_thousands),
                        [
                            'class' => 'form-control',
                            'id' => 'moneyFormat',
                            'placeholder' => '',
                            'required' => ''
                        ]
                    ) !!}
                    <p class="help-block"></p>
                    @if($errors->has('amount'))
                        <p class="help-block">
                            {{ $errors->first('amount') }}
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    <script>
        $('.date').datepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}"
        });
    </script>

@stop
