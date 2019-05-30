@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.members.title')</h3>
    @can('member_create')
    <p>
        <a href="{{ route('admin.members.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>
    @endcan

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table id="tblMembers" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th width="15%">@lang('quickadmin.members.fields.first_name')</th>
                        <th width="10%">@lang('quickadmin.members.fields.last_name')</th>
                        <th width="10%">@lang('quickadmin.members.fields.idproof')</th>
                        <th width="10%">@lang('quickadmin.members.fields.mobile')</th>
                        <th width="25%">@lang('quickadmin.members.fields.address')</th>
                        <th width="15%">@lang('quickadmin.members.fields.reference')</th>
                        <th width="15%">Actions</th>
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
            get_Members();
        });

        function get_Members()
        {
            var filter_country = '';
            var filter_name = '';

            tblLedger = $('#tblMembers').DataTable({
                "processing" : true,
                "serverSide" : true,
                "order" : [],
                "searching" : true,
                "ajax" : {
                    url:"{{ route('load_members') }}",
                    type:"POST",
                    data:{
                        filter_name: filter_name, 
						filter_country: filter_country
						@can('member_view')
						, gate_allow_view: '1'
						@endcan
						@can('member_edit')
						, gate_allow_edit: '1'
						@endcan
                    }
                },
                "columns": [
                    { "data": "first_name" },
                    { "data": "last_name"},
                    { "data": "idproof" },
                    { "data": "mobile" },
                    { "data": "address" },
                    { "data": "reference" },
                    { "data": "actions" }
                ]
            });
        }



    </script>
@endsection
