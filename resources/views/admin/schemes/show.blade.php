@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.schemes.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.schemes.fields.title')</th>
                            <td field-key='title'>{{ $scheme->title }}</td>
                            <th >@lang('quickadmin.schemes.fields.status') </th>
                            <td >
                                @if ($scheme->start_date != '' && $scheme->status == 'pending')
                                    @can('scheme_edit')
                                        {!! Form::open(array(
                                            'style' => 'display: inline-block;',
                                            'method' => 'PUT',
                                            'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                            'route' => ['admin.schemeStart', $scheme->id])) !!}
                                        {!! Form::submit(trans('quickadmin.qa_schemeStart'), array('class' => 'btn btn-md')) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                @else
                                    {{ $scheme->status or '' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.schemes.fields.duration')</th>
                            <td field-key='symbol'>{{ $scheme->duration }}</td>
                            <th>@lang('quickadmin.schemes.fields.subscribers')</th>
                            <td field-key='money_format'>{{ $scheme->subscribers }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.schemes.fields.installment')</th>
                            <td field-key='money_format' class="text-right">₹ {{ number_format($scheme->installment, 2) }}</td>
                            <th >Total Collected </th>
                            <td class="text-right">₹  {{number_format($scheme_details[0]['payment'], 2)}}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.schemes.fields.value')</th>
                            <td field-key='money_format' class="text-right">₹ {{ number_format($scheme->value, 2) }}</td>
                            <th >Distributed + Expenses </th>
                            <td class="text-right">₹  {{number_format($scheme_details[0]['expense'], 2)}}</td>
                        </tr>

                        <tr>
                            <th>@lang('quickadmin.schemes.fields.start_date')</th>
                            <td field-key='created_by'>{{ $scheme->start_date or '' }}</td>
                            <th >Profit/Loss </th>
                            <td class="text-right">₹  {{number_format($scheme_details[0]['payment'] - $scheme_details[0]['expense'], 2)}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="{{ Session::get('scheme_tab') == 'auctions' ? 'active' : ''}}"><a href="#auctions" aria-controls="auctions" role="tab" data-toggle="tab">Auctions</a></li>
                <li role="presentation" class="{{ Session::get('scheme_tab') == 'subscribers' ? 'active' : ''}}"><a href="#subscribers" aria-controls="subscribers" role="tab" data-toggle="tab">Members</a></li>
                <li role="presentation" class="{{ Session::get('scheme_tab') == 'installments' ? 'active' : ''}}"><a href="#installments" aria-controls="installments" role="tab" data-toggle="tab">Installments</a></li>
                <li role="presentation" class="{{ Session::get('scheme_tab') == 'ledger' ? 'active' : ''}}"><a href="#ledger" aria-controls="ledger" role="tab" data-toggle="tab">Ledger</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">

                <div role="tabpanel" class="tab-pane active" id="auctions">
                    {{ Form::open(['method' => 'POST', 'route' => ['admin.auction.save', $scheme->id], 'name' => 'frmSchemeAuction']) }}
                    <table class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th style="width:50px">@lang('quickadmin.auctions.fields.number')</th>
                            <th style="width:120px">@lang('quickadmin.auctions.fields.auction_date')</th>
                            <th style="width:120px">@lang('quickadmin.auctions.fields.bid_amount')</th>
                            <th style="width:150px">@lang('quickadmin.auctions.fields.remaining_amount')</th>
                            <th style="width:150px">@lang('quickadmin.auctions.fields.installment_total')</th>
                            <th style="width:150px">@lang('quickadmin.auctions.fields.collected_total')</th>
                            <th>@lang('quickadmin.auctions.fields.subscriber_id')</th>
                            <th> </th>
                        </tr>
                        </thead>

                        <tbody>
                        @if (count($auctions) > 0)
                            @foreach ($auctions as $auction)
                                <tr data-entry-id="{{ $auction->id }}" auction_id="{{ $auction->id }}" auction_date="{{ $auction->auction_date }}" bid_amount="{{ $auction->bid_amount }}">
                                    <td field-key='number'>{{ $auction->number }}</td>
                                    <td field-key='auction_date'>
                                        @if ($auction->status != 'awarded')
                                        <input name="auction_date[{{ $auction->id }}]" value="{{ $auction->auction_date }}" class="form-control date"/>
                                        @else
                                            {{ $auction->auction_date }}
                                        @endif
                                    </td>
                                    <td field-key='bid_amount'>
                                        @if ($auction->status != 'awarded')
                                            <input name="bid_amount[{{ $auction->id }}]" value="₹{{ number_format($auction->bid_amount, 2) }}" class="form-control moneyFormat text-right" />
                                        @else
                                            ₹{{ number_format($auction->bid_amount, 2) }}
                                        @endif
                                    </td>
                                    <td field-key='remaining_amount'>
                                        <input name="remaining_amount[{{ $auction->id }}]" value="₹{{ number_format($auction->remaining_amount, 2) }}" readonly class="form-control moneyFormat  text-right"/>
                                    </td>
                                    <td field-key='installment_total'>
                                        <input name="installment_total[{{ $auction->id }}]" value="₹{{number_format($auction->number * $scheme->value, 2)}}" readonly class="form-control moneyFormat  text-right" />
                                    </td>
                                    <td field-key='collected_total'>
                                        <input name="collected_total[{{ $auction->id }}]" value="₹{{ isset($arrAuctionCollected[$auction->id]) ? number_format($arrAuctionCollected[$auction->id], 2) : 0.00 }}" readonly class="form-control moneyFormat  text-right" />
                                    </td>
                                    <td field-key='subscriber'>
                                        @if ($auction->status != 'awarded')
                                        {{Form::select('subscriber[' . $auction->id . ']',
                                             $remaining_subscribers,
                                             $auction->subscriber_id,
                                              ['class' => 'form-control select2 full-width'])}}
                                        @else
                                            @can('member_view')
                                                <a href="{{ route('admin.members.show',[$auction->subscriber->member->id]) }}">{{$auction->subscriber->member->name}}</a>
                                            @else
                                                {{$auction->subscriber->member->name}}
                                            @endcan
                                        @endif
                                    </td>
                                    <td field-key='subscriber'>
                                        @if ($auction->showbutton)
                                            @if ($auction->status == 'projected')
                                            <a href="{{ route('admin.auction.generate', [$scheme->id, $auction->id]) }}"
                                               class="btn btn-sm btn-primary"
                                               onClick="return Confirm('Are you sure want to generate the installments');"
                                            >Generate</a>
                                            <a href="{{ route('admin.auction.skip', [$scheme->id, $auction->id]) }}"
                                               class="btn btn-sm btn-info"
                                               onClick="return Confirm('Are you sure want to skip the installments');"
                                            >Skip</a>
                                            @elseif ($auction->status == 'active')
                                                <button type="button" onClick="confirmBidWon(this)" class="btn btn-sm btn-success" >Bid Award</button>
                                            @endif
                                        @elseif ($auction->status == 'awarded')
                                            Awarded
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">@lang('quickadmin.qa_no_entries_in_table')</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>


                <div role="tabpanel" class="tab-pane " id="subscribers">
                    {{ Form::open(['method' => 'POST', 'route' => ['admin.subscribe', $scheme->id], 'name' => 'frmSchemeSubscriber']) }}
                    <table class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>@lang('quickadmin.subscribers.fields.ticket')</th>
                            <th>@lang('quickadmin.subscribers.fields.name')</th>
                            <th>@lang('quickadmin.subscribers.fields.confirmed_date')</th>
                            <th>@lang('quickadmin.subscribers.fields.status')</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if (count($subscribers) > 0)
                            @foreach ($subscribers as $subscriber)
                                <tr data-entry-id="{{ $subscriber->id }}">
                                    <td field-key='ticket'>{{ $subscriber->ticket }}</td>
                                    <td field-key='name'>
                                        @if ($scheme->status == 'pending' || empty($subscriber->member_id))
                                        {{Form::select('subscribers[' . $subscriber->id . ']',
                                             $members,
                                             $subscriber->member_id,
                                              ['class' => 'form-control select2 full-width'])}}
                                        @else
                                            @can('member_view')
                                                <a href="{{ route('admin.members.show',[$subscriber->member->id]) }}">{{$subscriber->member->name}}</a>
                                            @else
                                                {{$subscriber->member->name}}
                                            @endcan
                                        @endif
                                    </td>
                                    <td field-key='confirmed_date'>{{ $subscriber->confirmed_date }}</td>
                                    <td field-key='status'>{{ $subscriber->status }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">@lang('quickadmin.qa_no_entries_in_table')</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>


                <div role="tabpanel" class="tab-pane " id="installments">
                    <table class="table table-bordered table-striped " id="tableInstallments">
                        <thead>
                        <tr>
                            <th>@lang('quickadmin.installments.fields.number')</th>
                            <th>@lang('quickadmin.installments.fields.ticket')</th>
                            <th>@lang('quickadmin.installments.fields.subscriber')</th>
                            <th>@lang('quickadmin.installments.fields.due_amount')</th>
                            <th>@lang('quickadmin.installments.fields.due_date')</th>
                            <th>@lang('quickadmin.installments.fields.paid_date')</th>
                            <th>@lang('quickadmin.installments.fields.status')</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>

				<div role="tabpanel" class="tab-pane" id="ledger">

					<div class="panel-heading text-right">
						<!-- Trigger the modal with a button -->
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addPayment">Payment</button>
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#chitExpense">Add Expenses</button>
					</div>
					<div class="panel-body table-responsive">
					    <table class="table table-bordered table-striped " id="tblPayments">
						<thead>
							<tr>
								<th>Entry Date</th>
								<th>Description</th>
								<th>Payment</th>
								<th>Expenses</th>
							</tr>
						</thead>

						</table>
					</div>

				</div>

            </div>



                <p>&nbsp;</p>

            <a href="{{ route('admin.schemes.index') }}" class="btn btn-md btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>


    {!! Form::open([
        'style' => 'display: inline-block;',
        'method' => 'PUT',
        'route' => ['admin.auction.bidwon', $scheme->id],
        'name' => 'frmBidWon'
        ]) !!}
    {!! Form::hidden('amount') !!}
    {!! Form::hidden('subscriber_id') !!}
    {!! Form::hidden('auction_id') !!}
    {!! Form::close() !!}
@stop


@include('partials.addchitexpense')
@include('partials.addchitpayment')



@section('javascript')
    @parent
    <script>
	var tblInstallments;
	var tblLedger;


        $('.date').datepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}"
        });


        function confirmBidWon(thisObj) {
            if (confirm('Are you sure want to proceed?')) {

                var $frm = $('[name=frmBidWon]');
                var $thisTR = $(thisObj).closest('tr');

                if ($('input[name^=bid_amount]', $thisTR).maskMoney('unmasked')[0] <= 0) {
                    alert('Please enter bid won amount.');
                    $('input[name^=bid_amount]', $thisTR).focus();
                    return false;
                }
                if ($('[name^=subscriber]', $thisTR).val() == '0') {
                    alert('Select the bid winner');
                    $('[name^=subscriber]', $thisTR).focus();
                    return false;
                }


                $('[name=auction_id]', $frm).val($thisTR.attr('auction_id'));
                $('[name=subscriber_id]', $frm).val($('[name^=subscriber]', $thisTR).val());
                $('[name=amount]', $frm).val($('input[name^=bid_amount]', $thisTR).maskMoney('unmasked')[0]);
                $frm.submit();
            }
        }

        $(document).ready(function() {

            resetInstallmentTotal();
            resetRemainingAmount();

            $('input[name^=auction_date]').change(function() {
                resetInstallmentTotal();
                resetRemainingAmount();
            });

            $('input[name^=bid_amount]').change(function() {
                resetRemainingAmount();
            });

			get_installments();
			get_chitledger();
        });

        function resetInstallmentTotal()
        {
            $('[name^=installment_total]').each(function () {
                var scheme_date = new Date('{{$scheme->start_date}}');
                var auction_date = ($('[name^=auction_date]', $(this).closest('tr')).length) ?
                    $('[name^=auction_date]', $(this).closest('tr')).val() :
                    $(this).closest('tr').attr('auction_date');
                auction_date = new Date(auction_date);

                var m = diff_months(auction_date, scheme_date);

                var installment_total = (m + 1) * '{{ $scheme->subscribers or 0 }}' * '{{ $scheme->installment or 0 }}';

                $(this).val(installment_total.toFixed(2));
                $(this).maskMoney('mask');
            });
        }


        function resetRemainingAmount()
        {
            var prev_bid_total = 0;// initial value, with in function scope
            $('[name^=remaining_amount]').each(function () {

                var installment_total = $('[name^=installment_total]', $(this).closest('tr')).maskMoney('unmasked')[0];
                var bid_amount = ($('[name^=bid_amount]', $(this).closest('tr')).length)    ?
                    $('[name^=bid_amount]', $(this).closest('tr')).maskMoney('unmasked')[0] :
                    $(this).closest('tr').attr('bid_amount');

                // update prev_bid_total for next iteration
                prev_bid_total = prev_bid_total * 1 + bid_amount * 1;

                var remaining_amount = installment_total - prev_bid_total;

                $(this).val(remaining_amount.toFixed(2));
                $(this).maskMoney('mask');
            });
        }


        function get_installments()
        {
            var scheme_id = '{{$scheme->id}}';
            var filter_country = '';

            tblInstallments = $('#tableInstallments').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : false,
                "ajax" : {
                    url:"{{ route('installments.ajax_load_data_by_scheme') }}",
                    type:"POST",
                    data:{
                        scheme_id: scheme_id, filter_country:filter_country
                    }
                },
                "columns": [
                    { "data": "number" },
                    { "data": "ticket" },
                    { "data": "name" },
                    { "data": "due_amount", className: "text-right dt-nowrap" },
                    { "data": "due_date" },
                    { "data": "paid_date" },
                    { "data": "status" }
                ]
            });
        }


        function get_chitledger()
        {
            var scheme_id = '{{$scheme->id}}';
            var filter_country = '';

            tblLedger = $('#tblPayments').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : false,
                "ajax" : {
                    url:"{{ route('schemes.ledger', [$scheme->id]) }}",
                    type:"POST",
                    data:{
                        scheme_id: scheme_id, filter_country:filter_country
                    }
                },
                "columns": [
                    { "data": "entry_date" },
                    { "data": "description"},
                    { "data": "payment" },
                    { "data": "expense" }
                ]
            });
        }



    </script>

@stop
