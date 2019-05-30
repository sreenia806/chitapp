<!-- Modal -->
<div id="addMemberPayment" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <input name="scheme_id" type="hidden" value="" />
    <input name="auction" type="hidden" value="" />
    <input name="subscriber_id" type="hidden" value="" />
    <input name="installment_id" type="hidden" value="" />
    <input name="due_amount" type="hidden" value="" />

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Chit Payment</h4>
      </div>
      <div class="modal-body">

            <div class="row">
                <div class="col-xs-12 form-group">
                    <label for="ledger_description" class="control-label">Subscription</label>
                    <span id="spnSubscription"></span>
                </div>
            </div>

              <div class="row">
                  <div class="col-xs-12 form-group">
                      {!! Form::label('addpayment_installment_id', 'Installment', ['class' => 'control-label']) !!}
                      <span id="spnInstallment"></span>
                  </div>
              </div>

              <div class="row">
                  <div class="col-xs-12 form-group">
                      {!! Form::label('addpayment_paid_amount', trans('quickadmin.ledger-entry.fields.amount').'*', ['class' => 'control-label']) !!}
                      {!! Form::text('addpayment_paid_amount', old('addpayment_paid_amount'), ['class' => 'form-control', 'id' => 'moneyFormat', 'placeholder' => '', 'required' => '']) !!}
                      <p class="help-block"></p>
                  </div>
              </div>

      </div>
      <div class="modal-footer">
        <button type="button" id="btnAddChitPayment" class="btn btn-primary" >Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


@section('javascript')

    @parent
    <script>

	$(document).ready(function() {

		$('#btnAddChitPayment').click(function() {
			$('.help-block', $('[name=addpayment_paid_amount]').closest('div')).text(''); // clear

			var addpayment_paid_amount = $('[name=addpayment_paid_amount]').maskMoney('unmasked')[0];
            var effective_due = $('[name=due_amount]', $('#addMemberPayment')).val();

			if (addpayment_paid_amount <= 0 || addpayment_paid_amount > effective_due) {
				$('.help-block', $('[name=addpayment_paid_amount]').closest('div')).text('Please enter valid amount and right amount');
				return;
			}

			    $.ajax({
                    url: "{{ route('admin.payinstallment') }}",
                    method: 'POST',
					data: {
						installment_id: $('[name=installment_id]', $('#addMemberPayment')).val(),
                        subscriber_id: $('[name=subscriber_id]', $('#addMemberPayment')).val(),
						amount: addpayment_paid_amount,
						scheme_id: $('[name=scheme_id]', $('#addMemberPayment')).val(),
						_token: '{{csrf_token()}}'
					},
                    success: function(data) {
                        if (data.status == 1) {
							$('[name=addpayment_subscriber_id]').val('').change();
							$('[name=addpayment_paid_amount]').val(0.00);
                            $('#addMemberPayment').modal('hide');
                        } else {
                            alert('error occured');
                        }

                        tblInstallments.ajax.reload( null, false ); // user paging is not reset on reload
                    },
					error: function(data) {
						alert(data.message);
					}
                });

		});
	});


	</script>
@stop
