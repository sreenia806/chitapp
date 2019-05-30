@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.payments.title')</h3>
    @can('payment_create')
    <p>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>
    @endcan



    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped " id="tblPayments">
                <thead>
                    <tr>
                        <th>@lang('quickadmin.payments.fields.member')</th>
                        <th>@lang('quickadmin.payments.fields.chit')</th>
                        <th>@lang('quickadmin.payments.fields.ticket')</th>
                        <th>@lang('quickadmin.payments.fields.installment')</th>
                        <th>@lang('quickadmin.payments.fields.paid_amount')</th>
                        <th>@lang('quickadmin.payments.fields.paid_date')</th>
                        <th>&nbsp;</th>

                    </tr>
                </thead>

            </table>
        </div>
    </div>
@stop

@section('javascript')
    @parent
<script>
    fill_datatable();

    function fill_datatable()
    {
        var filter_gender = '';
        var filter_country = '';

        var dataTable = $('#tblPayments').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "searching" : false,
            "ajax" : {
                url:"{{ route('payments.ajax_load_data') }}",
                type:"POST",
                data:{
                    filter_gender:filter_gender, filter_country:filter_country
                }
            },
            "columns": [
                { "data": "name" },
                { "data": "title" },
                { "data": "ticket" },
                { "data": "number" },
                { "data": "amount" },
                { "data": "entry_date" }
            ]
        });
    }
</script>

@stop
