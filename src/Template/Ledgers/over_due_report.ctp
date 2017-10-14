<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Overdue Report');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Overdue Report</span>
				</div>
			</div>
			<div class="portlet-body">
			<div class="row">
			<?= $this->Form->create('overdue',['type' => 'get']) ?>
				<div class="col-md-12">
					<div class="col-md-3">
						<div class="form-group">
							<?php echo $this->Form->control('run_time_date',['class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy', 'label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','value'=>date('d-m-Y'),'required'=>'required']); ?>
						</div>
					</div>
					<div class="col-md-2">
						<?= $this->Form->button(__('Go'),['class'=>'btn btn-success submit']) ?>
					</div>
				</div>
			<?= $this->Form->end() ?>	
			</div>
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col"> Transaction Date </th>
								<th scope="col">Reference Name</th>
								<th scope="col">Party</th>
								<th scope="col">Due Balance</th>
								<th scope="col">Days</th>
							</tr>
						</thead>
						<tbody><?php $sno = 1; 
								  foreach ($reference_details as $reference_detail):
									$duebalance = $reference_detail->total_debit - $reference_detail->total_credit;
									if($duebalance > 0)
									{ ?>
										<tr>
											<td><?php echo $reference_detail->sales_invoice->transaction_date; ?></td>
											<td><?php echo $reference_detail->ref_name; ?></td>
											<td><?php echo $reference_detail->ledger->name; ?></td>
											<td><?php echo $duebalance;  ?></td>
											<td><?php $ref_date = date('Y-m-d',strtotime($reference_detail->sales_invoice->transaction_date));	
											
											$ref_date_create =  date_create($ref_date);
											$run_time_date_create =  date_create($run_time_date);
											
											$diff=date_diff($run_time_date_create,$ref_date_create);
											echo $diff->format("%R%a days");
											?></td>
										</tr>
							  <?php } endforeach; ?>
						</tbody>
					</table>
				</div>				
			</div>
		</div>
	</div>
</div>



<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>