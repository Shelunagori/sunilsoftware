<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Receipt');
?>
<div class="row">
	<div class="col-md-9">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Receipt</span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($receipt,['onsubmit'=>'return checkValidation()']) ?>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Voucher No :</label>&nbsp;&nbsp;
								<?= h('#'.str_pad($voucher_no, 4, '0', STR_PAD_LEFT)) ?>
								<input type="hidden" value="<?php echo $voucher_no;?>" name="voucher_no">
								<input type="hidden" value="<?php echo $company_id;?>" name="company_id">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Transaction Date <span class="required">*</span></label>
								<?php echo $this->Form->control('transaction_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy','label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','data-date-start-date'=>@$coreVariable[fyValidFrom],'data-date-end-date'=>@$coreVariable[fyValidTo],'value'=>date('d-m-Y')]); ?>
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="table-responsive">
							<table id="main_table" class="table table-condensed table-bordered main_table" style="margin-bottom: 4px;" width="100%">
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
											$option[]= ['value'=>'Cr','text'=>'Cr'];
											echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm','value'=>'Cr']); ?>
										</td>
										<td width="15%">
											<?php echo $this->Form->input('ledger_id', ['options'=>@$partyOptions,'label' => false,'class' => 'form-control input-medium ledger_id','required'=>'required']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass calculation mainDebit','placeholder'=>'Debit','style'=>'display:none;']); ?>
										</td>
										<td width="25%">
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm calculation rightAligntextClass mainCredit','placeholder'=>'Credit']); ?>	
										</td>
										<td align="center"></td>
									</tr>
									<!---->
									<tr class="banktr tab" style="display:none;  background-color:#eeeeee">
									<td width="10%">
											</td>
										<td width="10%">
											<?php 
											unset($option1);
											$option1[]= ['value'=>'Cheque','text'=>'Cheque'];
				                            $option1[]= ['value'=>'NEFT/RTGS','text'=>'NEFT/RTGS'];
											echo $this->Form->input('mode_of_payment', ['empty'=>'--select--','options'=>$option1,'label' =>false,'class' => 'form-control input-sm']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('cheque_no', ['label' =>false,'class' => 'form-control input-sm cheque_no','placeholder'=>'Cheque No']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('cheque_date', ['label' =>false,'class' => 'form-control input-sm cheque_date date-picker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'Cheque Date', 'value'=>date('d-m-Y')]); ?>
										</td>
										<td align="center"></td>
									</tr>
									<!---->
									<!---->
									<tr class="partytr tab" style="display:none; background-color:#eeeeee">
									<td colspan="5">
									<table id="main_table1" class="table table-condensed table-bordered main_table1" style="margin-bottom: 4px;" width="100%">
									<tbody id='main_tbody1' class="tab">
									<tr class="main_tr1 tab">
										<td width="20%">
											<?php
											unset($option2);											
											$option2[]= ['value'=>'New Ref','text'=>'New Ref'];
											$option2[]= ['value'=>'Agst Ref','text'=>'Agst Ref'];
											$option2[]= ['value'=>'Advance','text'=>'Advance'];
											$option2[]= ['value'=>'On Account','text'=>'On Account'];
											echo $this->Form->input('ref_type', ['empty'=>'--select--','options'=>$option2,'label' =>false,'class' => 'form-control input-sm refClass']); ?>
										</td>
										<td width="20%" class="refClass1">
											<!--<?php echo $this->Form->input('name', ['label' =>false,'class' => 'form-control input-sm cheque_no','placeholder'=>'Name']); ?>-->
										</td>
										<td width="10%">
											<?php 
											unset($option);
											$option[]= ['value'=>'Cr','text'=>'Cr',];
											$option[]= ['value'=>'Dr','text'=>'Dr',];
											echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','value'=>'Cr']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass debit_hide_show calculation1','placeholder'=>'Debit','style'=>'display:none;']); ?>
										</td>
										<td width="25%">
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm calculation1 rightAligntextClass credit_hide_show','placeholder'=>'Credit']); ?>	
										</td>
										</tr>
										</tbody>
										<tfoot>
									    <tr>
										<td>	
											<button type="button" class="add_row1 btn btn-primary input-sm"><i class="fa fa-plus"></i> </button>
										</td>
										<td colspan="2">&nbsp;</td>
										<td width="25%" >
											<?php echo $this->Form->input('party_debit', ['label' => false,'class' => 'form-control input-sm party_total_debit rightAligntextClass','id'=>'','placeholder'=>'Party Total Debit','type'=>'text']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('party_credit', ['label' => false,'class' => 'form-control input-sm party_total_credit rightAligntextClass','id'=>'','placeholder'=>'Party Total Credit']); ?>
										</td>
										</tr>
										</tfoot>
										</table></td></tr>
									<!---->
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2" >	
											<button type="button" class="add_row btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('voucher_amount', ['label' => false,'class' => 'form-control input-sm total_debit rightAligntextClass','id'=>'total_inward','placeholder'=>'Total Debit','type'=>'text']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('amount', ['label' => false,'class' => 'form-control input-sm total_credit rightAligntextClass','id'=>'total_inward','placeholder'=>'Total Credit']); ?>
											
										</td>
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
				<?= $this->Form->button(__('Submit'),['class'=>'btn btn-success']) ?>
				<?= $this->Form->end() ?>
			</div>
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

<table id="sample_table" class="main_table" style="display:none;" width="100%">
	<tbody>
		<tr class="main_tr" class="tab">
			<td width="10%">
				<?php 
				echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','value'=>'Dr']); ?>
			</td>
			<td width="15%">
				<?php echo $this->Form->input('ledger_id', ['options'=>@$partyOptions,'label' => false,'class' => 'form-control input-medium ledger_id']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm debit_hide_show calculation rightAligntextClass mainDebit','placeholder'=>'Debit']); ?>
			</td>
			<td width="25%">
				<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm credit_hide_show calculation rightAligntextClass mainCredit','placeholder'=>'Credit','style'=>'display:none;']); ?>	
			</td>
			<td align="center">
				<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-trash-o"></i></a>
			</td>
		</tr>
		<!---->
		<tr class="banktr tab" style="display:none; background-color:#eeeeee">
			<td width="10%">
			</td>
			<td width="10%">
				<?php 
				unset($option1);
				$option1[]= ['value'=>'Cheque','text'=>'Cheque'];
				$option1[]= ['value'=>'NEFT/RTGS','text'=>'NEFT/RTGS'];
				echo $this->Form->input('mode_of_payment', ['empty'=>'--select--','options'=>$option1,'label' =>false,'class' => 'form-control input-sm']); ?>
			</td>
			<td width="25%" >
				<?php echo $this->Form->input('cheque_no', ['label' =>false,'class' => 'form-control input-sm cheque_no','placeholder'=>'Cheque No']); ?>
			</td>
			<td width="25%" >
				<?php echo $this->Form->input('cheque_date', ['label' =>false,'class' => 'form-control input-sm cheque_date date-picker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'Cheque Date']); ?>
			</td>
			<td align="center"></td>
		</tr>
		<!---->
			<!---->
				<tr class="partytr tab" style="display:none; background-color:#eeeeee">
									<td colspan="4">
									<table id="main_table1" class="table table-condensed table-bordered main_table1" style="margin-bottom: 4px;" width="100%">
									<tbody id='main_tbody1' class="tab">
									<tr class="main_tr1 tab">
										<td width="20%">
											<?php 
											unset($option2);
											$option2[]= ['value'=>'New Ref','text'=>'New Ref'];
											$option2[]= ['value'=>'Agst Ref','text'=>'Agst Ref'];
											$option2[]= ['value'=>'Advance','text'=>'Advance'];
											$option2[]= ['value'=>'On Account','text'=>'On Account'];
											echo $this->Form->input('ref_type', ['options'=>$option2,'label' =>false,'class' => 'form-control input-sm refClass','required'=>'required']); ?>
										</td>
										<td width="20%" class="refClass1">
											<!--<?php echo $this->Form->input('name', ['label' =>false,'class' => 'form-control input-sm cheque_no','placeholder'=>'Name']); ?>-->
										</td>
										<td width="10%">
											<?php 
											unset($option);
											$option[]= ['value'=>'Cr','text'=>'Cr'];
											$option[]= ['value'=>'Dr','text'=>'Dr'];
											echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','value'=>'Cr']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass debit_hide_show calculation1','placeholder'=>'Debit','style'=>'display:none;']); ?>
										</td>
										<td width="25%">
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm calculation1 rightAligntextClass credit_hide_show','placeholder'=>'Credit']); ?>	
										</td>
										</tr>
										</tbody>
										<tfoot>
						<tr>
							<td >	
								<button type="button" class="add_row1 btn btn-primary input-sm"><i class="fa fa-plus"></i> </button>
							</td>
							<td colspan="2">&nbsp;</td>
							<td width="25%" >
											<?php echo $this->Form->input('party_debit', ['label' => false,'class' => 'form-control input-sm party_total_debit rightAligntextClass','id'=>'','placeholder'=>'Party Total Debit','type'=>'text']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('party_credit', ['label' => false,'class' => 'form-control input-sm party_total_credit rightAligntextClass','id'=>'','placeholder'=>'Party Total Credit']); ?>
											
										</td>
						</tr>
					</tfoot>
										</table></td></tr>
									<!---->
	</tbody>
</table>

<table id="sample_table1" style="display:none;" width="100%">
	<tbody>
			<tr class="main_tr1 tab">
										<td width="20%">
											<?php 
											unset($option2);
											$option2[]= ['value'=>'New Ref','text'=>'New Ref'];
											$option2[]= ['value'=>'Agst Ref','text'=>'Agst Ref'];
											$option2[]= ['value'=>'Advance','text'=>'Advance'];
											$option2[]= ['value'=>'On Account','text'=>'On Account'];
											echo $this->Form->input('ref_type', ['options'=>$option2,'label' =>false,'class' => 'form-control input-sm refClass']); ?>
										</td>
										<td width="20%" class="refClass1">
											<!--<?php echo $this->Form->input('name', ['label' =>false,'class' => 'form-control input-sm cheque_no','placeholder'=>'Name']); ?>-->
										</td>
										<td width="10%">
											<?php 
											unset($option);
											$option[]= ['value'=>'Cr','text'=>'Cr',];
											$option[]= ['value'=>'Dr','text'=>'Dr',];
											echo $this->Form->input('type', ['options'=>$option,'label' => false,'class' => 'form-control input-sm hide_cr_dr','value'=>'Cr']); ?>
										</td>
										<td width="25%" >
											<?php echo $this->Form->input('debit', ['label' => false,'class' => 'form-control input-sm rightAligntextClass debit_hide_show calculation1','placeholder'=>'Debit','style'=>'display:none;']); ?>
										</td>
										<td width="25%">
											<?php echo $this->Form->input('credit', ['label' => false,'class' => 'form-control input-sm calculation1 rightAligntextClass credit_hide_show','placeholder'=>'Credit']); ?>	
										</td>
										<td align="center">
				<a class="btn btn-danger delete-tr1 btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-remove"></i></a>
			</td>
										</tr>
									<!---->
	</tbody>
</table>
<?php
	$js="
	$(document).ready(function() { 
       
	   $('.ledger_id').die().live('change',function() 
		{
			var ledger_id = $(this).val(); 
			var open_window=$('option:selected', this).attr('open_window');
			//alert(open_window);
			//var account_type=$('option:selected', this).attr('account_type');
			//alert(account_type);
			if(open_window=='reference')
			{
			    $(this).closest('tr').next('tr').next('tr.partytr').show();
				$(this).closest('tr').next('tr.banktr').hide();
			}
			else if(open_window=='bank')
			{
				$(this).closest('tr').next('tr.banktr').show();
				$(this).closest('tr').next('tr').next('tr.partytr').hide();
			}
			else if(open_window=='on_account'){
			    $(this).closest('tr').next('tr').next('tr.partytr').show();
				$(this).closest('tr').next('tr.banktr').hide();
			}
			else if(open_window=='no'){
			$(this).closest('tr').next('tr.banktr').hide();
			$(this).closest('tr').next('tr').next('tr.partytr').hide();
			}
		});
		
		$('.refClass').die().live('change',function(){
		var itemQ=$(this).closest('tr');
		var itemvalue=$(this).val();
		var ledgerId=$(this).closest('table').parent().parent().parent().find('select.ledger_id option:selected').val();
		var url='".$this->Url->build(["controller" => "Receipts", "action" => "ajaxReferenceDetails"])."';
		url=url+'/'+itemvalue
		$.ajax({
			url: url,
			type: 'GET'
			//dataType: 'text'
		}).done(function(response) {
		//alert(response);
				itemQ.find('.refClass1').html(response);
		});	
		});
		
		$('.calculation').die().live('keyup',function()
		{ 
			total_debit_credit();
		});
		
		function total_debit_credit()
		{
			var total_debit_amount  = 0;
			var total_credit_amount = 0;
			$('.mainDebit').each(function()
			{
				var debit_amount  = parseFloat($(this).val());
				if(debit_amount)
				{
					total_debit_amount  = parseFloat(total_debit_amount)+parseFloat(debit_amount);
				}
			});
			$('.mainCredit').each(function()
			{
				var credit_amount  = parseFloat($(this).val());
				if(credit_amount)
				{
					total_credit_amount = parseFloat(total_credit_amount)+parseFloat(credit_amount);
				}
			});
			
			$('.total_debit').val(total_debit_amount);
			$('.total_credit').val(total_credit_amount);
		}

		$('.calculation1').die().live('keyup',function()
		{ 
		    var obj=$(this).closest('table');
			party_total_debit_credit(obj);
		});

		function party_total_debit_credit(obj)
		{
			var party_total_debit_amount  = 0;
			var party_total_credit_amount = 0;
			obj.find('tr.main_tr1').each(function()
			{
				var debit_amount  = parseFloat($(this).find('td:nth-child(4) input').val());
				var credit_amount = parseFloat($(this).find('td:nth-child(5) input').val());
				if(debit_amount)
				{
					party_total_debit_amount  = parseFloat(party_total_debit_amount)+parseFloat(debit_amount);
				}
				if(credit_amount)
				{
					party_total_credit_amount = parseFloat(party_total_credit_amount)+parseFloat(credit_amount);
				}
			});
			obj.find('.party_total_debit').val(party_total_debit_amount);
			obj.find('.party_total_credit').val(party_total_credit_amount);
		}
		
		$('.delete-tr').die().live('click',function() 
		{
		$(this).closest('tr').next().next('tr.partytr').remove();
		$(this).closest('tr').next('tr.banktr').remove();
		$(this).closest('tr').remove();
			rename_rows();
			total_debit_credit();
		});
		
		$('.delete-tr1').die().live('click',function() 
		{
			$(this).closest('tr').remove();
			rename_rows();
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
			var main_tr=$('#sample_table tbody tr.main_tr').clone();
			$('#main_table tbody#main_tbody').append(main_tr);
			var banktr=$('#sample_table tbody tr.banktr').clone();
			$('#main_table tbody#main_tbody').append(banktr);
			var partytr=$('#sample_table tbody tr.partytr').clone();
			$('#main_table tbody#main_tbody').append(partytr);
			rename_rows();
			$('.banktr').hidden();
			
		}
		
		$('.add_row1').live('click',function(){
 			var main_tr1=$('#sample_table1 tbody tr.main_tr1').clone();
			$(this).closest('#main_table1').append(main_tr1);
			rename_rows();
			
		}) ;
		rename_rows();
		
        function rename_rows()
		{
			var i=0; 
			$('#main_table tbody#main_tbody tr.main_tr').each(function(){ 
			var obj=$(this).closest('tr').next();
			var partyObj=$(this).closest('tr').next().next();
			$(this).find('td:nth-child(1) select').select2().attr({name:'receipt_rows['+i+'][type]',id:'receipt_rows-'+i+'-type'});	
				$(this).find('td:nth-child(2) select').select2().attr({name:'receipt_rows['+i+'][ledger_id]',id:'receipt_rows-'+i+'-ledger_id'});	
				$(this).find('td:nth-child(3) input').attr({name:'receipt_rows['+i+'][debit]', id:'receipt_rows-'+i+'-debit'});
				$(this).find('td:nth-child(4) input').attr({name:'receipt_rows['+i+'][credit]', id:'receipt_rows-'+i+'-credit'});
				
				obj.find('td:nth-child(2) select').select2().attr({name:'receipt_rows['+i+'][mode_of_payment]',id:'receipt_rows-'+i+'-mode_of_payment'});	
				obj.find('td:nth-child(3) input').attr({name:'receipt_rows['+i+'][cheque_no]', id:'receipt_rows-'+i+'-cheque_no'});
				obj.find('td:nth-child(4) input').attr({name:'receipt_rows['+i+'][cheque_date]', id:'receipt_rows-'+i+'-cheque_date'});
				
				/* var j=0;
				$('#main_table1 tbody#main_tbody1 tr.main_tr1').each(function(){ 
				partyObj.find('td:nth-child(1) select').select2().attr({name:'receipt_rows['+i+'][reference_details]['+j+'][ref_type]',id:'receipt_rows-'+i+'-reference_details-'+j+'-ref_type'});	
					partyObj.find('td:nth-child(2) input').attr({name:'receipt_rows['+i+'][reference_details]['+j+'][ref_name]', id:'receipt_rows-'+i+'-reference_details-'+j+'-ref_name'});
					partyObj.find('td:nth-child(4) input').attr({name:'receipt_rows['+i+'][reference_details]['+j+'][debit]', id:'receipt_rows-'+i+'-reference_details-'+j+'-debit'});
					partyObj.find('td:nth-child(5) input').attr({name:'receipt_rows['+i+'][reference_details]['+j+'][credit]', id:'receipt_rows-'+i+'-reference_details-'+j+'-credit'});
				j++;
				}); */
				
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
	} ";

echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom')); 
?>