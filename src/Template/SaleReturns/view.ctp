<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Stock Journal View');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Sales Return View</span>
				</div>
			</div>
			<div class="portlet-body">
				<table width="100%" >
				  <tr>
					<td width="15%"><b>Voucher No </b></td>
					<td width="1%">:</td>
					<td><?php echo '#'.str_pad($saleReturn->voucher_no, 4, '0', STR_PAD_LEFT);?></td>
					<td width="15%"><b>Reference No</b></td>
					<td width="1%">:</td>
					<td><?php echo $saleReturn->party_ledger->name; ?></td>
					<td width="15%"><b>Transaction Date</b></td>
					<td width="1%">:</td>
					<td><?php echo date("d-m-Y",strtotime($saleReturn->transaction_date)); ?></td>
				  </tr>
                  
                </table><br>
       		    <table width="100%" class="table  table-bordered" style="border:none;" border="0">
					
								<thead>
								<tr><td align="center" colspan="6"><b>Inward</b></td></tr>
								<tr align="center">
									<td><b>Sr</b></td>
									<td><b>Item</b></td>
									<td><b>Qty</b></td>
									<td><b>Rate</b></td>
									<td><b>Taxable Amount</b></td>
								</tr>
								</thead>
								<tbody id='main_tbody' class="tab">
								 <?php 		$i=0;							
										 foreach($saleReturn->sale_return_rows as $sale_return_row)
										 {  
								?>
									<tr class="main_tr" class="tab">
										<td width="7%"><?php echo $i+1; ?></td>
										<td width="25%">
											<?php echo $sale_return_row->item->name; ?>
										</td>
										<td width="15%" class="rightAligntextClass">
											<?php echo $sale_return_row->return_quantity; ?>
										</td>
										<td width="20%" class="rightAligntextClass">
											<?php echo $sale_return_row->rate; ?>
										</td>
										<td width="25%" class="rightAligntextClass">
											<?php echo $sale_return_row->taxable_value; ?>	
										</td>
									</tr>
								<?php $i++; } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="4" class="rightAligntextClass">Total</td>
										<td width="25%" class="rightAligntextClass"><?php echo $saleReturn->inward_amount_total;?></td>
									</tr>
								</tfoot>
							</table>
						
            </div>
		</div>
	</div>
</div>

