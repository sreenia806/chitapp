@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.schemes.title')</h3>
    @can('scheme_create')
    <p>
        <a href="{{ route('admin.schemes.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>
    @endcan

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table id="tblSchemes" class="table table-bordered table-striped ">
                <thead>
                    <tr>

                        <th>@lang('quickadmin.schemes.fields.title')</th>
                        <th width="30">@lang('quickadmin.schemes.fields.duration')</th>
                        <th>@lang('quickadmin.schemes.fields.value')</th>
                        <th>@lang('quickadmin.schemes.fields.status')</th>

                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript')
    <script>

        $(document).ready(function() {
            get_schemes();
        });

        function get_schemes()
        {
            var filter_country = '';
            var filter_name = '';

            tblLedger = $('#tblSchemes').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : true,
                "ajax" : {
                    url:"{{ route('load_schemes') }}",
                    type:"POST",
                    data:{
                        filter_name: filter_name,
						filter_country: filter_country,
						_token: '{{csrf_token()}}'
						@can('scheme_view')
						, gate_allow_view: '1'
						@endcan
						@can('scheme_edit')
						, gate_allow_edit: '1'
						@endcan
                    }
                },
                "columns": [
                    { "data": "title" },
                    { "data": "duration"},
                    { "data": "value" },
                    { "data": "status" },
                    { "data": "actions" }
                ]
            });
        }



    </script>
@endsection

