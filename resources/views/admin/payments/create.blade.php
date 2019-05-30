@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.payments.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.payments.store'], 'name' => 'frmPayment']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('member_id', trans('quickadmin.payments.fields.member').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('member_id', $members, old('member_id'), ['class' => 'form-control select2', 'required' => '', 'id' => 'member_id']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('member_id'))
                        <p class="help-block">
                            {{ $errors->first('member_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('subscriber_id', 'Subscriptions', ['class' => 'control-label']) !!}
                    <span>

                    {!! Form::select('subscriber_id', ['' => 'Please Select'], old('subscriber_id'), ['class' => 'form-control select2', 'required' => '', 'id' => 'subscriber_id']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('subscriber_id'))
                            <p class="help-block">
                            {{ $errors->first('subscriber_id') }}
                        </p>
                        @endif
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('installment_id', 'Installment', ['class' => 'control-label']) !!}
                    <span>
                    {!! Form::select('installment_id', ['' => 'Please Select'], old('installment_id'), ['class' => 'form-control select2', 'required' => '', 'id' => 'installment_id']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('installment_id'))
                    <p class="help-block">
                        {{ $errors->first('installment_id') }}
                    </p>
                    @endif
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('amount', trans('quickadmin.ledger-entry.fields.amount').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('amount', old('amount'), ['class' => 'form-control', 'id' => 'moneyFormat', 'placeholder' => '', 'required' => '']) !!}
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

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::button('Cancel', ['class' => 'btn ', 'id' => 'btnCancel']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    <script>
        $('.date').datepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}"
        });

        $('#btnCancel').click(function() {
            window.location.href = "{{ route('admin.payments.index') }}"
        });

        $("#member_id").change(function() {

            if ($(this).val()) {
                $.ajax({
                    url: "{{ route('admin.members.get_subscriptions') }}?member_id=" + $(this).val(),
                    method: 'GET',
                    success: function(data) {
                        $('#subscriber_id').html(data.html);
                        $("#subscriber_id").change();
                    }
                });
            } else {
                $('#subscriber_id').html('<option value="">Please select</option>');
            }

        });

        $("#subscriber_id").change(function() {
            if ($(this).val()) {
                $.ajax({
                    url: "{{ route('admin.installments.get_by_subscriber') }}?subscriber_id=" + $(this).val(),
                    method: 'GET',
                    success: function(data) {
                        $('#installment_id').html(data.html);
                        $('#installment_id').change();
                    }
                });
            } else {
                $('#installment_id').html('<option value="">Please select</option>');
            }
        });


        $("#installment_id").change(function() {
            if ($(this).val()) {
                var effective_due = $('option:selected', this).attr('still_due')
                $('.help-block', $(this).closest('div')).html('Effective Due - ' + effective_due + '/-');
            } else {
                $('.help-block', $(this).closest('div')).html('Effective Due - 0/-' );
            }
        });

    </script>

@stop
