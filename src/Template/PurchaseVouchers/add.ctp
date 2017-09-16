<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Create Purchase Voucher');
?>
<div class="row">
	<div class="col-md-9">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Create Purchase Voucher</span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($purchaseVoucher,['onsubmit'=>'return checkValidation()']) ?>
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
								<?php echo $this->Form->control('transaction_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy','label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','data-date-start-date'=>@$coreVariable[fyValidFrom],'data-date-end-date'=>@$coreVariable[fyValidTo],'value'=>date('d-m-Y')]); ?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Supplier Invoice No </label>
								<?php echo $this->Form->control('supplier_invoice_no',['class'=>'form-control input-sm','label'=>false,'placeholder'=>'Supplier Invoice No','autofocus']); ?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Supplier Invoice Date</label>
								<?php echo $this->Form->control('supplier_invoice_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy','label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text']); ?>
							</div>
						</div> 
					</div>
					<br>
					<div class="row">
						<div class="table-responsive">
							<table id="main_table" class="table table-condensed table-bordered" style="margin-bottom: 4px;" width="100%">
								<thead>
									<tr align="center">
										<td><label></td>
										<td><label>Particulars<label></td>
										<td><label>Debit<label></td>
										<td><label>Credit<label></td>
										<td></td>
									</tr>
								</thead>
								<tbody id='main_tbody' class="tab">
									<tr class="main_tr" class="tab">
										<td width="10%">
											<?php 
											$option[]= ['value'=>'Cr','text'=>'Cr',];
											$option[]= ['value'=>'Dr','text'=>'Dr',];
											echo $this->Form->input('debit_credit_list', ['options'=>$option,'label' => false,'class' => 'form-control input-sm','required'=>'required','value'=>'Cr','disabled'=>'disabled']); ?>
										</td>
										<td width="15%">
											<?php echo $this->Form->input('ledger_id', ['options'=>@$Creditledgers,'label' => false,'class' => 'form-control input-medium','required'=>'required']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass','placeholder'=>'Debit','style'=>'display:none;']); ?>
										</td>
										<td width="25%">
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm calculation rightAligntextClass','required'=>'required','placeholder'=>'Credit']); ?>	
										</td>
										<td align="center"></td>
									</tr>
									<tr class="main_tr" class="tab">
										<td width="10%">
											<?php 
											echo $this->Form->input('debit_credit_list', ['options'=>$option,'label' => false,'class' => 'form-control input-sm','required'=>'required','value'=>'Dr','disabled'=>'disabled']); ?>
										</td>
										<td width="15%">
											<?php echo $this->Form->input('ledger_id', ['options'=>@$Debitledgers,'label' => false,'class' => 'form-control input-medium','required'=>'required']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm calculation rightAligntextClass','required'=>'required','placeholder'=>'Debit']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass','style'=>'display:none;','placeholder'=>'Credit']); ?>
                                        </td>
										<td align="center"></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2" >	
											<button type="button" class="add_row btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('voucher_amount', ['label' => false,'class' => 'form-control input-sm total_debit rightAligntextClass','id'=>'total_inward','placeholder'=>'Total Debit','type'=>'text']); ?></td>
										<td width="25%" >
											<?php echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm total_credit rightAligntextClass','id'=>'total_inward','placeholder'=>'Total Credit']); ?></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				  
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label>Narration </label>
							<?php echo $this->Form->control('narration',['class'=>'form-control input-sm ','label'=>false,'placeholder'=>'Narration','rows'=>'2']); ?>
						</div>
					</div>
				</div>
			</div>
				<?= $this->Form->button(__('Submit'),['class'=>'btn btn-success submit']) ?>
				<?= $this->Form->end() ?>
		</div>
	</div>
</div>


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

<table id="sample_table" style="display:none;" width="100%">
	<tbody>
		<tr class="main_tr" class="tab">
			<td width="10%">
				<?php 
				echo $this->Form->input('debit_credit_list', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','required'=>'required','value'=>'Dr']); ?>
			</td>
			<td width="15%">
				<?php echo $this->Form->input('ledger_id', ['options'=>@$ledgers,'label' => false,'class' => 'form-control input-medium ','required'=>'required']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm debit_hide_show calculation rightAligntextClass','placeholder'=>'Debit']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm credit_hide_show calculation rightAligntextClass','placeholder'=>'Credit','style'=>'display:none;']); ?>	
			</td>
			<td align="center">
				<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
			</td>
		</tr>
	</tbody>
</table>

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
			$(this).closest('tr').remove();
			rename_rows();
			total_debit_credit();
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
			var tr=$('#sample_table tbody tr.main_tr').clone();
			$('#main_table tbody#main_tbody').append(tr);

			rename_rows();
		}


		rename_rows();

		function rename_rows()
		{
			var i=0;
			$('#main_table tbody#main_tbody tr.main_tr').each(function(){ 

				$(this).find('td:nth-child(2) select').select2().attr({name:'purchase_voucher_rows['+i+'][ledger_id]',id:'purchase_voucher_rows-'+i+'-ledger_id'});	

				$(this).find('td:nth-child(3) input').attr({name:'purchase_voucher_rows['+i+'][debit]', id:'purchase_voucher_rows-'+i+'-debit'});
				$(this).find('td:nth-child(4) input').attr({name:'purchase_voucher_rows['+i+'][credit]', id:'purchase_voucher_rows-'+i+'-credit'});

				i++;
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