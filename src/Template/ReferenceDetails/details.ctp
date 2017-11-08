
<div class="row">
	<div class="col-md-9">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Referencial Details View</span>
				</div>
			</div>
			<div class="portlet-body table-responsive">
					<?php if(!empty($referenceDetails)){?>
					<tr><td>&nbsp;</td><td colspan="5">
					<table class="table table-bordered table-condensed" width="50%" align="center" style="">
					<thead>
					<tr>
						<th scope="col" style="text-align:center">Date</th>
						<th scope="col" style="text-align:center">Type</th>
						<th scope="col" style="text-align:center">Ref Name</th>
						<th scope="col" style="text-align:center">Debit</th>
						<th scope="col" style="text-align:center">Credit</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($referenceDetails as $refdata){ ?>
					<tr>
					<td class="rightAligntextClass"><?=date('d-m-Y',strtotime($refdata->transaction_date))?></td>
					<td style="text-align:center"><?=$refdata->type?></td>
					<td style="text-align:center"><?=$refdata->ref_name?></td>
					<td class="rightAligntextClass"><?=$refdata->debit?></td>
					<td class="rightAligntextClass"><?=$refdata->credit?></td>
					</tr>
					<?php }?>
					</tbody>
					</table>
				<?php }?>	
			
			</div>
		</div>
	</div>					
</div>