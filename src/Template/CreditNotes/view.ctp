<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Credit Note View');
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
	<div class="col-md-9">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Credit Note View</span>
				</div>
			</div>
			<div class="portlet-body table-responsive">
				<table class="table" width="100%">
					<thead>
				
					<tr>
						<th scope="col" style="text-align:center";>Voucher No: 
						<?= h(str_pad($creditNote->voucher_no, 4, '0', STR_PAD_LEFT)) ?></th>
						<th scope="col" style="text-align:center";>Company: <?=$creditNote->company->name?></th>
						<th scope="col" style="text-align:center";>Transaction Date: <?=date('d-m-y',strtotime($creditNote->transaction_date))?></th>
						<th scope="col" style="text-align:center";>Narration: <?=$creditNote->narration?></th>
					</tr>
						
						</thead>
				</table>
				
				<table class="table table-bordered table-hover table-condensed" width="100%">
					<thead>
					<tr>
						<th scope="col" style="text-align:center">Cr/Dr</th>
						<th scope="col" style="text-align:center">Ledger</th>
						<th scope="col" style="text-align:center">Debit</th>
						<th scope="col" style="text-align:center">Credit</th>
						<th scope="col" style="text-align:center">Mode of Payment</th>
						<th scope="col" style="text-align:center">Cheque No</th>
						<th scope="col" style="text-align:center">Cheque Date</th>
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
					
					foreach($creditNote->credit_note_rows as $credit_note_row)
					{
					?>
					<tr>
					<td style="text-align:center"><?=$credit_note_row->cr_dr?></td>
					<td style="text-align:center"><?=$credit_note_row->ledger->name?></td>
					<td class="rightAligntextClass"><?=$credit_note_row->debit?></td>
					<td class="rightAligntextClass"><?=$credit_note_row->credit?></td>
					<td class="" style="text-align:center"><?=$credit_note_row->mode_of_payment?></td>
					<td class="" style="text-align:center"><?=$credit_note_row->cheque_no?></td>
					<td class="" style="text-align:center">
					<?php if($credit_note_row->cheque_date=='000:00:00'){?>
					<?php echo '';?> 
					<?php } else{?>
					<?php echo date('d-m-Y', strtotime($credit_note_row->cheque_date));?>
					<?php }?>
					</td>
					</tr>
					<?php if(!empty($credit_note_row->reference_details)){?>
					<tr><td>&nbsp;</td><td colspan="5">
					<table class="table table-bordered table-condensed" width="50%" align="center" style="background-color:#f1f3fa">
					<thead>
					<tr>
						<th scope="col" style="text-align:center">Type</th>
						<th scope="col" style="text-align:center">Ref Name</th>
						<th scope="col" style="text-align:center">Debit</th>
						<th scope="col" style="text-align:center">Credit</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($credit_note_row->reference_details as $refdata)
					{?>
					<tr>
					<td style="text-align:center"><?=$refdata->type?></td>
					<td style="text-align:center"><?=$refdata->ref_name?></td>
					<td class="rightAligntextClass"><?=$refdata->debit?></td>
					<td class="rightAligntextClass"><?=$refdata->credit?></td>
					</tr>
					<?php }?>
					</tbody>
					</table>
					</td><td>&nbsp;</td></tr>
					<?php }?>
					<?php }?>
					</tbody>
					</table>
			
</div>
</div>
</div>					
</div>
