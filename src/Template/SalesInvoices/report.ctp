<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Sales Report');
?>
<style>
table th {
    white-space: nowrap;
}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Sales Report</span>
				</div>
			</div>
			<div class="portlet-body table-responsive">
				<?php 
				if(!empty($salesInvoices->toArray()))
				{
				?>
				<table class="table table-bordered table-hover table-condensed" width="100%">
					<thead>
						<tr>
							<th scope="col" colspan="18" style="text-align:left";>Sales Register From <?=$from?> To <?=$to?></th>
						</tr>
						<tr>
							<th scope="col" style="text-align:center";>Customer Code</th>
							<th scope="col" style="text-align:center";>Customer Name</th>
							<th scope="col" style="text-align:center";>Invoice No</th>
							<th scope="col" style="text-align:center";>Invoice date</th>
							<th scope="col" style="text-align:center";>HSN Code</th>
							<th scope="col" style="text-align:center";>Item Code</th>
							<th scope="col" style="text-align:center";>Item Name</th>
							<th scope="col" style="text-align:center";>Quantity</th>
							<th scope="col" style="text-align:center";>Rate Per Unit</th>
							<th scope="col" style="text-align:center";>Discount %</th>
							<th scope="col" style="text-align:center";>Discount Amt.</th>
							<th scope="col" style="text-align:center";>CGST%</th>
							<th scope="col" style="text-align:center";>CGST Amt.</th>
							<th scope="col" style="text-align:center";>SGST%</th>
							<th scope="col" style="text-align:center";>SGST Amt.</th>
							<th scope="col" style="text-align:center";>IGST%</th>
							<th scope="col" style="text-align:center";>IGST Amt.</th>
							<th scope="col" style="text-align:center";>Net Amount</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$totalDiscount=0;
					$totalCgst=0;
					$totalSgst=0;
					$totalIgst=0;
					$totalNet=0;
					foreach($salesInvoices->toArray() as $data){
					foreach($data->sales_invoice_rows as $salesInvoicedata)
					{
					    if($data->party_ledger->customer_id==0 || $data->party_ledger->customer_id=='')
						{
						$customerName='Cash';
						$customerCode='-';
						}
						else{
						$customerName=$data->party_ledger->name;
						$customerCode=$data->party_ledger->customer->customer_id;
						}
					
						if($salesInvoicedata->discount_percentage>0)
						{
						   $totrate=$salesInvoicedata->quantity*$salesInvoicedata->rate;
						   $dis=$totrate*100/$salesInvoicedata->discount_percentage;
						}
						else{
						   $dis=0;
						}
						$totalDiscount+=$dis;
						
						if($data->total_igst=='' || $data->total_igst==0)
						{
							$gst=$salesInvoicedata->gst_value/2;
							$cgtax=$salesInvoicedata->gst_figure->tax_percentage/2;
							$cgst=$gst;
							$sgst=$gst;
							$igst=0;
							$itax=0;
						}
						else
						{
							$cgst=0;
							$sgst=0;
							$igst=$salesInvoicedata->gst_value;
							$itax=$salesInvoicedata->gst_figure->tax_percentage;
							$cgtax=0;
						}
						$totalCgst+=$cgst;
						$totalSgst+=$sgst;
						$totalIgst+=$igst;
						$totalNet+=$salesInvoicedata->net_amount;
					?>
					<tr>
					<td><?=$customerCode?></td>
					<td><?=$customerName?></td>
					<td><?=$data->voucher_no?></td>
					<td><?=$data->transaction_date?></td>
					<td><?=$salesInvoicedata->item->hsn_code?></td>
					<td><?=$salesInvoicedata->item->item_code?></td>
					<td><?=$salesInvoicedata->item->name?></td>
					<td><?=$salesInvoicedata->quantity?></td>
					<td><?=$salesInvoicedata->rate?></td>
					<td><?=$salesInvoicedata->discount_percentage?></td>
					<td><?=$dis?></td>
					<td><?=$cgtax.'%'?></td>
					<td><?=$cgst?></td>
					<td><?=$cgtax.'%'?></td>
					<td><?=$sgst?></td>
					<td><?=$itax.'%'?></td>
					<td><?=$igst?></td>
					<td><?=$salesInvoicedata->net_amount?></td>
					</tr>
					<?php }}?>
					<tr>
					<td colspan="10" align="right"><b>Total</b></td>
					<td><b><?=$totalDiscount?></b></td>
					<td></td>
					<td><b><?=$totalCgst?></b></td>
					<td></td>
					<td><b><?=$totalSgst?></b></td>
					<td></td>
					<td><b><?=$totalIgst?></b></td>
					<td><b><?=$totalNet?></b></td>
					</tr>
					</tbody>
					</table>
					<?php } else { ?>
					<?php echo '<b>No Invoice Found from '.$from.' - '.$to.'</b>';?>
					<?php } ?>
</div>
</div>
</div>					
</div>
