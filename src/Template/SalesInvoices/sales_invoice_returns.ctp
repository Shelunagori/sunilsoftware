<style>
.disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}
.checkbox{
	margin:0px;
}
</style>
<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Update Sales Invoice & Sales Return');
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
					<span class="caption-subject font-green-sharp bold ">Update Sales Invoice & Sales Return</span>
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
									$words = explode(" ", $coreVariable['company_name']);
									$acronym = "";
									foreach ($words as $w) {
									$acronym .= $w[0];
									}
								?>
								<?= $acronym.'/'.$financialyear.'/'. h(str_pad($salesInvoice->voucher_no, 3, '0', STR_PAD_LEFT))?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Transaction Date </label>
								<?php echo $this->Form->control('transaction_date',['class'=>'form-control input-sm ','value'=>$salesInvoice->transaction_date, 'autofocus'=>'autofocus','type'=>'text','label'=>false,'readonly'=>'readonly']); ?>
							</div>
						</div>
						<input type="hidden" name="party_state_id" class="ps" value="<?php echo $party_state_id;?>">
                        <input type="hidden" name="outOfStock" class="outOfStock" value="false">
						<input type="hidden" name="due_days" class="dueDays">
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
								<?php echo $this->Form->control('party_ledger_id',['class'=>'form-control input-sm  party_ledger_id','label'=>false, 'required'=>'required', 'value'=>$salesInvoice->party_ledger->name,'type'=>'text','readonly'=>'readonly']);
								?>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Sales Account</label>
								<?php echo $this->Form->control('sales_ledger_id',['class'=>'form-control input-sm sales_ledger_id select2me','label'=>false, 'options' => $Accountledgers,'required'=>'required', 'value'=>$salesInvoice->sales_ledger_id,'readonly'=>'readonly']);
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
																
										<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.item_id', ['empty'=>'-Item Name-','options'=>$itemOptions,'label' => false,'class' => 'form-control input-sm attrGet calculation','required'=>'required','value'=>$salesInvoiceRow->item_id]);
										echo $this->Form->input('salesInvoiceRow.'.$i.'.id', ['value'=>$salesInvoiceRow->id,'type'=>'hidden']);	?>
										<span class="itemQty" style="font-size:10px;"></span>
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.quantity', ['type'=>'text','label' => false,'class' => 'form-control input-sm calculation quantity numberOnly rightAligntextClass','id'=>'check','required'=>'required','placeholder'=>'Quantity', 'value'=>$salesInvoiceRow->quantity]); ?>
									
									<input type="hidden" name="" class="salesReturnQuantity" value=<?php echo @$sales_return_qty[$salesInvoiceRow->item->id];?>>
									
									<input type="hidden" name="" class="salesRQuantity" value=<?php echo @$salesInvoiceRow->quantity;?>>
									
									
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.rate', ['label' => false,'class' => 'form-control input-sm calculation rate rightAligntextClass','required'=>'required','placeholder'=>'Rate','value'=>$salesInvoiceRow->rate, 'readonly'=>'readonly', 'tabindex'=>'-1']); ?>
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.discount_percentage', ['label' => false,'class' => 'form-control input-sm discalculation discount numberOnly rightAligntextClass','placeholder'=>'Dis.', 'value'=>$salesInvoiceRow->discount_percentage]); ?>	
								</td>
								<td>
									<?php echo $this->Form->input('salesInvoiceRow.'.$i.'.discount', ['label' => false,'class' => 'form-control input-sm calculation dis_amount numberOnly rightAligntextClass','placeholder'=>'Dis.', 'value'=>$salesInvoiceRow->discount]); ?>	
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
									<a class="btn btn-danger delete-tr btn-xs" href="#" role="button" style="margin-bottom: 5px;"><i class="fa fa-times"></i></a>
									<?php 
									if($salesInvoiceRow->is_gst_excluded=='1')
									{
										$checked='checked';
										$value='1';
									}else if($salesInvoiceRow->is_gst_excluded=='0'){
										$checked='';
										$value='0';
									}
									?>
									<?php echo $this->Form->input('is_gst_excluded1', ['label' => false,'class' => 'form-control input-sm is_gst_excluded tooltips', 'type'=>'checkbox', 'data-placement'=>'top', 'data-original-title'=>'Excluded GST?', 'checked'=>$checked]); ?>
									<?php echo $this->Form->input('is_gst_excluded', ['label' => false,'class' => 'form-control input-sm is_gstvalue_excluded', 'type'=>'hidden', 'value'=>$value]); ?>
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
										<?php echo $this->Form->input('discount_amount', ['label' => false,'class' => 'form-control input-sm toalDiscount rightAligntextClass','required'=>'required', 'readonly'=>'readonly','placeholder'=>'', 'tabindex'=>'-1']); ?>	
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
										<td colspan="6" align="right"><b>Round Off</b>
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
							<b>Receipt Amount</b>
							</td>
							
							<td colspan="2">
							<?php 
							if($salesInvoice->invoice_receipt_type=='cash'){ $sa=$salesInvoice->receipt_amount;}else{ $sa='0';}
							
							echo $this->Form->input('receipt_amount', ['label' => false,'class' => 'form-control input-sm receipt_amount rightAligntextClass','required'=>'required','placeholder'=>'', 'value'=>$sa, 'type'=>'text']); 
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
	

	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-select/bootstrap-select.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
	<!-- BEGIN COMPONENTS PICKERS -->
	
	
	<?php echo $this->Html->script('/assets/global/plugins/clockface/js/clockface.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/moment.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<!-- END COMPONENTS PICKERS -->
	
	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-select/bootstrap-select.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->script('/assets/admin/pages/scripts/components-pickers.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<!-- END COMPONENTS PICKERS -->

	<!-- BEGIN COMPONENTS DROPDOWNS -->
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
			<span class="itemQty" style="font-size:10px;"></span>
			</td>
			<td>
				<?php echo $this->Form->input('quantity', ['label' => false,'class' => 'form-control input-sm calculation quantity numberOnly rightAligntextClass','id'=>'check','required'=>'required','placeholder'=>'Quantity', 'value'=>1]); ?>
			</td>
			<td>
				<?php echo $this->Form->input('rate', ['label' => false,'class' => 'form-control input-sm calculation rate rightAligntextClass','required'=>'required','placeholder'=>'Rate', 'readonly'=>'readonly', 'tabindex'=>'-1']); ?>
			</td>
			<td>
				<?php echo $this->Form->input('discount_percentage', ['label' => false,'class' => 'form-control input-sm discalculation discount numberOnly rightAligntextClass','placeholder'=>'Dis.','value'=>0]); ?>	
			</td>
			<td>
				<?php echo $this->Form->input('discount', ['label' => false,'class' => 'form-control input-sm calculation dis_amount numberOnly rightAligntextClass','placeholder'=>'Dis.','value'=>0]); ?>	
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
				<?php echo $this->Form->input('is_gst_excluded1', ['label' => false,'class' => 'form-control input-sm is_gst_excluded tooltips', 'type'=>'checkbox', 'data-placement'=>'top', 'data-original-title'=>'Excluded GST?']); ?>
				<?php echo $this->Form->input('is_gst_excluded', ['label' => false,'class' => 'form-control input-sm is_gstvalue_excluded', 'type'=>'hidden']); ?>
			</td>
		</tr>
	</tbody>
</table>
