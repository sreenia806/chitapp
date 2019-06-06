<!-- Modal -->
<div id="chitExpense" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Chit Expense</h4>
      </div>
      <div class="modal-body">

          <div class="row">
              <div class="col-xs-12 form-group">
                  <label for="ledger_category" class="control-label">Income / Expense</label>
                  <span>
                      <label class="radio-inline"><input type="radio" name="ledger_category" value="{{ config('app.chit_ledger_codes.INCOME') }}" >Income</label>
                      <label class="radio-inline"><input type="radio" name="ledger_category" value="{{ config('app.chit_ledger_codes.COMMISSION') }}" checked>Expense</label>
						<p class="help-block"></p>
                    </span>
              </div>
          </div>
          <div class="row">
              <div class="col-xs-12 form-group">
                  <label for="ledger_description" class="control-label">Description</label>
                  <span>
						<input class="form-control" placeholder="" required="" name="ledger_description" type="text">
						<p class="help-block"></p>
                    </span>
              </div>
          </div>

            <div class="row">
                <div class="col-xs-12 form-group">
                    <label for="ledger_amount" class="control-label">Amount</label>
                    <span>
						<input class="form-control moneyFormat" placeholder="" required="" name="ledger_amount" type="text">
						<p class="help-block"></p>
                    </span>
                </div>
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" id="btnExpense" class="btn btn-primary" >Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



@section('javascript')
    @parent
    <script>
		$('#btnExpense').click(function() {

			if ($('[name=ledger_description]').val() == '') {
				$('.help-block', $('[name=ledger_description]').closest('span')).text('Description requried');
				return;
			}

			var amount = $('[name=ledger_amount]').maskMoney('unmasked')[0];

			if (amount <= 0) {
				$('.help-block', $('[name=ledger_amount]').closest('span')).text('Please enter valid amount');
				return;
			}

			    $.ajax({
                    url: "{{ route('admin.add_scheme_expense') }}",
                    method: 'POST',
					data: {
						category:$('[name=ledger_category]:checked').val(),
						description:$('[name=ledger_description]').val(),
						amount: amount,
						scheme_id: '{{$scheme->id}}',
						_token: '{{csrf_token()}}'
					},
                    success: function(data) {
                        if (data.status == 1) {
							$('[name=ledger_description]').val('');
							$('[name=ledger_amount]').val(0.00);
                            $('#chitExpense').modal('hide');
                        } else {
                            alert('error occured');
                        }

						tblLedger.ajax.reload( null, false ); // user paging is not reset on reload

                    }
                });

		});
	</script>
@stop
