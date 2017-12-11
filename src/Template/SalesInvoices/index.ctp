<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Sales Invoice List');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Sales Invoice</span>
				</div>
				<div class="actions">
				<form method="GET" id="">
					<div class="row">
						<div class="col-md-9">
							<?php echo $this->Form->input('search',['class'=>'form-control input-sm pull-right','label'=>false, 'placeholder'=>'Search','autofocus'=>'autofocus','value'=> @$search]);
							?>
						</div>
						<div class="col-md-1">
							<button type="submit" class="go btn blue-madison input-sm">Go</button>
						</div> 
					</div>
				</form>
				</div>
				</div>
				<div class="actions"> 
					<?php if(@$status==''){
						$class1="btn btn-xs blue";
						$class2="btn btn-default btn-xs";
					}else{
						$class1="btn btn-default btn-xs";
						$class2="btn btn-xs blue";
					}
					?>
						<?php echo $this->Html->link('Open',['controller'=>'SalesInvoices','action' => 'index/'],['escape'=>false,'class'=>$class1,'style'=>'padding: 1px 5px;']); ?>
						<?php echo $this->Html->link('Cancel',['controller'=>'SalesInvoices','action' => 'index/Cancel'],['escape'=>false,'class'=>$class2,'style'=>'padding: 1px 5px;']); ?>&nbsp;
					<?php  ?>
					
			</div>
			
			
			<div class="portlet-body">
				<div class="table-responsive">
					<?php $page_no=$this->Paginator->current('SalesInvoices');
					 $page_no=($page_no-1)*20; 
									?>						
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col"><?= __('Sr') ?></th>
								<th scope="col"><?= $this->Paginator->sort('voucher_no') ?></th>
								<th scope="col"><?= $this->Paginator->sort('party_ledger_id') ?></th>
								<th scope="col"><?= $this->Paginator->sort('sales_ledger_id') ?></th>
								<th scope="col"><?= $this->Paginator->sort('transaction_date') ?></th>
								<th scope="col"><?= $this->Paginator->sort('amount_after_tax') ?></th>
								<th scope="col" class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($salesInvoices as $salesInvoice): ?>
							<tr>
								<td><?= h(++$page_no) ?></td>
								<td>
								<?php $date=date('Y-m-d',strtotime($salesInvoice->transaction_date));
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
								<?= $acronym.'/'.$financialyear.'/'. h(str_pad($salesInvoice->voucher_no, 3, '0', STR_PAD_LEFT))
								?>
		                        </td>
								<td><?= h($salesInvoice->party_ledger->name) ?></td>
								<td><?= h($salesInvoice->sales_ledger->name) ?></td>
								<td><?= h($salesInvoice->transaction_date) ?></td>
								<td class="rightAligntextClass"><?= h($salesInvoice->amount_after_tax) ?></td>
								<td class="actions">

									<?php if (in_array("4", $userPages)){?>
									<?= $this->Html->link(__('Edit'), ['action' => 'edit', $salesInvoice->id]) ?><?php }?>&nbsp;&nbsp;
									<?= $this->Html->link(__('View Bill'), ['action' => 'sales_invoice_bill', $salesInvoice->id],['escape'=>false,'target'=>'_blank']) ?>&nbsp;&nbsp;
									<?php if($salesInvoice->status != 'cancel'){ ?>
									<?= $this->Form->postLink(__('Cancel Bill'), ['action' => 'cancel', $salesInvoice->id], ['style'=>'color:red;','confirm' => __('Are you sure you want to cancel # {0}?',h(str_pad($salesInvoice->voucher_no, 3, '0', STR_PAD_LEFT)))]) ?>
									<?php } ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="paginator">
					<ul class="pagination">
						<?= $this->Paginator->first('<< ' . __('first')) ?>
						<?= $this->Paginator->prev('< ' . __('previous')) ?>
						<?= $this->Paginator->numbers() ?>
						<?= $this->Paginator->next(__('next') . ' >') ?>
						<?= $this->Paginator->last(__('last') . ' >>') ?>
					</ul>
					<p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
				</div>
				
			</div>
		</div>
	</div>
</div>