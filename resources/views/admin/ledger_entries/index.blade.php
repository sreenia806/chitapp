@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.ledger-entry.title')</h3>
    @can('ledger_entry_create')
    <p>
        <a href="{{ route('admin.ledger_entries.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>

    </p>
    @endcan



    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
					  <label for="start_date" class="col-xs-4">Start date</label>
					  <div class="col-xs-8">
						<input type="text" class="form-control date" id="start_date" placeholder="Start Date" autocomplete="off" value="{{date('Y-m-01')}}"/>
					  </div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
					  <label for="end_date" class="col-xs-4">End Date</label>
					  <div class="col-xs-8">
						<input type="text" class="form-control date" id="end_date" placeholder="End Date" autocomplete="off" value="{{date('Y-m-t')}}"/>
					  </div>
					</div>
				</div>
				<div class="col-md-2">
					<input class="btn btn-primary" type="button" id="filter" value="Filter" >
				</div>
			</div>
            <table id="tblLedger" class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>@lang('quickadmin.ledger-entry.fields.entry-date')</th>
                        <th>@lang('quickadmin.ledger-entry.fields.ledger-category')</th>
                        <th>Description</th>
                        <th>Income</th>
                        <th>Expense</th>
                        <th>Ledger Balance</th>
                        <th>&nbsp;</th>

                    </tr>
                </thead>

                <tbody>
				        <tr>
                            <td colspan="9">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop


@section('javascript')
    <script>
		var tblLedger;

        $(document).ready(function() {
            getLedgerEntries();

			$('.date').datepicker({
				autoclose: true,
				dateFormat: "{{ config('app.date_format_js') }}"
			});

			$('#filter').click(function() {
				tblLedger.ajax.reload(null, true);
			});

        });

        function getLedgerEntries()
        {

            tblLedger = $('#tblLedger').DataTable({
                "processing" : true,
                "serverSide" : true,
				"ordering": false,
                "order" : [],
                "searching" : false,
                "ajax" : {
                    url:"{{ route('load_ledger') }}",
                    type:"POST",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
						d.end_date = $('#end_date').val();
						@can('ledger_entry_view')
						d.gate_allow_view = '1';
						@endcan
						@can('ledger_entry_edit')
						d.gate_allow_edit = '1';
						@endcan
						@can('ledger_entry_delete')
						d.gate_allow_delete = '1';
						@endcan
                    }
                },
                "columns": [
                    { "data": "entry_date" },
                    { "data": "name" },
                    { "data": "description" },
                    { "data": "payment", className: "text-right dt-nowrap text-success" },
                    { "data": "expense", className: "text-right dt-nowrap text-danger" },
                    { "data": "balance", className: "text-right dt-nowrap" },
                    { "data": "actions"}
                ]
            });
        }



    </script>
@endsection
