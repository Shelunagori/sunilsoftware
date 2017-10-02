<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Payment Voucher');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Create Payment Voucher</span>
				</div>
				<div class="actions">
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($payment ) ?>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label>Voucher No :</label>&nbsp;&nbsp;
							<?= h('#'.str_pad($voucher_no, 4, '0', STR_PAD_LEFT)) ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Transaction Date <span class="required">*</span></label>
							<?php echo $this->Form->control('transaction_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy', 'label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','data-date-start-date'=>@$coreVariable[fyValidFrom],'data-date-end-date'=>@$coreVariable[fyValidTo],'value'=>date('d-m-Y'),'required'=>'required']); ?>
						</div>
					</div>
				</div>
				<div class="row">
						<div class="table-responsive">
							<table id="main_table" class="table table-condensed table-bordered" style="margin-bottom: 4px;" width="100%">
								<thead>
									<tr align="center">
										<td><label></td>
										<td><label>Particulars<label></td>
										<td><label>Debit<label></td>
										<td><label>Credit<label></td>
										<td width="10%"></td>
									</tr>
								</thead>
								<tbody id='main_tbody' class="tab">
									<tr class="tr1 main_tr" class="tab">
									
										<td width="10%">
											<?php 
											$option[]= ['value'=>'Cr','text'=>'Cr',];
											$option[]= ['value'=>'Dr','text'=>'Dr',];
											echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm','required'=>'required','value'=>'Cr','disabled'=>'disabled']); ?>
										</td>
										<td width="65%">
											<?php echo $this->Form->input('ledger_id', ['options'=>@$ledgerOptions,'label' => false,'class' => 'form-control input-sm ledger','required'=>'required']); ?>
											<div ="tr2 main_tr">
											<?php echo 'hello'; ?>
											</div>
											
										</td>
										<td width="10%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass','placeholder'=>'Debit','style'=>'display:none;']); ?>
										</td>
										<td width="10%">
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm calculation rightAligntextClass','required'=>'required','placeholder'=>'Credit']); ?>	
										</td>
										<td align="center"  width="5%">
											<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
										</td>
										
									</tr>
									<tr class="tr2 main_tr"  style="display:none;">
										<td></td>
										<td colspan="3">
											<table class="table" align="center" id="bank_table" style="width: 90%;background-color:#EEF0F1; ">
											<tr class="bnk_tr">
											<td><label class="control-label">Mode of Payment<span class="required" aria-required="true"></span></label>
												
											<?php echo $this->Form->radio(
												'payment_mode',
												[
													['value' => 'Cheque', 'text' => 'Cheque'],
													['value' => 'NEFT/RTGS', 'text' => 'NEFT/RTGS']
												]); ?>
										</td>
										
										<td><label class="control-label">Cheque No<span class="required" aria-required="true"></span></label><?php echo $this->Form->input('cheque_no', ['type'=>'text','label' => false,'class' => 'form-control input-sm','placeholder'=>'Cheque No']); ?></td>
										
										<td><label class="control-label">Cheque Date<span class="required" aria-required="true"></span></label><?php echo $this->Form->input('cheque_date', ['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy', 'label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','value'=>date('d-m-Y')]); ?> </td>
										</tr>
										</table>
										<td></td>
									</tr>
									<tr class="tr3 main_tr" class="tab" style="display:none; ">
									<td></td>
										<td colspan="3">
											<table class="table" align="center" id="party_table" style="text-align:center;width: 90%;background-color:#EEF0F1; ">
												<thead>
													<tr>
														<td>Ref. Type</td>
														<td>Ref. No.</td>
														<td>Dr/Cr</td>
														<td>Amount</td>
														
														<td></td>
													</tr>
												</thead>
												
												<tbody id="main_tbody1">
													<tr class="main_tr1">
														
														<td width="20%">
															<?php 
															$option_ref[]= ['value'=>'New Ref','text'=>'New Ref'];
															$option_ref[]= ['value'=>'Against','text'=>'Against'];
															$option_ref[]= ['value'=>'Advance','text'=>'Advance'];
															$option_ref[]= ['value'=>'On Account','text'=>'On Account'];
															echo $this->Form->input('type', ['options'=>$option_ref,'label' => false,'class' => 'form-control input-sm']); ?>
														</td>
														<td width="25%">
															<?php echo $this->Form->input('ref_name', ['type'=>'text','label' => false,'class' => 'form-control input-sm']); ?>
														</td>
														<td width="15%">
															<?php 
															echo $this->Form->input('type_cr_dr', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','value'=>'Dr']); ?>
														</td>
														<td width="20%">
															<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm debit_hide_show calculation rightAligntextClass','placeholder'=>'Debit']); ?>
														
															<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm credit_hide_show calculation rightAligntextClass','placeholder'=>'Credit','style'=>'display:none;']); ?>	
														</td>
														<td align="center"  width="5%">
															<a class="btn btn-danger delete-tr1 btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
														</td>
														
													</tr>
												</tbody>
												<tfoot>
													<tr>
														<td colspan="5" align="left">	
															<button type="button" class="add_row1 btn btn-primary input-sm"><i class="fa fa-plus"></i> </button>
														</td>
													</tr>
												</tfoot>
											</table>
										</td>
										<td></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2" >	
											<button type="button" class="add_row btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label>Narration </label>
								<?php echo $this->Form->control('narration',['class'=>'form-control input-sm ','label'=>false,'placeholder'=>'Narration','rows'=>'4']); ?>
							</div>
						</div>
					</div>
				<?= $this->Form->button(__('Submit'),['class'=>'btn btn-success submit'])  ?>
				<?= $this->Form->end() ?>
			</div>
		</div>
	</div>
</div>

<table id="sample_table1" style="display:none;" width="100%">
	<tbody>
		<tr class="main_tr1">
		
			<td width="20%">
				<?php 
				echo $this->Form->input('type', ['options'=>$option_ref,'label' => false,'class' => 'form-control input-sm','required'=>'required']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('ref_name', ['type'=>'text','label' => false,'class' => 'form-control input-sm']); ?>
			</td>
			<td width="15%">
				<?php 
				echo $this->Form->input('type_cr_dr', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','value'=>'Dr']); ?>
			</td>
			<td width="20%">
				<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm debit_hide_show calculation rightAligntextClass','placeholder'=>'Debit']); ?>
			
				<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm credit_hide_show calculation rightAligntextClass','placeholder'=>'Credit','style'=>'display:none;']); ?>	
			</td>
			
			<td align="center">
				
				<a class="btn btn-danger delete-tr1 btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
			</td>
		</tr>
	</tbody>
</table>

<table id="sample_table" style="display:none;" width="100%">
	<tbody>
		<tr class="tr1 main_tr">
			<td width="10%">
				<?php 
				echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','required'=>'required','value'=>'Dr']); ?>
			</td>
			<td width="40%">
				<?php echo $this->Form->input('ledger_id', ['options'=>@$ledgerOptions,'label' => false,'class' => 'form-control input-sm ledger','required'=>'required']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm debit_hide_show calculation rightAligntextClass','placeholder'=>'Debit']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm credit_hide_show calculation rightAligntextClass','placeholder'=>'Credit','style'=>'display:none;']); ?>	
			</td>
			<td align="center"  width="10%">
				<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
			</td>
		</tr>
		<tr class="tr2 main_tr"  style="display:none;">
			<td></td>
			<td colspan="3">
				<table class="table" align="center" id="bank_table" style="width: 90%;background-color:#EEF0F1; ">
				<tr class="bnk_tr">
				<td><label class="control-label">Mode of Payment<span class="required" aria-required="true"></span></label>
					
				<?php echo $this->Form->radio(
					'payment_mode',
					[
						['value' => 'Cheque', 'text' => 'Cheque'],
						['value' => 'NEFT/RTGS', 'text' => 'NEFT/RTGS']
					]); ?>
			</td>
			
			<td><label class="control-label">Cheque No<span class="required" aria-required="true"></span></label><?php echo $this->Form->input('cheque_no', ['type'=>'text','label' => false,'class' => 'form-control input-sm','placeholder'=>'Cheque No']); ?></td>
			
			<td><label class="control-label">Cheque Date<span class="required" aria-required="true"></span></label><?php echo $this->Form->input('cheque_date', ['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy', 'label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','value'=>date('d-m-Y')]); ?> </td>
			<td></td></tr>
			</table>
		</tr>
		
		<tr class="tr3 main_tr" class="tab" style="display:none; ">
			<td></td>
			<td colspan="3">
				<table class="table" align="center" id="party_table" style="text-align:center;width: 90%;background-color:#EEF0F1; ">
					<thead>
						<tr>
							<td>Ref. Type</td>
							<td>Ref. No.</td>
							<td>Dr/Cr</td>
							<td>Amount</td>
							
							<td></td>
						</tr>
					</thead>
					
					<tbody id="main_tbody1">
						<tr class="main_tr1">
							
							<td width="20%">
								<?php 
								echo $this->Form->input('type', ['options'=>$option_ref,'label' => false,'class' => 'form-control input-sm','required'=>'required']); ?>
							</td>
							<td width="25%">
								<?php echo $this->Form->input('ref_name', ['type'=>'text','label' => false,'class' => 'form-control input-sm']); ?>
							</td>
							<td width="15%">
								<?php 
								echo $this->Form->input('type_cr_dr', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','required'=>'required','value'=>'Dr']); ?>
							</td>
							<td width="20%">
								<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm debit_hide_show calculation rightAligntextClass','placeholder'=>'Debit']); ?>
							
								<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm credit_hide_show calculation rightAligntextClass','placeholder'=>'Credit','style'=>'display:none;']); ?>	
							</td>
							<td align="center"  width="5%">
								<a class="btn btn-danger delete-tr1 btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
							</td>
							
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5" align="left">	
								<button type="button" class="add_row1 btn btn-primary input-sm"><i class="fa fa-plus"></i> </button>
							</td>
						</tr>
					</tfoot>
				</table>
			</td>
			<td></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" >	
				<button type="button" class="add_row btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
			</td>
		</tr>
	</tfoot>
</table>
			
	
<!-- BEGIN PAGE LEVEL STYLES -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->css('/assets/global/plugins/clockface/css/clockface.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<!-- END COMPONENTS PICKERS -->

	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-select/bootstrap-select.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/jquery-multi-select/css/multi-select.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/clockface/js/clockface.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/moment.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<!-- END COMPONENTS PICKERS -->
	
	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-select/bootstrap-select.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->script('/assets/admin/pages/scripts/components-pickers.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<!-- END COMPONENTS PICKERS -->

	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->script('/assets/global/scripts/metronic.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/layout/scripts/layout.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/layout/scripts/quick-sidebar.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/layout/scripts/demo.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/pages/scripts/components-dropdowns.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL SCRIPTS -->

<?php
	$js="
	$(document).ready(function() { 
       
		$('.calculation').die().live('keyup',function()
		{ 
			total_debit_credit();
		});
		
		function total_debit_credit()
		{
			var total_debit_amount  = 0;
			var total_credit_amount = 0;
			$('#main_table tbody#main_tbody tr.main_tr').each(function()
			{
				var debit_amount  = parseFloat($(this).find('td:nth-child(3) input').val());
				var credit_amount = parseFloat($(this).find('td:nth-child(4) input').val());
				if(debit_amount)
				{
					total_debit_amount  = total_debit_amount+debit_amount;
				}

				if(credit_amount)
				{
					total_credit_amount = total_credit_amount+credit_amount;
				}
			});
			$('.total_debit').val(total_debit_amount);
			$('.total_credit').val(total_credit_amount);
		}

		$('.delete-tr').die().live('click',function() 
		{
			$(this).closest('tr').next('tr.tr2').hide();
			$(this).closest('tr').next('tr').next('tr.tr3').hide();
			$(this).closest('tr').remove();
			
			rename_rows();
			total_debit_credit();
		});

		$('.delete-tr1').die().live('click',function() 
		{
			$(this).closest('tr').remove();
			rename_rows();
			
		});
		
		$('.ledger').die().live('change',function() 
		{
			var ledger=$(this).val();
			var open_window=($('option:selected',this).attr('open_window'));
			//alert(open_window);
			if(open_window=='bank'){
				$(this).closest('tr').next('tr.tr2').show();
				$(this).closest('tr').next('tr').next('tr.tr3').hide();
			}
			if(open_window=='party'){
			$(this).closest('tr').next('tr').next('tr.tr3').show();
			$(this).closest('tr').next('tr.tr2').hide();
			}
		});
		
		$('.hide_cr_dr').die().live('change',function() 
		{
			var check_debit_or_credit = $(this).val(); 
			if(check_debit_or_credit=='Dr')
			{
				$(this).closest('tr').find('.debit_hide_show').show();
				$(this).closest('tr').find('.credit_hide_show').val('');
				$(this).closest('tr').find('.credit_hide_show').hide();
			}
			else if(check_debit_or_credit=='Cr')
			{
				$(this).closest('tr').find('.debit_hide_show').hide();
				$(this).closest('tr').find('.debit_hide_show').val('');
				$(this).closest('tr').find('.credit_hide_show').show();
			}

		});

		$('.add_row').click(function(){
			add_row();
		}) ;


		function add_row()
		{
			var tr1=$('#sample_table tbody tr.tr1').clone();
			$('#main_table tbody#main_tbody').append(tr1);
			var tr2=$('#sample_table tbody tr.tr2').clone();
			$('#main_table tbody#main_tbody').append(tr2);
			var tr3=$('#sample_table tbody tr.tr3').clone();
			$('#main_table tbody#main_tbody').append(tr3);
			rename_rows();
			
		}
		
		$('.add_row1').live('click',function(){
 			var tr=$('#sample_table1 tbody tr.main_tr1').clone();
			$(this).closest('#party_table').append(tr);
			rename_rows();
		}) ;
 
		rename_rows();

		function rename_rows()
		{
			var i=0; 
			$('#main_table tbody#main_tbody tr.tr1').each(function(){
				$(this).find('td:nth-child(2) select').select2().attr({name:'payment_rows['+i+'][ledger_id]',id:'payment_rows-'+i+'-ledger_id'});	
				$(this).find('td:nth-child(3) input').attr({name:'payment_rows['+i+'][debit]', id:'payment_rows-'+i+'-debit'});
				$(this).find('td:nth-child(4) input').attr({name:'payment_rows['+i+'][credit]', id:'payment_rows-'+i+'-credit'});
				i++;
				});
			
			var i=0;
			$('#main_table tbody#main_tbody tr.tr2 table#bank_table tr.bnk_tr').each(function(){ 
				
				$(this).find('td:nth-child(1) input').attr({name:'payment_rows['+i+'][mode_of_payment]',id:'payment_rows-'+i+'-mode_of_payment'});	
				$(this).find('td:nth-child(2) input').attr({name:'payment_rows['+i+'][cheque_no]', id:'payment_rows-'+i+'-cheque_no'});
				$(this).find('td:nth-child(3) input').attr({name:'payment_rows['+i+'][cheque_date]', id:'payment_rows-'+i+'-cheque_date'});
			
				i++;
			});
			
			var k=0;
				$('#main_table tbody#main_tbody tr.tr3').each(function(){ 
					var j=0; 
					$(this).find('table#party_table tbody#main_tbody1 tr.maintr1').each(function(){
					$(this).find('td:nth-child(1) select').select2().attr({name:'payment_rows['+k+'][reference_details]['+j+'][type]',id:'payment_rows-'+k+'-reference_details-'+j+'-type'});	
					$(this).find('td:nth-child(2) input').attr({name:'payment_rows['+k+'][reference_details]['+j+'][ref_name]', id:'payment_rows-'+k+'-reference_details-'+j+'-ref_name'});
					$(this).find('td:nth-child(4) input').attr({name:'payment_rows['+k+'][reference_details]['+j+'][debit]', id:'payment_rows-'+k+'-reference_details-'+j+'-debit'});
					$(this).find('td:nth-child(5) input').attr({name:'payment_rows['+k+'][reference_details]['+j+'][credit]', id:'payment_rows-'+k+'-reference_details-'+j+'-credit'});
					j++;
				});
				k++;
				});
		}

		ComponentsPickers.init();
	});

	function checkValidation() 
	{ 
			var total_debit  = $('.total_debit').val();
			var total_credit = $('.total_credit').val();

			if(total_debit!=total_credit)
			{
				alert('Credit and debit value not matched');
				return false;
			}
			else
			{ 
				if(confirm('Are you sure you want to submit!'))
				{
					$('.submit').attr('disabled','disabled');
					$('.submit').text('Submiting...');
					return true;
				}
				else
				{
					return false;
				}

			}
	}
	
	
	";

echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom')); 
?>
