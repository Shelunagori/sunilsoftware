
<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Create Credit Note');
if($creditNote->cash_or_credit=="credit")
{
	foreach($partyOptions as $partyOption)
	{
		$value=$partyOption['value'];
		if($value==$creditNote->party_ledger_id)
		{
			$party_states=$partyOption['party_state_id'];
		if($party_states>'0')
		{
			$party_state_id=$party_states;
		}
		else
		{
			$party_state_id=$state_id;
		}
		}
	}
}
else if($creditNote->cash_or_credit=="cash")
{
  $party_state_id=$state_id;
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Credit Note </span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($creditNote,['onsubmit'=>'return checkValidation()']) ?>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>Voucher No :</label>&nbsp;&nbsp;
								<?= h('#'.str_pad($creditNote->voucher_no, 4, '0', STR_PAD_LEFT)) ?>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label width="10%">Sale Invoice No. for Reference</label>
								<?php echo $this->Form->control('sales_invoice_no',['class'=>'form-control input-sm ','label'=>false,'placeholder'=>'Sale Invoice No.','type'=>'text', 'value'=>$creditNote->sales_invoice_no, 'autofocus'=>'autofocus']); ?>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Transaction Date <span class="required">*</span></label>
								<?php echo $this->Form->control('transaction_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy','label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','data-date-start-date'=>@$coreVariable[fyValidFrom],'data-date-end-date'=>@$coreVariable[fyValidTo],'value'=>$creditNote->transaction_date]); ?>
							</div>
						</div>
							<input type="hidden" name="company_id" class="customer_id" value="<?php echo $company_id;?>">
							<input type="hidden" name="state_id" class="state_id" value="<?php echo $state_id;?>">
							<input type="hidden" name="ps" class="ps" value="<?php echo $party_state_id;?>">
							<input type="hidden" name="is_interstate" id="is_interstate" value="<?php if(@$party_state_id!=$state_id){echo '1';}else if($party_state_id==$state_id){echo '0';}?>">
							<input type="hidden" name="isRoundofType" id="isRoundofType" class="isRoundofType" value="0">
							<input type="hidden" name="voucher_no" id="" value="<?= $creditNote->voucher_no ?>">
						<div class="col-md-2">
							<label>Type</label><span class="required">*</span></label>
							<select name="cash_or_credit" id="UserGender" class="cashcredit  form-control input-sm">
								<option value="cash" class="cash" <?php if($creditNote->cash_or_credit=='cash'){ echo 'selected';}?>>Cash</option>
								<option value="credit" class="credit" <?php if($creditNote->cash_or_credit=='credit'){ echo 'selected';}?>>Credit</option>
							</select>
						</div>
						<div class="col-md-2  cusomerIds" style="display:none">
							<label>Party</label>
							<?php echo $this->Form->control('party_ledger_id',['empty'=>'-Select Party-', 'class'=>'form-control input-sm party_ledger_id select2me customer_id ','label'=>false, 'options' => $partyOptions, 'value'=>$creditNote->party_ledger_id]);
							?>
						</div>
						<div class="col-md-2  cashcusomerIds" style="display:none">
							<label>Party</label>
							<?php echo $this->Form->control('cash_party_ledger_id',[ 'class'=>'form-control input-sm party_ledger_id select2me customer_id ','label'=>false, 'options' => $CashPartyLedgers, 'value'=>$creditNote->party_ledger_id]);
							?>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Sales Account</label>
								<?php echo $this->Form->control('sales_ledger_id',['class'=>'form-control input-sm sales_ledger_id select2me','label'=>false, 'options' => $Accountledgers,'required'=>'required', 'value'=>$creditNote->sales_ledger_id]);
								?>
							</div>
						</div> 
					</div>
					<br>
					<div class="row">
						<div class="table-responsive">
							<table id="main_table" class="table table-condensed table-bordered" style="margin-bottom: 4px;" width="100%">
								<thead>
									<tr align="center">
										<td width="20%"><label>Item<label></td>
										<td><label>Qty<label></td>
										<td><label>Rate<label></td>
										<td><label>Taxable Value<label></td>
										<td><label id="gstDisplay">
										<?php if(@$party_state_id!=$state_id){echo 'IGST';}else if($party_state_id==$state_id){echo 'GST';}?>
										<label></td>
										<td><label>Net Amount<label></td>
										<td></td>
									</tr>
								</thead>
								<tbody id='main_tbody' class="tab">
								 <?php if(!empty($creditNote->credit_note_rows))
                                         $i=0;		
								         foreach($creditNote->credit_note_rows as $creditNoteRow)
									     {
										 if(@$party_state_id!=$state_id){$exactgst=$creditNoteRow->gst_value;}else if($party_state_id==$state_id){$exactgst=$creditNoteRow->gst_value/2;}
							     ?>
									<tr class="main_tr" class="tab">
										<td>
										<input type="hidden" name="creditNoteRow<?php echo $i;?>id" class="id" value="<?php echo $creditNoteRow->id; ?>">
										<input type="hidden" name="" class="outStock" value="0">
											<input type="hidden" name="creditNoteRow<?php echo $i;?>gst_figure_id" class="gst_figure_id" value="<?php echo $creditNoteRow->gst_figure_id;?>">
											<input type="hidden" name="gst_amount" class="gst_amount" value="">
											<input type="hidden" name="creditNoteRow<?php echo $i;?>gst_figure_tax_percentage" class="gst_figure_tax_percentage calculation" value="<?php echo $creditNoteRow->gst_figure->tax_percentage;?>">
											<input type="hidden" name="tot" class="totamount calculation" value="">
											<input type="hidden" name="creditNoteRow<?php echo $i;?>gst_value" class="gstValue calculation" value="<?php echo $creditNoteRow->gst_value;?>">
											<input type="hidden" name="exactgst_value" class="exactgst_value calculation" value="<?php $exactgst;?>">
											<input type="hidden" name="discountvalue" class="discountvalue calculation" value="">
											
											<?php echo $this->Form->input('creditNoteRow.'.$i.'.item_id', ['empty'=>'-Item Name-', 'options'=>$itemOptions,'label' => false,'class' =>'form-control input-medium attrGet calculation ','required'=>'required', 'value'=>$creditNoteRow->item_id]); 
											echo $this->Form->input('creditNoteRow.'.$i.'.id', ['value'=>$creditNoteRow->id,'type'=>'hidden']);	?>
											<span class="itemQty" style="color:red"></span>
										</td>
										<td>
											<?php echo $this->Form->input('creditNoteRow.'.$i.'.quantity', ['label' => false,'class' => 'form-control input-sm calculation quantity rightAligntextClass','id'=>'check','required'=>'required','placeholder'=>'Quantity', 'value'=>$creditNoteRow->quantity]); ?>
										</td>
										<td>
											<?php echo $this->Form->input('creditNoteRow.'.$i.'.rate', ['label' => false,'class' => 'form-control input-sm calculation rate rightAligntextClass','required'=>'required','placeholder'=>'Rate', 'value'=>$creditNoteRow->rate]); ?>
										</td>
										<td>
											<?php echo $this->Form->input('creditNoteRow.'.$i.'.taxable_value', ['label' => false,'class' => 'form-control input-sm gstAmount reverse_total_amount rightAligntextClass','required'=>'required','placeholder'=>'Amount', 'readonly'=>'readonly', 'value'=>$creditNoteRow->taxable_value]); ?>
										</td>
										<td>
											<?php echo $this->Form->input('creditNoteRow.'.$i.'.gst_figure_tax_name', ['label' => false,'class' => 'form-control input-sm gst_figure_tax_name rightAligntextClass', 'readonly'=>'readonly','required'=>'required','placeholder'=>'GST', 'value'=>$creditNoteRow->gst_figure->name]); ?>	
										</td>
										<td>	
											<?php echo $this->Form->input('creditNoteRow.'.$i.'.net_amount', ['label' => false,'class' => 'form-control input-sm discountAmount calculation rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'Taxable Value', 'value'=>$creditNoteRow->net_amount]); ?>	
										</td>
										<td align="center">
									<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
								</td>
							</tr>
								<?php $i++; } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="7">	
											<button type="button" class="add_row btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
										</td>
									</tr>
									<tr>
										<td colspan="5" align="right"><b>Amt Before Tax</b></td>
										<td colspan="2">
											<?php echo $this->Form->input('amount_before_tax', ['label' => false,'class' => 'form-control input-sm amount_before_tax rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'']); ?>	
										</td>
									</tr>
									<tr id="add_cgst">
										<td colspan="5" align="right"><b>Total CGST</b></td>
										<td colspan="2">
											<?php echo $this->Form->input('total_cgst', ['label' => false,'class' => 'form-control input-sm add_cgst rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'']); ?>	
										</td>
									</tr>
									<tr id="add_sgst">
										<td colspan="5" align="right"><b>Total SGST</b></td>
										<td colspan="2">
											<?php echo $this->Form->input('total_sgst', ['label' => false,'class' => 'form-control input-sm add_sgst rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'']); ?>	
										</td>
									</tr>
									<tr id="add_igst">
										<td colspan="5" align="right"><b>Total IGST</b></td>
										<td colspan="2">
											<?php echo $this->Form->input('total_igst', ['label' => false,'class' => 'form-control input-sm add_igst rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'']); ?>	
										</td>
									</tr>
									
									<tr>
										<td colspan="5" align="right"><b>Round OFF</b></td>
										<td colspan="2">
											<?php echo $this->Form->input('round_off', ['label' => false,'class' => 'form-control input-sm roundValue rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'']); ?>	
										</td>
									</tr>
									<tr>
										<td colspan="5" align="right"><b>Amt After Tax</b></td>
										<td colspan="2">
											<?php echo $this->Form->input('amount_after_tax', ['label' => false,'class' => 'form-control input-sm amount_after_tax rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'']); ?>	
										</td>
									</tr>
								</tfoot>
							</table>
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
			<td>
			<input type="hidden" name="" class="outStock" value="0">
				<input type="hidden" name="gst_figure_id" class="gst_figure_id" value="">
				<input type="hidden" name="gst_amount" class="gst_amount" value="">
				<input type="hidden" name="gst_figure_tax_percentage" class="gst_figure_tax_percentage calculation" value="">
				<input type="hidden" name="tot" class="totamount calculation" value="">
				<input type="hidden" name="gst_value" class="gstValue calculation" value="">
				<input type="hidden" name="exactgst_value" class="exactgst_value calculation" value="">
				<input type="hidden" name="discountvalue" class="discountvalue calculation" value="">
				<?php echo $this->Form->input('item_id', ['empty'=>'-Item Name-', 'options'=>$itemOptions,'label' => false,'class' =>'form-control input-medium attrGet calculation','required'=>'required']); ?>
				<span class="itemQty" style="color:red"></span>
			</td>
			<td>
				<?php echo $this->Form->input('quantity', ['label' => false,'class' => 'form-control input-sm calculation quantity rightAligntextClass','id'=>'check','required'=>'required','placeholder'=>'Quantity', 'value'=>1]); ?>
			</td>
			<td>
				<?php echo $this->Form->input('rate', ['label' => false,'class' => 'form-control input-sm calculation rate rightAligntextClass','required'=>'required','placeholder'=>'Rate']); ?>
			</td>
			<td>
				<?php echo $this->Form->input('taxable_value', ['label' => false,'class' => 'form-control input-sm gstAmount reverse_total_amount rightAligntextClass','required'=>'required','placeholder'=>'Amount', 'readonly'=>'readonly']); ?>
			</td>
			<td>
				<?php echo $this->Form->input('gst_figure_tax_name', ['label' => false,'class' => 'form-control input-sm gst_figure_tax_name rightAligntextClass', 'readonly'=>'readonly','required'=>'required','placeholder'=>'GST']); ?>	
			</td>
			<td>
				<?php echo $this->Form->input('net_amount', ['label' => false,'class' => 'form-control input-sm discountAmount calculation rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'Taxable Value']); ?>					
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
		//select item gst rate start
		$('.attrGet').die().live('change',function(){
			var gst_amount=$('option:selected', this).attr('gst_amount');
			$(this).closest('tr').find('.gst_amount').val(gst_amount);
			forward_total_amount();
		});
		//change party state wise start
		$('.party_ledger_id').die().live('change',function(){
			var customer_state_id=$('option:selected', this).attr('party_state_id');
			var state_id=$('.state_id').val();
			if(customer_state_id!=state_id)
			{
				if(customer_state_id>0)
				{
					$('#gstDisplay').html('IGST');
					$('#add_igst').show();
					$('#add_cgst').hide();
					$('#add_sgst').hide();
					$('#is_interstate').val('1');
				}
				else if(!customer_state_id)
				{
					$('#gstDisplay').html('GST');
					$('#add_cgst').show();
					$('#add_sgst').show();
					$('#add_igst').hide();
					$('#is_interstate').val('0');
				}
				else if(customer_state_id==0)
				{
					$('#gstDisplay').html('GST');
					$('#add_cgst').show();
					$('#add_sgst').show();
					$('#add_igst').hide();
					$('#is_interstate').val('0');
				}
			}
			else if(customer_state_id==state_id){
				$('#gstDisplay').html('GST');
				$('#add_cgst').show();
				$('#add_sgst').show();
				$('#add_igst').hide();
				$('#is_interstate').val('0');
			}
			//$(this).closest('tr').find('.output_igst_ledger_id').val(output_igst_ledger_id);
		});
		//change party state wise end
		
		//cash or credit start
		$('.cashcredit').die().live('change',function(){
			
			var cashcredit=$(this).val();
			if(cashcredit=='cash')
			{
				$('.cusomerIds').hide();
				$('#gstDisplay').html('GST');
				$('#add_cgst').show();
				$('#add_sgst').show();
				$('#add_igst').hide();
				$('#add_igst').val('');
				$('#is_interstate').val('0');
				//$('.cashcusomerIds').show();
			}
			else{
				$('.cusomerIds').show();
				//$('.cashcusomerIds').hide();
			}
		});
		//cash or credit end
		// delete row start
		$('.delete-tr').die().live('click',function() 
		{
			$(this).closest('tr').remove();
			rename_rows();
		});
		//delete row end
		ComponentsPickers.init();
		//add row start 
		$('.add_row').click(function(){
			add_row();
		}) ;
		
		function add_row()
		{
			var tr=$('#sample_table tbody tr.main_tr').clone();
			$('#main_table tbody#main_tbody').append(tr);
			//$('.attrGet').select2();
			rename_rows();
			forward_total_amount();
		}
		//add row end
		//rename row start
		function rename_rows()
		{
			var i=0;
			$('#main_table tbody#main_tbody tr.main_tr').each(function(){ 
			
			$(this).find('td:nth-child(1) input.id').attr({name:'credit_note_rows['+i+'][id]',id:'credit_note_rows['+i+'][id]'});
			
			$(this).find('td:nth-child(1) select.attrGet').select2().attr({name:'credit_note_rows['+i+'][item_id]',id:'credit_note_rows['+i+'][item_id]'});
			
				$(this).find('.quantity').attr({name:'credit_note_rows['+i+'][quantity]',id:'credit_note_rows['+i+'][quantity]'});
				$(this).find('.rate').attr({name:'credit_note_rows['+i+'][rate]',id:'credit_note_rows['+i+'][rate]'});
				$(this).find('.gstAmount').attr({name:'credit_note_rows['+i+'][taxable_value]',id:'credit_note_rows['+i+'][taxable_value]'});
			  
				$(this).find('.gst_figure_id').attr({name:'credit_note_rows['+i+'][gst_figure_id]',id:'credit_note_rows['+i+'][gst_figure_id]'});
			  
				$(this).find('.discountAmount').attr({name:'credit_note_rows['+i+'][net_amount]',id:'credit_note_rows['+i+'][net_amount]'});
				$(this).find('.gstValue').attr({name:'credit_note_rows['+i+'][gst_value]',id:'credit_note_rows['+i+'][gst_value]'});	
				
				i++;
			});
		}
		
		
		
		//calculation start 
		$('.calculation').die().live('keyup',function()
		{
			forward_total_amount();
		});
		$( document ).ready( readyFn );
		$( document ).ready( gstChange );
		$( document ).ready( partyOnLoad );
		function readyFn( jQuery ) {
		forward_total_amount();
		}
		function gstChange()
		{
			var cashcredit=$('.cashcredit').val();
			if(cashcredit=='credit')
			{
					$('#gstDisplay').html('IGST');
					$('.cusomerIds').hide();
					$('.cusomerIds').show();
			}
			else if(cashcredit=='cash'){
				$('.cusomerIds').hide();
				$('.cusomerIds').hide();
			}
		}
		function partyOnLoad()
		{
		var customer_state_id=$('.ps').val();
			var state_id=$('.state_id').val();
			if(customer_state_id!=state_id)
			{
				if(customer_state_id>0)
				{
					$('#gstDisplay').html('IGST');
					$('#add_igst').show();
					$('#add_cgst').hide();
					$('#add_sgst').hide();
				}
				else if(!customer_state_id)
				{
					$('#gstDisplay').html('GST');
					$('#add_cgst').show();
					$('#add_sgst').show();
					$('#add_igst').hide();
				}
				else if(customer_state_id==0)
				{
					$('#gstDisplay').html('GST');
					$('#add_cgst').show();
					$('#add_sgst').show();
					$('#add_igst').hide();
				}
			}
			else if(customer_state_id==state_id){
				$('#gstDisplay').html('GST');
				$('#add_cgst').show();
				$('#add_sgst').show();
				$('#add_igst').hide();
			}
			}
		function forward_total_amount()
		{
			var total  = 0;
			var gst_amount  = 0;
			var gst_value  = 0;
			var s_cgst_value=0;
			var roundOff1=0;
			var round_of=0;
			var isRoundofType=0;
			var igst_value=0;
			var outOfStockValue=0;
			var s_igst=0;
			var newsgst=0;
			var newigst=0;
			var exactgstvalue=0;
			$('#main_table tbody#main_tbody tr.main_tr').each(function()
			{
			    var outdata=$(this).closest('tr').find('.outStock').val();
				if(!outdata){outdata=0;}
				outOfStockValue=parseFloat(outOfStockValue)+parseFloat(outdata);
				
				 var gstpaid=$('option:selected', this).attr('gst_amount');
			    $(this).closest('tr').find('.gst_amount').val(gstpaid);
				
				var quantity  = Math.round($(this).find('.quantity').val());
				if(!quantity){quantity=0;}
				var rate  = parseFloat($(this).find('.rate').val());
				if(!rate){rate=0;}
				var totamount = quantity*rate;
				$(this).find('.totamount').val(totamount);
				   
				var discount  = parseFloat($(this).find('.discount').val());
				if(!discount){discount=0;}
				var discountValue=(discount*totamount)/100;
				var discountAmount=totamount-discountValue;
				
				if(!discountAmount){discountAmount=0;}
				$(this).find('.discountAmount').val(discountAmount.toFixed(2));
				var gst_ietmamount  = $(this).find('.gst_amount').val();
				var discountAmount  = $(this).find('.discountAmount').val();
				var item_gst_amount=discountAmount/quantity;
				
				if(item_gst_amount<=gst_ietmamount)
				{
					var first_gst_figure_tax_percentage=$('option:selected', this).attr('FirstGstFigure');
					var first_gst_figure_tax_name=$('option:selected', this).attr('FirstGstFigure');
					var first_gst_figure_id=$('option:selected', this).attr('first_gst_figure_id');
						
					$(this).closest('tr').find('.gst_figure_id').val(first_gst_figure_id);
					$(this).closest('tr').find('.gst_figure_tax_percentage').val(first_gst_figure_tax_percentage);
					$(this).closest('tr').find('.gst_figure_tax_name').val(first_gst_figure_tax_name);
                }
				else if(item_gst_amount>gst_ietmamount)
				{
					var second_gst_figure_tax_percentage=$('option:selected', this).attr('SecondGstFigure');
					var second_gst_figure_tax_name=$('option:selected', this).attr('SecondGstFigure');
					var second_gst_figure_id=$('option:selected', this).attr('second_gst_figure_id');

					$(this).closest('tr').find('.gst_figure_id').val(second_gst_figure_id);
					$(this).closest('tr').find('.gst_figure_tax_percentage').val(second_gst_figure_tax_percentage);
					$(this).closest('tr').find('.gst_figure_tax_name').val(second_gst_figure_tax_name);
				}
				
				$(this).find('.discountvalue').val(discountValue.toFixed(2));
				
				var gst_figure_tax_percentage  = parseFloat($(this).find('.gst_figure_tax_percentage').val());
				if(!gst_figure_tax_percentage){gst_figure_tax_percentage=0;}
				var discountAmount  = parseFloat($(this).find('.discountAmount').val());
				if(!discountAmount){discountAmount=0;}
				var divideValue=100;
				var divideval=divideValue+gst_figure_tax_percentage;
				var gstAmount=(discountAmount*100)/divideval;
	            var gstValue=(gstAmount*gst_figure_tax_percentage)/100;
				$(this).find('.gstAmount').val(gstAmount.toFixed(2));
				$(this).find('.gstValue').val(gstValue.toFixed(2));

				var gstValue  = parseFloat($(this).find('.gstValue').val());
				var gstAmount  = parseFloat($(this).find('.gstAmount').val());
				var is_interstate  = parseFloat($('#is_interstate').val());
				if(is_interstate=='0')
				{
				exactgstvalue=round(gstValue/2,2);
				$(this).find('.exactgst_value').val(exactgstvalue);
				var add_cgst  = $(this).find('.exactgst_value').val();
				if(!add_cgst){add_cgst=0;}
				//alert(add_cgst);
				newsgst=round(parseFloat(newsgst)+parseFloat(add_cgst), 2);
				gst_amount=parseFloat(gst_amount.toFixed(2))+parseFloat(gstAmount.toFixed(2));
				total=gst_amount+newsgst+newsgst;
				roundOff1=Math.round(total);
				}else{
				exactgstvalue=round(gstValue,2);
				$(this).find('.exactgst_value').val(exactgstvalue);
				var add_igst  = parseFloat($(this).find('.exactgst_value').val());
				if(!add_igst){add_igst=0;}
				newigst=round(parseFloat(newigst)+parseFloat(add_igst), 2);
				gst_amount=parseFloat(gst_amount.toFixed(2))+parseFloat(gstAmount.toFixed(2));
				total=gst_amount+newigst;
				roundOff1=Math.round(total);
				}
				if(total<roundOff1)
				{
				round_of=parseFloat(roundOff1)-parseFloat(total);
				isRoundofType='0';
				}
				if(total>roundOff1)
				{
				round_of=parseFloat(roundOff1)-parseFloat(total);
				isRoundofType='1';
				}
				if(total==roundOff1)
				{
				round_of=parseFloat(total)-parseFloat(roundOff1);
				isRoundofType='0';
				}
		});
		$('.amount_after_tax').val(roundOff1.toFixed(2));
		$('.amount_before_tax').val(gst_amount.toFixed(2));
		$('.add_cgst').val(newsgst);
		$('.add_sgst').val(newsgst);
		$('.add_igst').val(newigst);					
		$('.roundValue').val(round_of.toFixed(2));
		$('.isRoundofType').val(isRoundofType);
		$('.outOfStock').val(outOfStockValue);
		rename_rows();
		}
		//calculation end
		});
		//form validation start
		function checkValidation() 
		{  
			var amount_before_tax  = $('.amount_before_tax').val();
			var amount_after_tax = $('.amount_after_tax').val();
			if(amount_before_tax && amount_after_tax)
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
			else{
				   alert('Please enter your data!');
			}
		}";

echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom')); 
?>