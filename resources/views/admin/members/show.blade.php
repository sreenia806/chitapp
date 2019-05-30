@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.members.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.members.fields.first_name')</th>
                            <td field-key='title'>{{ $member->first_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.members.fields.last_name')</th>
                            <td field-key='symbol'>{{ $member->last_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.members.fields.idproof')</th>
                            <td field-key='symbol'>{{ $member->idproof }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.members.fields.mobile')</th>
                            <td field-key='money_format'>{{ $member->mobile }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.members.fields.address')</th>
                            <td field-key='money_format'>{{ $member->address }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.members.fields.reference')</th>
                            <td field-key='money_format'>{{ $member->reference }}</td>
                        </tr>
                    </table>
                </div>
            </div>

			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">

			<li role="presentation" class="active"><a href="#subscriptions" aria-controls="subscriptions" role="tab" data-toggle="tab">Subscriptions</a></li>
			<li role="presentation" ><a href="#installments" aria-controls="installments" role="tab" data-toggle="tab">Installments</a></li>
			<li role="presentation" ><a href="#payments" aria-controls="payments" role="tab" data-toggle="tab">Payments</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">

				<div role="tabpanel" class="tab-pane active" id="subscriptions">
					<table id="tblSubscriptions" class="table table-bordered table-striped ">
						<thead>
							<tr>
								<th>Chit Scheme</th>
								<th>Ticket</th>
								<th>Installment</th>
								<th>Bid Awarded</th>
								<th>Start Date</th>
								<th>Chit Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="7">@lang('quickadmin.qa_no_entries_in_table')</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div role="tabpanel" class="tab-pane " id="installments">
					<table id="tblInstallments" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>Chit Scheme</th>
								<th>Ticket</th>
								<th>Auction</th>
								<th>Paid Amount</th>
								<th>Due Amount</th>
								<th>Due Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="7">@lang('quickadmin.qa_no_entries_in_table')</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div role="tabpanel" class="tab-pane " id="payments">
					<table id="tblPayments" class="table table-bordered table-striped ">
						<thead>
							<tr>
								<th>Description</th>
								<th>Paid Amount</th>
								<th>Entry Date</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="7">@lang('quickadmin.qa_no_entries_in_table')</td>
							</tr>
						</tbody>
					</table>
				</div>


			</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.members.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop
        @include('partials.addmemberpayment')
@section('javascript')
    <script>
        var tblInstallments; // global

        $(document).ready(function() {
            getSubscriptions();
			getInstallments();
			getPayments();


            $('#tblInstallments tbody').on( 'click', 'button', function () {

                var action = this.className;
                var data = tblInstallments.row( $(this).closest('tr') ).data();
                var paid_amount = data.paid_amount ? data.paid_amount : 0;
                var due_amount = data.due_amount * 1; // make sure it is numerical

                $('[name=scheme_id]', $('#addMemberPayment')).val(data.scheme_id);
                $('#spnSubscription', $('#addMemberPayment')).html(data.title + ' - [Ticket: ' + data.ticket + '][auction: ' + data.number + ']');
                $('#spnInstallment', $('#addMemberPayment')).html('₹ ' + paid_amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')  + '/' + '₹ ' + due_amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('[name=subscriber_id]', $('#addMemberPayment')).val(data.subscriber_id);
                $('[name=installment_id]', $('#addMemberPayment')).val(data.id);
                $('[name=due_amount]', $('#addMemberPayment')).val(due_amount - paid_amount);
                //$('#addBookDialog').modal('show');
            } );
        });

        function getSubscriptions()
        {
            var filter_country = '';
            var filter_name = '';

            tblLedger = $('#tblSubscriptions').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : false,
                "ajax" : {
                    url:"{{ route('members.subscriptions', [$member->id]) }}",
                    type:"POST",
                    data:{

                    }
                },
                "columns": [
                    { "data": "title" },
                    { "data": "ticket"},
                    { "data": "installment", className: "text-right dt-nowrap" },
                    { "data": "bid_amount", className: "text-right dt-nowrap" },
                    { "data": "start_date" },
                    { "data": "status" }
                ]
            });
        }


        function getInstallments()
        {
            var filter_country = '';
            var filter_name = '';

            tblInstallments = $('#tblInstallments').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : false,
                "ajax" : {
                    url:"{{ route('members.installments', [$member->id]) }}",
                    type:"POST",
                    data:{

                    }
                },
                "columns": [
                    { "data": "title" },
                    { "data": "ticket"},
                    { "data": "number"},
                    { "data": "paid_amount", className: "text-right dt-nowrap"},
                    { "data": "due_amount", className: "text-right dt-nowrap"},
                    { "data": "due_date"},
                    { "data": "status"}
                ]
            });


        }


        function getPayments()
        {
            var filter_country = '';
            var filter_name = '';

            tblPayments = $('#tblPayments').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : false,
                "ajax" : {
                    url:"{{ route('members.payments', [$member->id]) }}",
                    type:"POST",
                    data:{

                    }
                },
                "columns": [
                    { "data": "description" },
                    { "data": "amount", className: "text-right dt-nowrap text-success"},
                    { "data": "entry_date"}
                ]
            });
        }
    </script>
@endsection
