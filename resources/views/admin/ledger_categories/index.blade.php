@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.ledger-category.title')</h3>
    @can('ledger_category_create')
    <p>
        <a href="{{ route('admin.ledger_categories.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>
    @endcan



    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($ledger_categories) > 0 ? 'datatable' : '' }} ">
                <thead>
                    <tr>
                        <th>@lang('quickadmin.ledger-category.fields.name')</th>
                        <th>@lang('quickadmin.ledger-category.fields.ledger_type')</th>
                        <th>&nbsp;</th>

                    </tr>
                </thead>

                <tbody>
                    @if (count($ledger_categories) > 0)
                        @foreach ($ledger_categories as $ledger_category)
                            <tr data-entry-id="{{ $ledger_category->id }}">
                                <td field-key='name'>{{ $ledger_category->name }}</td>
                                <td field-key='name'>{{ $ledger_category->ledger_type }}</td>
                                <td>
                                    @if (!in_array($ledger_category->id, [
                                            config('app.chit_ledger_codes.INSTALLMENT'),
                                            config('app.chit_ledger_codes.PAYOUT'),
                                            config('app.chit_ledger_codes.COMMISSION')
                                            ]))
                                        @can('ledger_category_view')
                                        <a href="{{ route('admin.ledger_categories.show',[$ledger_category->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                        @endcan
                                        @can('ledger_category_edit')
                                        <a href="{{ route('admin.ledger_categories.edit',[$ledger_category->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                        @endcan
                                        @can('ledger_category_delete')
    {!! Form::open(array(
                                            'style' => 'display: inline-block;',
                                            'method' => 'DELETE',
                                            'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                            'route' => ['admin.ledger_categories.destroy', $ledger_category->id])) !!}
                                        {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                        @endcan
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
        </div>
    </div>
@stop

@section('javascript')
    <script>

    </script>
@endsection
