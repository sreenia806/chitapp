@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.ledger-category.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.ledger_categories.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', trans('quickadmin.ledger-category.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('ledger_type', trans('quickadmin.ledger-category.fields.ledger_type').'*', ['class' => 'control-label']) !!}
                    
                    {{Form::select('ledger_type',
                         ['CR' => 'Income', 'DR' => 'Expense'],
                         old('ledger_type'),
                          ['class' => 'form-control select2 full-width'])}}
                    <p class="help-block"></p>
                    @if($errors->has('ledger_type'))
                        <p class="help-block">
                            {{ $errors->first('ledger_type') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

