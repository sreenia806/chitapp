<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMembersRequest;
use App\Http\Requests\Admin\UpdateMembersRequest;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('member_access')) {
            return abort(401);
        }

        return view('admin.members.index');
    }


    public function loadData(Request $request)
    {
        $page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        $member_query = Member::query();

        $member_query = $member_query->whereRaw('deleted_at IS NULL');

        $totalRows = $member_query->count();

        // More Filters
        $search = $request->input('search');
        if (isset($search['value'])) {
            $member_query = $member_query->where('first_name', $search['value'])
                ->orWhere('last_name', $search['value'])
                ->orWhere('first_name', 'like', '%' . $search['value'] . '%')
                ->orWhere('last_name', 'like', '%' . $search['value'] . '%');
        }
        $filteredRows = $member_query->count();


        // Order By
        $order = $request->input('order');
        if (isset($order[0]['column'])) {
            $columns = $request->input('columns');
            $sortColumn = $columns[$order[0]['column']]['data'];
            if ($sortColumn) {
                $member_query = $member_query->orderBy($sortColumn, $order[0]['dir']);
            }
        }

        $members = $member_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();

        foreach ($members as &$member) {
            $member['actions'] = '';
            if ($request->input('gate_allow_view', '') == 1) {
                $member['actions'] .= '<a href="' . route('admin.members.show', [$member['id']]) . '" class="btn btn-xs btn-primary">' . trans('quickadmin.qa_view') . '</a>';
            }
            if ($request->input('gate_allow_edit', '') == 1) {
                $member['actions'] .= ' <a href="' . route('admin.members.edit', [$member['id']]) . '" class="btn btn-xs btn-info">' . trans('quickadmin.qa_edit') . '</a>';
            }
        }

        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $filteredRows,
            'data' => $members
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('member_create')) {
            return abort(401);
        }

        return view('admin.members.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMembersRequest $request)
    {

        if (! Gate::allows('member_create')) {
            return abort(401);
        }
        $user = Member::create($request->all());



        return redirect()->route('admin.members.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
		if (! Gate::allows('member_view')) {
            return abort(401);
        }

		// $incomes = \App\Income::where('created_by_id', $id)->get();
		// $expenses = \App\Expense::where('created_by_id', $id)->get();
		$chitschemes = [];

        $member = Member::findOrFail($id);

        return view('admin.members.show', compact('member', 'chitschemes')); //, 'expense_categories', 'income_categories', 'currencies', 'incomes', 'expenses'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if (! Gate::allows('member_edit')) {
            return abort(401);
        }

        $member = Member::findOrFail($id);

        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if (! Gate::allows('member_edit')) {
            return abort(401);
        }
        $member = Member::findOrFail($id);
        $member->update($request->all());



        return redirect()->route('admin.members.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
