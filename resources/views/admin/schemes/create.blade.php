@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.schemes.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.schemes.store'], 'name' => 'frmSchemeCreate']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('title', trans('quickadmin.schemes.fields.title').'', ['class' => 'control-label']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Chit Scheme Title']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('start_date', trans('quickadmin.schemes.fields.start_date').'', ['class' => 'control-label']) !!}
                    {!! Form::text('start_date', old('start_date'), ['class' => 'form-control date', 'placeholder' => 'Chit Start Date']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('start_date'))
                        <p class="help-block">
                            {{ $errors->first('start_date') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('duration', trans('quickadmin.schemes.fields.duration').'', ['class' => 'control-label']) !!}
                    {!! Form::number('duration', old('duration'), ['class' => 'form-control', 'placeholder' => 'No# of Months']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('duration'))
                        <p class="help-block">
                            {{ $errors->first('duration') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('subscribers', trans('quickadmin.schemes.fields.subscribers').'', ['class' => 'control-label']) !!}
                    {!! Form::text('subscribers', old('subscribers'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('subscribers'))
                        <p class="help-block">
                            {{ $errors->first('subscribers') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('installment', trans('quickadmin.schemes.fields.installment').'', ['class' => 'control-label']) !!}
                    {!! Form::text('installment', '₹' . number_format(old('installment'), 2), ['class' => 'form-control moneyFormat text-right', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('value'))
                        <p class="help-block">
                            {{ $errors->first('installment') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('value', trans('quickadmin.schemes.fields.value').'', ['class' => 'control-label']) !!}
                    {!! Form::text('value', '₹' . number_format(old('value'), 2), ['class' => 'form-control moneyFormat text-right', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('value'))
                        <p class="help-block">
                            {{ $errors->first('value') }}
                        </p>
                    @endif
                </div>
            </div>



        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    <script>
        $('.date').datepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}"
        });

        $('[name=duration]').change(function() {
            $('[name=subscribers]').val($(this).val());
        });
    </script>

@stop
