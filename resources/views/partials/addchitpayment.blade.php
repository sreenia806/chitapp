<!-- Modal -->
<div id="addPayment" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Chit Payment</h4>
      </div>
      <div class="modal-body">

            <div class="row">
                <div class="col-xs-12 form-group">
                    <label for="ledger_description" class="control-label">Subscribers</label>
                    <span>
						{!! Form::select('addpayment_subscriber_id', $select_subscribers, '', ['class' => 'form-control select2', 'required' => '', 'id' => 'addpayment_subscriber_id']) !!}
						<p class="help-block"></p>
                    </span>
                </div>
            </div>

              <div class="row">
                  <div class="col-xs-12 form-group">
                      {!! Form::label('addpayment_installment_id', 'Installment', ['class' => 'control-label']) !!}
                      <span>
                        {!! Form::select('addpayment_installment_id', ['' => 'Please Select'], old('installment_id'), ['class' => 'form-control select2', 'required' => '', 'id' => 'addpayment_installment_id']) !!}
                        <p class="help-block"></p>
                      </span>
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

        $("#addpayment_subscriber_id").change(function() {
            if ($(this).val()) {
                $.ajax({
                    url: "{{ route('admin.installments.get_by_subscriber') }}?subscriber_id=" + $(this).val(),
                    method: 'GET',
                    success: function(data) {
                        $('#addpayment_installment_id').html(data.html);
                        $('#addpayment_installment_id').change();
                    }
                });
            } else {
                $('#addpayment_installment_id').html('<option value="">Please select</option>');
            }
        });

        $("#addpayment_installment_id").change(function() {
            if ($(this).val()) {
                var effective_due = $('option:selected', this).attr('still_due');
                $('.help-block', $(this).closest('div')).html('Effective Due - ' + effective_due + '/-');
            } else {
                $('.help-block', $(this).closest('div')).html('Effective Due - 0/-' );
            }
        });

		$('#btnAddChitPayment').click(function() {
			$('.help-block', $('[name=addpayment_paid_amount]').closest('div')).text(''); // clear

			var addpayment_paid_amount = $('[name=addpayment_paid_amount]').maskMoney('unmasked')[0];
            var effective_due = $('option:selected', $('[name=addpayment_installment_id]')).attr('still_due');

			if (addpayment_paid_amount <= 0 || addpayment_paid_amount > effective_due) {
				$('.help-block', $('[name=addpayment_paid_amount]').closest('div')).text('Please enter valid amount and right amount');
				return;
			}

			    $.ajax({
                    url: "{{ route('admin.payinstallment') }}",
                    method: 'POST',
					data: {
						installment_id: $('[name=addpayment_installment_id]').val(),
                        subscriber_id: $('[name=addpayment_subscriber_id]').val(),
						amount: addpayment_paid_amount,
						scheme_id: '{{$scheme->id}}',
						_token: '{{csrf_token()}}'
					},
                    success: function(data) {
                        if (data.status == 1) {
							$('[name=addpayment_subscriber_id]').val('').change();
							$('[name=addpayment_paid_amount]').val(0.00);
                            $('#addPayment').modal('hide');
                        } else {
                            alert('error occured');
                        }

						tblLedger.ajax.reload( null, false ); // user paging is not reset on reload
                    },
					error: function(data) {
						alert(data.message);
					}
                });

		});
	});


	</script>
@stop
