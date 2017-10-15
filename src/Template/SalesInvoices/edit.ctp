<style>
.disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}
</style>
<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Update Sales Invoice');
foreach($partyOptions as $partyOption)
{
	$value=$partyOption['value'];
	if($value==$salesInvoice->party_ledger_id)
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
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Update Sales Invoice</span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($salesInvoice,['onsubmit'=>'return checkValidation()']) ?>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Voucher No :</label>&nbsp;&nbsp;
								<?php
								    $date = date('Y-m-d', strtotime($salesInvoice->transaction_date));
									$d = date_parse_from_format('Y-m-d',$date);
									$yr=$d["year"];$year= substr($yr, -2);
									if($d["month"]=='01' || $d["month"]=='02' || $d["month"]=='03')
									{
									  $startYear=$year-1;
									  $endYear=$year;
									  $financialyear=$startYear.'-'.$endYear;
									}
									else
									{
									  $startYear=$year;
									  $endYear=$year+1;
									  $financialyear=$startYear.'-'.$endYear;
									}
								if($coreVariable['company_name']=='DANGI SAREES')
								{
									$field='DS';
								}
								else if($coreVariable['company_name']=='SUNIL TEXTILES')
								{
									$field='ST';
								}
								else if($coreVariable['company_name']=='SUNIL GARMENTS')
								{
									$field='SG';
								}
								?>
								<?= $field.'/'.$financialyear.'/'. h(str_pad($salesInvoice->voucher_no, 3, '0', STR_PAD_LEFT))?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Transaction Date <span class="required">*</span></label>
								<?php echo $this->Form->control('transaction_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy','label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','data-date-start-date'=>@$coreVariable[fyValidFrom],'data-date-end-date'=>@$coreVariable[fyValidTo],'value'=>$salesInvoice->transaction_date, 'autofocus'=>'autofocus']); ?>
							</div>
						</div>
						<input type="hidden" name="party_state_id" class="ps" value="<?php echo $party_state_id;?>">
                        <input type="hidden" name="outOfStock" class="outOfStock" value="false">
						
						<input type="hidden" name="company_id" class="company_id" value="<?php echo $company_id;?>">
						<input type="hidden" name="location_id" class="location_id" value="<?php echo $location_id;?>">
						<input type="hidden" name="state_id" class="state_id" value="<?php echo $state_id;?>">
						<input type="hidden" name="is_interstate" id="is_interstate" value="<?php if(@$party_state_id!=$state_id){if($party_state_id>0){echo '1';}
									else if($party_state_id==0){echo '0';}else if(!$party_state_id){echo '0';}
									}else if(@$party_state_id==$state_id) { echo '0';}
									?>">
						<input type="hidden" name="isRoundofType" id="isRoundofType" class="isRoundofType" value="0">
						<input type="hidden" name="voucher_no" id="" value="<?=$salesInvoice->voucher_no?>">
						<div class="col-md-3">
								<label>Party</label>
								<?php echo $this->Form->control('party_ledger_id',['empty'=>'-Select Party-', 'class'=>'form-control input-sm party_ledger_id select2me','label'=>false, 'options' => $partyOptions,'required'=>'required', 'value'=>$salesInvoice->party_ledger_id]);
								?>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Sales Account</label>
								<?php echo $this->Form->control('sales_ledger_id',['class'=>'form-control input-sm sales_ledger_id select2me','label'=>false, 'options' => $Accountledgers,'required'=>'required', 'value'=>$salesInvoice->sales_ledger_id]);
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
									<td><label>Discount(%)<label></td>
									<td><label>Discount Amount<label></td>
									<td><label>Taxable Value<label></td>
									
									<td><label id="gstDisplay">
										<?php if(@$party_state_id!=$state_id){if($party_state_id>0){echo 'IGST';}
										else if($party_state_id==0){echo 'GST';}else if(!$party_state_id){echo 'GST';}
										}else if(@$party_state_id==$state_id) { echo 'GST';}
										?>
									<label></td>
									<td><label>Net Amount<label></td>
									<td></td>
								</tr>
								</thead>
								<tbody id='main_tbody' class="tab">
								 <?php if(!empty($salesInvoice->sales_invoice_rows))
                                         $i=0;		
								         foreach($salesInvoice->sales_invoice_rows as $salesInvoiceRow)
									     {
											if(@$salesInvoiceRow->quantity ==@$sales_return_qty[$salesInvoiceRow->item->id]){
											$disable_class="disabledbutton";
										}else{
											$disable_class=""; 
										} 

									if(@$party_state_id!=$state_id){if($party_state_id>0){ $exactgst=$salesInvoiceRow->gst_value;}
									else if($party_state_id==0){$exactgst=$salesInvoiceRow->gst_value/2;}else if(!$party_state_id){$exactgst=$salesInvoiceRow->gst_value/2;}
									}else if(@$party_state_id==$state_id) { $exactgst=$salesInvoiceRow->gst_value/2; }
							     ?>
								<tr class="main_tr " class="tab">
									<td>
										<input type="hidden" name="salesInvoiceRow<?php echo $i;?>id" class="id" value="<?php echo $salesInvoiceRow->id; ?>">
										<input type="hidden" name="" class="outStock" value="0">
										<input type="hidden" name="" class="totStock " value="0">
										<input type="hidden" name="" class="exactQty " value="<?php echo $salesInvoiceRow->quantity;?>">
										<input type="hidden" name="gst_amount" class="gst_amount" value="">	
										<input type="hidden" name="salesInvoiceRow<?php echo $i;?>gst_figure_id" class="gst_figure_id" value="<?php echo $salesInvoiceRow->gst_figure_id;?>">
										<input type="hidden" name="" class="gst_figure_tax_percentage calculation" value="<?php echo $salesInvoiceRow->gst_figure->tax_percentage;?>">
										<input type="hidden" name="" class="totamount calculation" value="">
										<input type="hidden" name="salesInvoiceRow<?php echo $i;?>gst_value" class="gstValue calculation" value="<?php echo $salesInvoiceRow->gst_value;?>">
										<input type="hidden" name="exactgst_value" class="exactgst_value calculation" value="<?php $exactgst;?>">
										<input type="hidden" name="" class="discountvalue calculation" value="">
																
										<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.item_id', ['empty'=>'-Item Name-','options'=>$itemOptions,'label' => false,'class' => 'form-control input-sm attrGet calculation','required'=>'required','value'=>$salesInvoiceRow->item->id]);
										echo $this->Form->input('salesInvoiceRow.'.$i.'.id', ['value'=>$salesInvoiceRow->id,'type'=>'hidden']);	?>
										<span class="itemQty" style="color:red"></span>
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.quantity', ['type'=>'text','label' => false,'class' => 'form-control input-medium  quantity rightAligntextClass','id'=>'check','required'=>'required','placeholder'=>'Quantity', 'value'=>$salesInvoiceRow->quantity,'min'=>@$sales_return_qty[$salesInvoiceRow->item->id]]); 
									?>
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.rate', ['label' => false,'class' => 'form-control input-sm calculation rate rightAligntextClass','required'=>'required','placeholder'=>'Rate','value'=>$salesInvoiceRow->rate, 'readonly'=>'readonly', 'tabindex'=>'-1']); ?>
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.discount_percentage', ['label' => false,'class' => 'form-control input-sm calculation discount rightAligntextClass','placeholder'=>'Dis.', 'value'=>$salesInvoiceRow->discount_percentage]); ?>	
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.discount', ['label' => false,'class' => 'form-control input-sm discalculation dis_amount rightAligntextClass','placeholder'=>'Dis.', 'value'=>$salesInvoiceRow->discount]); ?>	
								</td>
								<td>
								<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.taxable_value', ['label' => false,'class' => 'form-control input-sm gstAmount reverse_total_amount rightAligntextClass','required'=>'required', 'placeholder'=>'Amount', 'value'=>$salesInvoiceRow->taxable_value, 'readonly'=>'readonly', 'tabindex'=>'-1']); ?>	
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.gst_figure_tax_name', ['label' => false,'class' => 'form-control input-sm gst_figure_tax_name rightAligntextClass', 'readonly'=>'readonly','required'=>'required','placeholder'=>'', 'value'=> $salesInvoiceRow->gst_figure->name, 'tabindex'=>'-1']); ?>	
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.net_amount', ['label' => false,'class' => 'form-control input-sm discountAmount calculation rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'Taxable Value', 'value'=>$salesInvoiceRow->net_amount, 'tabindex'=>'-1']); ?>	
								</td>
															
								<td align="center">
								<?php 
									
										if(@$sales_return_qty[$salesInvoiceRow->item->id]==0) { ?>
											<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
									<?php } ?>
								
								</td>
							</tr>
								<?php $i++; } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="8">
											<button type="button" class="add_row btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
										</td>
									</tr>
								
									<tr>
										<td colspan="6" align="right"><b>Amt Before Tax</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('amount_before_tax', ['label' => false,'class' => 'form-control input-sm amount_before_tax rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
										</td>
									</tr>
						
									<tr>
										<td colspan="6" align="right"><b>Discount Amount</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('discount_amount', ['label' => false,'class' => 'form-control input-sm toalDiscount rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1', 'style'=>'padding-right:25px']); ?>	
										</td>
									</tr>
									
									<tr id="add_cgst">
										<td colspan="6" align="right"><b>Total CGST</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('total_cgst', ['label' => false,'class' => 'form-control input-sm add_cgst rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
										</td>
									</tr>
									<tr id="add_sgst">
										<td colspan="6" align="right"><b>Total SGST</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('total_sgst', ['label' => false,'class' => 'form-control input-sm add_sgst rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
										</td>
									</tr>
									<tr id="add_igst" style="">
										<td colspan="6" align="right"><b>Total IGST</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('total_igst', ['label' => false,'class' => 'form-control input-sm add_igst rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
										</td>
									</tr>
							
									<tr>
										<td colspan="6" align="right"><b>Round OFF</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('round_off', ['label' => false,'class' => 'form-control input-sm roundValue rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
										</td>
									</tr>
									<tr>
										<td colspan="6" align="right"><b>Amt After Tax</b>
										</td>
										<td colspan="2">
										<?php echo $this->Form->input('amount_after_tax', ['label' => false,'class' => 'form-control input-sm amount_after_tax rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
										</td>
									</tr>
							<tr>
							<td colspan="4" >
									<div class="radio-list" id="invoiceReceiptTd1" style="display:none">
									 <b>Check for Receipt</b>
										<div class="radio-inline" style="padding-left: 0px;">
											<?php echo $this->Form->radio(
											'invoice_receipt_type',
											[
												['value' => 'cash', 'text' => 'Cash','class' => ''],
												['value' => 'credit', 'text' => 'Credit','class' => '']
											]
											); ?>
										</div>
                                    </div>
									<input type="hidden" id="invoiceReceiptTd2" name="invoiceReceiptTd" value="<?php if($salesInvoice->invoice_receipt_type=='cash'){ echo '1';}else { echo '0';}?>">
							</td>
							<td colspan="2" align="right">
							<b>Receipt Amount</b>';
							</td>
							
							<td colspan="2">
							<?php 
							if($salesInvoice->invoice_receipt_type=='cash'){ $sa=$salesInvoice->receipt_amount;}else{ $sa='0';}
							
							echo $this->Form->input('receipt_amount', ['label' => false,'class' => 'form-control input-sm receipt_amount rightAligntextClass','required'=>'required','placeholder'=>'', 'value'=>$sa]); 
							?>	
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
			<input type="hidden" name="" class="totStock " value="0">
			<input type="hidden" name="" class="exactQty " value="0">
			<input type="hidden" name="gst_amount" class="gst_amount" value="">
			<input type="hidden" name="gst_figure_id" class="gst_figure_id" value="">
			<input type="hidden" name="" class="gst_figure_tax_percentage calculation" value="">
			<input type="hidden" name="" class="totamount calculation" value="">
			<input type="hidden" name="gst_value" class="gstValue calculation" value="">
            <input type="hidden" name="" class="discountvalue calculation" value="">
			<input type="hidden" name="exactgst_value" class="exactgst_value calculation" value="">
				<?php echo $this->Form->input('item_id', ['empty'=>'-Item Name-','options'=>$itemOptions,'label' => false,'class' => 'form-control input-sm attrGet calculation','required'=>'required']); ?>
			<span class="itemQty" style="color:red;font-size:10px;"></span>
			</td>
			<td>
				<?php echo $this->Form->input('quantity', ['label' => false,'class' => 'form-control input-sm calculation quantity rightAligntextClass','id'=>'check','required'=>'required','placeholder'=>'Quantity', 'value'=>1]); ?>
			</td>
			<td>
				<?php echo $this->Form->input('rate', ['label' => false,'class' => 'form-control input-sm calculation rate rightAligntextClass','required'=>'required','placeholder'=>'Rate', 'readonly'=>'readonly', 'tabindex'=>'-1']); ?>
			</td>
			<td>
				<?php echo $this->Form->input('discount_percentage', ['label' => false,'class' => 'form-control input-sm calculation discount rightAligntextClass','placeholder'=>'Dis.','value'=>0]); ?>	
			</td>
			<td>
				<?php echo $this->Form->input('discount', ['label' => false,'class' => 'form-control input-sm discalculation dis_amount rightAligntextClass','placeholder'=>'Dis.','value'=>0]); ?>	
			</td>
			<td>
				<?php echo $this->Form->input('taxable_value', ['label' => false,'class' => 'form-control input-sm gstAmount reverse_total_amount rightAligntextClass','required'=>'required','placeholder'=>'Amount', 'readonly'=>'readonly', 'tabindex'=>'-1']); ?>
			</td>
			<td>
				<?php echo $this->Form->input('gst_figure_tax_name', ['label' => false,'class' => 'form-control input-sm gst_figure_tax_name rightAligntextClass', 'readonly'=>'readonly','required'=>'required','placeholder'=>'', 'tabindex'=>'-1']); ?>	
			</td>
			<td>
				<?php echo $this->Form->input('net_amount', ['label' => false,'class' => 'form-control input-sm discountAmount calculation rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'Taxable Value', 'tabindex'=>'-1']); ?>	
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
		$('.attrGet').die().live('change',function(){
		var itemQ=$(this).closest('tr');
		var gst_amount=$('option:selected', this).attr('gst_amount');
		var sales_rate=$('option:selected', this).attr('sales_rate');
		$(this).closest('tr').find('.gst_amount').val(gst_amount);
		$(this).closest('tr').find('.rate').val(sales_rate);
		var itemId=$(this).val();
		var url='".$this->Url->build(["controller" => "SalesInvoices", "action" => "ajaxItemQuantity"])."';
		url=url+'/'+itemId
		$.ajax({
			url: url,
			type: 'GET'
			//dataType: 'text'
		}).done(function(response) {
			var fetch=$.parseJSON(response);
			var text=fetch.text;
			var type=fetch.type;
			var mainStock=fetch.mainStock;
			itemQ.find('.itemQty').html(text);
			itemQ.find('.totStock').val(mainStock);
		if(type=='true')
		{
		 itemQ.find('.outStock').val(1);
		}
		else{
		 itemQ.find('.outStock').val(0);
		}
		});	
		forward_total_amount();
		});
		
		$(document).ready(onLoadInvoiceReceipt);
		function onLoadInvoiceReceipt()
		{
		 var partyexist=$('select[name=party_ledger_id] :selected').attr('partyexist');
		 if(partyexist=='1')
			{
				$('#invoiceReceiptTd1').show();
				$('#invoiceReceiptTd2').val('1');
			}
			else{
				$('#invoiceReceiptTd1').hide();
				$('#invoiceReceiptTd2').val('0');
				$('.receipt_amount').val('0');
			}
		}
		
		$('.party_ledger_id').die().live('change',function(){
			var customer_state_id=$('option:selected', this).attr('party_state_id');
			var partyexist=$('option:selected', this).attr('partyexist');
			if(!customer_state_id){customer_state_id=0;}
			var state_id=$('.state_id').val();
			if(partyexist=='1')
			{
				$('#invoiceReceiptTd1').show();
				$('#invoiceReceiptTd2').val('1');
			}
			else{
				$('#invoiceReceiptTd1').hide();
				$('#invoiceReceiptTd2').val('0');
			}
			
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
		});
		
		$('.delete-tr').die().live('click',function() 
		{
			$(this).closest('tr').remove();
			rename_rows();
			forward_total_amount();
		});
		ComponentsPickers.init();
	});
	
	
	
	$('.add_row').click(function(){
		add_row();
    }) ;
	
	function add_row()
	{
		var tr=$('#sample_table tbody tr.main_tr').clone();
		$('#main_table tbody#main_tbody').append(tr);
		rename_rows();
		forward_total_amount();
	}
	
	$( document ).ready( stockLoad );
	function stockLoad()
	{
	$('#main_table tbody#main_tbody tr.main_tr').each(function(){ 
		var itemQ=$(this).closest('tr');
		var gst_amount=$('option:selected', this).attr('gst_amount');
		var itemId=$('option:selected', this).attr('value');
		var sales_rate=$('option:selected', this).attr('sales_rate');
		$(this).closest('tr').find('.gst_amount').val(gst_amount);
		//$(this).closest('tr').find('.rate').val(sales_rate);
		//var itemId=$(this).val();
		var url='".$this->Url->build(["controller" => "SalesInvoices", "action" => "ajaxItemQuantity"])."';
		url=url+'/'+itemId
		$.ajax({
			url: url,
			type: 'GET'
		}).done(function(response) {
			var fetch=$.parseJSON(response);
			var text=fetch.text;
			var type=fetch.type;
			var mainStock=fetch.mainStock;
			itemQ.find('.itemQty').html(text);
			itemQ.find('.totStock').val(mainStock);
			if(type=='true')
			{
			 itemQ.find('.outStock').val(1);
			}
			else{
			 itemQ.find('.outStock').val(0);
			}
		});	
		});
	}
	
	function rename_rows()
	{
		var i=0;
		$('#main_table tbody#main_tbody tr.main_tr').each(function(){ 
			
			$(this).find('td:nth-child(1) input.id').attr({name:'sales_invoice_rows['+i+'][id]',id:'sales_invoice_rows['+i+'][id]'});
			$(this).find('td:nth-child(1) select.attrGet').select2().attr({name:'sales_invoice_rows['+i+'][item_id]',id:'sales_invoice_rows['+i+'][item_id]'});
			var qty=$(this).find('.quantity').attr('min');

			$(this).find('.quantity').attr({name:'sales_invoice_rows['+i+'][quantity]',id:'sales_invoice_rows['+i+'][quantity]'}).attr('minlength', qty);
		  $(this).find('.rate').attr({name:'sales_invoice_rows['+i+'][rate]',id:'sales_invoice_rows['+i+'][rate]'});
		  $(this).find('.discount').attr({name:'sales_invoice_rows['+i+'][discount_percentage]',id:'sales_invoice_rows['+i+'][discount_percentage]'});
		  $(this).find('.dis_amount').attr({name:'sales_invoice_rows['+i+'][discount]',id:'sales_invoice_rows['+i+'][discount]'});
		  
		  $(this).find('.gstAmount').attr({name:'sales_invoice_rows['+i+'][taxable_value]',id:'sales_invoice_rows['+i+'][taxable_value]'});
		  
		  $(this).find('.gst_figure_id').attr({name:'sales_invoice_rows['+i+'][gst_figure_id]',id:'sales_invoice_rows['+i+'][gst_figure_id]'});
		  
		  $(this).find('.discountAmount').attr({name:'sales_invoice_rows['+i+'][net_amount]',id:'sales_invoice_rows['+i+'][net_amount]'});
		  $(this).find('.gstValue').attr({name:'sales_invoice_rows['+i+'][gst_value]',id:'sales_invoice_rows['+i+'][gst_value]'});
		  
		i++;
		});
	}
	

	$('.calculation').die().live('keyup',function()
	{
		forward_total_amount();
	});
	$('.discalculation').die().live('keyup',function()
	{
		reverse_total_amount();
	});
	
	$('.quantity').die().live('blur',function()
	{ 
		forward_total_amount();
	});
	
	$( document ).ready( forward_total_amount );
	$( document ).ready( partyOnLoad );
		
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
            var totDiscounts=0;	
            var convertDiscount=0;			
			$('#main_table tbody#main_tbody tr.main_tr').each(function()
			{
				var chkquanity1=$(this).find('.quantity').attr('min');
				var chkquanity2=parseFloat($(this).find('.quantity').val());
			
				if(chkquanity2 != 0){
					if(chkquanity2 < chkquanity1)
					{
						alert('Please enter a value greater than or equal to quantity '+chkquanity1);
						$(this).closest('tr').find('.quantity').val(chkquanity1); 
					}
					
				}
			
			    var outdata=$(this).closest('tr').find('.outStock').val();
				if(!outdata){outdata=0;}
				outOfStockValue=parseFloat(outOfStockValue)+parseFloat(outdata);
			
			    var gstpaid=$('option:selected', this).attr('gst_amount');
			    $(this).closest('tr').find('.gst_amount').val(gstpaid);
			
				var fetchQuantity  = $(this).find('.quantity').val();
			    var quantity=round(fetchQuantity,2);
				if(!quantity){quantity=0;}
				var rate  = parseFloat($(this).find('.rate').val());
				if(!rate){rate=0;}
				var totamount = quantity*rate;
				$(this).find('.totamount').val(totamount);
				   
				var discount  = parseFloat($(this).find('.discount').val());
				if(!discount){discount=0;}
				
				var discountValue=(discount*totamount)/100;
				var convertDiscount=round(discountValue,2);
				totDiscounts=round(parseFloat(totDiscounts)+parseFloat(discountValue), 2);
				var discountAmount=totamount-discountValue;
				$(this).find('.dis_amount').val(convertDiscount);
			
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
			$('.toalDiscount').val(totDiscounts);
			//$('.receipt_amount').val(roundOff1.toFixed(2));
			rename_rows();
		}
		
		
	
	function reverse_total_amount()
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
		var totDiscounts=0;
		var convertDiscount=0;
		$('#main_table tbody#main_tbody tr.main_tr').each(function()
		{
			var outdata=$(this).closest('tr').find('.outStock').val();
			if(!outdata){outdata=0;}
			outOfStockValue=parseFloat(outOfStockValue)+parseFloat(outdata);

			var fetchQuantity  = $(this).find('.quantity').val();
			var quantity=round(fetchQuantity,2);
			if(!quantity){quantity=0;}
			var rate  = parseFloat($(this).find('.rate').val());
			if(!rate){rate=0;}
			var totamount = quantity*rate;
			$(this).find('.totamount').val(totamount);
			   
			var dis_amount  = parseFloat($(this).find('.dis_amount').val());
			if(!dis_amount){dis_amount=0;}
			
			    var discountValue=dis_amount;
				totDiscounts=round(parseFloat(totDiscounts)+parseFloat(discountValue), 2);
				var discountAmount=totamount-discountValue;
				var convertDiscount=round((dis_amount*100)/totamount, 2);
				$(this).find('.discount').val(convertDiscount);
		

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
			var gstValue=parseFloat((gstAmount*gst_figure_tax_percentage/100).toFixed(2));
			$(this).find('.gstAmount').val(gstAmount.toFixed(2));
			$(this).find('.gstValue').val(gstValue);

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
		$('.toalDiscount').val(totDiscounts);		
		rename_rows();
	}
		
	function checkValidation() 
	{  
		var amount_before_tax  = parseFloat($('.amount_before_tax').val());
		if(!amount_before_tax || amount_before_tax==0){
			alert('Error: zero amount invoice can not be generated.');
			return false;
		}
		
		var StockDB=[]; var StockInput = {};
		$('#main_table tbody#main_tbody tr.main_tr').each(function()
		{
			var stock=$(this).find('td:nth-child(1) input.totStock').val();
			var item_id=$(this).find('td:nth-child(1) select.attrGet option:selected').val();
			var actualQuantity=parseFloat($(this).find('td:nth-child(2) input.quantity').val());
			var exactQty=parseFloat($(this).find('td:nth-child(1) input.exactQty').val());
			var existingQty=parseFloat(StockInput[item_id]);
			var quantity=parseFloat(actualQuantity)-parseFloat(exactQty);
			if(!existingQty){ existingQty=0; }
			StockInput[item_id] = quantity+existingQty;
			StockDB[item_id] = stock;
		});
		
		var c=1;
		$('#main_table tbody#main_tbody tr.main_tr').each(function()
		{
			var item_id=$(this).find('td:nth-child(1) select.attrGet option:selected').val();
			if(StockInput[item_id]>StockDB[item_id]){
				c=0;
			}
		});
		
		if(c==0){
			alert('Error: Stock is going in minus.');
			return false;
		}
		
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
	
/* function checkValidation() 
	{  
		var amount_before_tax  = $('.amount_before_tax').val();
		var amount_after_tax = $('.amount_after_tax').val();
		var outOfStock = $('.outOfStock').val();
		if(amount_before_tax && amount_after_tax && outOfStock==0)
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
		else if(outOfStock>0) {
		       alert('Please check, you have added out of stock data!');
			   return false;
		}

	} */";

echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom')); 
?>