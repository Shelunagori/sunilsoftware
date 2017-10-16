<?php //pr($salesInvoice); exit;
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
 
 
$this->set('title', 'Purchase Return');
$is_interstate=0;

if($supplier_state_id== $state_id){
		$is_interstate=0;
}else{
	$is_interstate=1;
}
?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			
			<div class="portlet-body">
				<?= $this->Form->create($purchaseReturn,['id'=>'form_sample_2']) ?>
					<div class="row">
						
						<div class="col-md-6 caption-subject font-green-sharp bold " align="center" style="font-size:16px"><b>PURCHASE RETURN VIEW</b></div>
					</div><br><br>
					
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label><b>Purchase Invoice Voucher No : </b><br><?= h('#'.str_pad($purchaseReturn->voucher_no, 4, '0', STR_PAD_LEFT)) ?></label>&nbsp;&nbsp;
								
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><b>Transaction Date</b></label><br>
								<?php  
								echo $purchaseReturn->transaction_date;
								?>
							</div>
						</div>
						
						<div class="col-md-3">
								<label><b>Party</b></label><br/>
								<?php echo $purchaseReturn->purchase_invoice->supplier_ledger->supplier->name
								?>
						</div>
						
						<div class="col-md-3">
								<label><b>Purchase Account</b></label><br/>
								<?php echo $purchaseReturn->purchase_invoice->purchase_ledger->name
								?>
						</div>
						
						
					</div>
					
				   <div class="row">
				  <div class="table-responsive">
						<table id="main_table" class="table table-condensed table-bordered" style="margin-bottom: 4px;font-size:12px;" width="100%">
							<thead>
								<tr align="center" style="font-size:12px;">
									<th rowspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">SR No.<label></th>
									<th rowspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">Item<label></th>
									<th rowspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">Qty</label></th>
									<th rowspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">Rate</label></th>
									<th  colspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">Discount (%)</label></th>
									<th  colspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">PNF (%)</label></th>
									<th rowspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">Taxable Value</label></td>
									<?php if($supplier_state_id== $state_id){ ?>
											<th colspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px" id="gstDisplay">GST</label></th>
									<?php } else { ?>
												<th colspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px" id="gstDisplay">IGST</label></th>
									<?php } ?>
									
									<th rowspan="2" style="text-align:center;"><label  style="text-align:center; font-size:12px">Round Of</label></th>
									<th rowspan="2" style="text-align:center; "><label style="text-align:center;">Total</label></th>
									
								</tr>
								<tr>
									<th><div align="center" style="text-align:center; font-size:12px">%</div></th>
									<th><div align="center" style="text-align:center; font-size:12px">Rs</div></th>
									<th><div align="center" style="text-align:center; font-size:12px">%</div></th>
									<th><div align="center" style="text-align:center; font-size:12px">Rs</div></th>
									<th><div align="center" style="text-align:center; font-size:12px">%</div></th>
									<th><div align="center" style="text-align:center; font-size:12px">Rs</div></th>
									
								</tr>
							</thead>
							<tbody id='main_tbody' class="tab">
							 <?php if(!empty($purchaseReturn->purchase_return_rows))
							$i=1;
							$total_amt=0;
							 foreach($purchaseReturn->purchase_return_rows as $purchase_return_row)
							 { 
							?>
							<tr class="main_tr" class="tab" >
								<td width="3%" align="center"><?php echo $i; ?></td>
								<td width="15%" align="center">
										<?php echo $purchase_return_row->item->name; ?>
								</td>
								<td width="5%" align="center">
										<?php echo $purchase_return_row->quantity;
										?>
								</td>	
								<td width="5%" align="center">
										<?php echo $purchase_return_row->rate;
										?>
								</td>	
								<td width="5%" align="center">
										<?php echo $purchase_return_row->discount_percentage;
										?>
								</td>	
								<td width="5%" align="center">
										<?php echo $purchase_return_row->discount_amount;
										?>
								</td>	
								<td width="5%" align="center">
										<?php echo $purchase_return_row->pnf_percentage;
										?>
								</td>	
								<td width="5%" align="center">
										<?php echo $purchase_return_row->pnf_amount;
										?>
								</td>	
								
								<td width="5%" align="center">
										<?php echo $purchase_return_row->taxable_value;
										?>
								</td>

								<td width="5%" align="center">
										<?php echo $purchase_return_row->item->FirstGstFigures->tax_percentage;
										?>
								</td>	
								<td width="5%" align="center">
										<?php echo $purchase_return_row->gst_value;
										?>
								</td>
								<td width="5%" align="center">
										<?php echo $purchase_return_row->round_off;
										?>
								</td>	
								<td width="5%" align="center" style="border-left-width:2px;">
										<?php echo $purchase_return_row->net_amount;
												$total_amt+=$purchase_return_row->net_amount;
										?>
								</td>
								
							</tr>
							<?php $i++; } ?>
							</tbody>
							<tfoot>
								<tr>
									<td  colspan="12"  align="right"><b>Total</b>
									</td>
									<td style="text-align:center">
									<?php echo $total_amt;
										?>	
									</td>
								</tr>
							</tfoot>
					</table>
				   </div>
				  </div>
			</div>
				
		</div>
	</div>
</div>

