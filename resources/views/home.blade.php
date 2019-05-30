@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('quickadmin.qa_dashboard')</div>

                <div class="panel-body">
                    @lang('quickadmin.qa_dashboard_text')
                </div>
            </div>
        </div>
    </div>


    <h3 class="page-title">New Schemes</h3>
	    <div class="panel panel-default">
        <div class="panel-heading">
            Latest 10
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Subscribers</th>
                        <th>Start Date</th>
                        <th>Value</th>
                        <th>Amount Collected</th>
                        <th>Amount Spent</th>
                        <th>Profit/Loss</th>
                        <th>Status</th>

                    </tr>
                </thead>

                <tbody>
                    @if (count($schemes) > 0)
                        @foreach ($schemes as $scheme)
                            <tr data-entry-id="{{ $scheme['id'] }}">
                                <td field-key='name'>
                                    @can('scheme_view')
                                        <a href="{{ route('admin.schemes.show',[$scheme['id']]) }}">{{ $scheme['title'] }}</a>
                                    @else
                                        {{ $scheme['title'] }}
                                    @endcan
                                </td>
                                <td field-key='name'>{{ $scheme['subscribers'] }}</td>
                                <td field-key='start_date'>{{ $scheme['start_date'] }}</td>
                                <td class="text-right">
									₹ {{ number_format($scheme['value']) }}
								</td>
								<td class="text-right">
									₹ {{ number_format($scheme['collected_total']) }}
								</td>
								<td class="text-right">
									₹ {{ number_format($scheme['expenses_total']) }}
								</td>
								<td class="text-right">
									₹ {{ number_format($scheme['collected_total'] - $scheme['expenses_total']) }}
								</td>
								<td >
									{{ $scheme['status'] }}
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
        </div>
    </div>

@endsection
