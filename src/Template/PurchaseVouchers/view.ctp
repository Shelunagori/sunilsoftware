<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'purchase Voucher View');
?>
<style>
table.fixed { table-layout:fixed; }
table.fixed td { overflow: hidden; }
</style>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Purchase Voucher View</span>
				</div>
			</div>
			<div class="portlet-body">
				<table width="100%" class="fixed">
				  <tr>
					<td width="8%"><b>Voucher No </b></td>
					<td width="1%">:</td>
					<td><?php echo '#'.str_pad($purchaseVoucher->voucher_no, 4, '0', STR_PAD_LEFT);?></td>
					<td width="12%" ><b>Transaction Date</b></td>
					<td width="1%" >:</td>
					<td><?php echo date("d-m-Y",strtotime($purchaseVoucher->transaction_date)); ?></td>
					<td width="12%"><b>Supplier Invoice No</b></td>
					<td width="1%">:</td>
					<td><?php echo $purchaseVoucher->supplier_invoice_no; ?></td>
					<td width="14%"><b>Supplier Invoice Date</b></td>
					<td width="1%">:</td>
					<td><?php 
							if(!empty($purchaseVoucher->supplier_invoice_date))
							{
								echo date("d-m-Y",strtotime($purchaseVoucher->supplier_invoice_date));	
							}
						?>
					</td>
				  </tr>
                  <tr style="padding-top:5px;">
					<td width="8%" valign="top"><b>Narration </b></td>
					<td width="1%" valign="top">:</td>
					<td colspan="3"><?php echo $purchaseVoucher->narration;?></td>
				  </tr>
                </table><br>
       		    <table id="main_table" class="table table-condensed table-bordered"  width="100%">
					<thead>
					<tr align="center">
						<td><b>Ledger</b></td>
						<td><b>Debit</b></td>
						<td><b>Credit</b></td>
					</tr>
					</thead>
					<tbody id='main_tbody' class="tab">
					 <?php 
							 $i=0;									
							 foreach ($purchaseVoucher->purchase_voucher_rows as $purchaseVoucherRows):
					?>
						<tr class="main_tr" class="tab">
							<td width="60%">
								<?php echo $purchaseVoucherRows->ledger->name; ?>
							</td>
							<td width="20%" class="rightAligntextClass">
								<?php 
									if(!empty($purchaseVoucherRows->debit))
									{
										echo $purchaseVoucherRows->debit; 
									}
									else
									{
										echo '-';
									}
								?>
							</td>
							<td width="20%" align="right">
								<?php 
								    if(!empty($purchaseVoucherRows->credit))
									{
										echo $purchaseVoucherRows->credit; 
									}
									else
									{
										echo '-';
									}
								?>	
							</td>
						</tr>
					<?php $i++; endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td align="right">Total</td>
							<td  align="right"><?php echo $purchaseVoucher->voucher_amount?></td>
							<td  align="right"><?php echo $purchaseVoucher->voucher_amount?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>