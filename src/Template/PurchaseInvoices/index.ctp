<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
 
$this->set('title', 'Purchase Invoice List');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Purchase Invoice</span>
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
			<div class="portlet-body">
				<div class="table-responsive">
					<?php $page_no=$this->Paginator->current('SalesInvoices');
					 $page_no=($page_no-1)*20; ?>
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col"><?= __('Sr') ?></th>
								<th scope="col"><?= $this->Paginator->sort('voucher_no') ?></th>
								<th scope="col"><?= $this->Paginator->sort('grn_voucher_no') ?></th>
								<th scope="col"><?= $this->Paginator->sort('supplier_ledger') ?></th>
								<th scope="col"><?= $this->Paginator->sort('transaction_date') ?></th>
								<th scope="col" class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($purchaseInvoices as $purchaseInvoice): ?>
							<tr>
								<td><?= h(++$page_no) ?></td>
								<td><?= h('#'.str_pad($purchaseInvoice->voucher_no, 4, '0', STR_PAD_LEFT)) ?></td>
								<td><?= h('#'.str_pad($purchaseInvoice->grn->voucher_no, 4, '0', STR_PAD_LEFT)) ?></td>
								<td><?= h($purchaseInvoice->supplier_ledger->name) ?></td>
								<td><?= h($purchaseInvoice->transaction_date) ?></td>
								
								<td class="actions">
									<?= $this->Html->link(__('View '), ['action' => 'view', $purchaseInvoice->id],['escape'=>false,'target'=>'_blank']) ?>
									<?= $this->Html->link(__('Edit'), ['action' => 'edit', $purchaseInvoice->id]) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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