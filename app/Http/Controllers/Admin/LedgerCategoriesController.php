<?php

namespace App\Http\Controllers\Admin;

use App\LedgerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLedgerCategoriesRequest;
use App\Http\Requests\Admin\UpdateLedgerCategoriesRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

class LedgerCategoriesController extends Controller
{
    /**
     * Display a listing of LedgerCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('ledger_category_access')) {
            return abort(401);
        }
        if ($filterBy = Input::get('filter')) {
            if ($filterBy == 'all') {
                Session::put('LedgerCategory.filter', 'all');
            } elseif ($filterBy == 'my') {
                Session::put('LedgerCategory.filter', 'my');
            }
        }

        $ledger_categories = LedgerCategory::all();

        return view('admin.ledger_categories.index', compact('ledger_categories'));
    }

    /**
     * Show the form for creating new LedgerCategory.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('ledger_category_create')) {
            return abort(401);
        }

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        return view('admin.ledger_categories.create', compact('created_bies'));
    }

    /**
     * Store a newly created LedgerCategory in storage.
     *
     * @param  \App\Http\Requests\StoreLedgerCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLedgerCategoriesRequest $request)
    {
        if (! Gate::allows('ledger_category_create')) {
            return abort(401);
        }
        $ledger_category = LedgerCategory::create($request->all());



        return redirect()->route('admin.ledger_categories.index');
    }


    /**
     * Show the form for editing LedgerCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('ledger_category_edit')) {
            return abort(401);
        }

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        $ledger_category = LedgerCategory::findOrFail($id);

        return view('admin.ledger_categories.edit', compact('ledger_category', 'created_bies'));
    }

    /**
     * Update LedgerCategory in storage.
     *
     * @param  \App\Http\Requests\UpdateLedgerCategoriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLedgerCategoriesRequest $request, $id)
    {
        if (! Gate::allows('ledger_category_edit')) {
            return abort(401);
        }
        $ledger_category = LedgerCategory::findOrFail($id);
        $ledger_category->update($request->all());



        return redirect()->route('admin.ledger_categories.index');
    }


    /**
     * Display LedgerCategory.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('ledger_category_view')) {
            return abort(401);
        }

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $ledger_entries = \App\LedgerEntry::where('ledger_category_id', $id)->get();

        $ledger_category = LedgerCategory::findOrFail($id);

        return view('admin.ledger_categories.show', compact('ledger_category', 'ledger_entries'));
    }


    /**
     * Remove LedgerCategory from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('ledger_category_delete')) {
            return abort(401);
        }
        $ledger_category = LedgerCategory::findOrFail($id);
        $ledger_category->delete();

        return redirect()->route('admin.ledger_categories.index');
    }

    /**
     * Delete all selected LedgerCategory at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('ledger_category_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = LedgerCategory::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
