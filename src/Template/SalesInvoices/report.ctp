<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Sales Report');
?>
<style>
table th {
    white-space: nowrap;
	font-size:12px !important;
}
table td {
	white-space: nowrap;
	font-size:11px !important;
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
							<th scope="col" colspan="19" style="text-align:left";>Sales Register From <?=$from?> To <?=$to?></th>
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
							<th scope="col" style="text-align:center";>Taxable Value</th>
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
					$totalTaxablevalue=0;
					foreach($salesInvoices->toArray() as $data){
					foreach($data->sales_invoice_rows as $salesInvoicedata)
					{
						$date = date('Y-m-d', strtotime($data->transaction_date));
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
						    $salesInvoicedata->gst_value;
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
						$totalTaxablevalue+=$salesInvoicedata->taxable_value;
					?>
					<tr>
					<td><?=$customerCode?></td>
					<td><?=$customerName?></td>
					<td><?= $field.'/'.$financialyear.'/'. h(str_pad($data->voucher_no, 3, '0', STR_PAD_LEFT))?></td>
					<td><?=$data->transaction_date?></td>
					<td><?=$salesInvoicedata->item->hsn_code?></td>
					<td><?=$salesInvoicedata->item->item_code?></td>
					<td><?=$salesInvoicedata->item->name?></td>
					<td class="rightAligntextClass"><?=$salesInvoicedata->quantity?></td>
					<td class="rightAligntextClass"><?=$salesInvoicedata->rate?></td>
					<td class="rightAligntextClass">
					<?php if($salesInvoicedata->discount_percentage==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $salesInvoicedata->discount_percentage.'%';?><?php }?>
					</td>
					<td class="rightAligntextClass">
					<?php if($dis==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $dis;?><?php }?>
					</td>
					<td class="rightAligntextClass"><?=$salesInvoicedata->taxable_value?></td>
					<td class="rightAligntextClass">
					<?php if($cgtax==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $cgtax.'%';?><?php }?></td>
					<td class="rightAligntextClass">
					<?php if($cgst==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $cgst;?><?php }?>
					</td>
					<td class="rightAligntextClass">
					<?php if($cgtax==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $cgtax.'%';?><?php }?>
					</td>
					<td class="rightAligntextClass">
					<?php if($sgst==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $sgst;?><?php }?>
					</td>
					<td class="rightAligntextClass">
					<?php if($itax==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $itax.'%';?><?php }?>
					</td>
					<td class="rightAligntextClass">
					<?php if($igst==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $igst;?><?php }?>
					</td>
					<td class="rightAligntextClass"><?=$salesInvoicedata->net_amount?></td>
					</tr>
					<?php }}?>
					<tr>
					<td colspan="10" align="right"><b>&nbsp;</b></td>
					<td class="rightAligntextClass"><b>
					<?php if($totalDiscount==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $totalDiscount;?><?php }?>
					</b></td>
					<td class="rightAligntextClass"><b><?=$totalTaxablevalue?></b></td>
					<td></td>
					<td class="rightAligntextClass"><b>
					<?php if($totalCgst==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $totalCgst;?><?php }?>
					</b></td>
					<td></td>
					<td class="rightAligntextClass"><b>
					<?php if($totalSgst==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $totalSgst;?><?php }?>
					</b></td>
					<td></td>
					<td class="rightAligntextClass"><b>
					<?php if($totalIgst==0){?>
					<?php echo '';?> <?php }else{ ?>
					<?php echo $totalIgst;?><?php }?>
					</b></td>
					<td class="rightAligntextClass"><b><?=$totalNet?></b></td>
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
