<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Sales Voucher List');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Sales Voucher</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-responsive">
					<?php $page_no=$this->Paginator->current('Salesvouchers');
					 $page_no=($page_no-1)*20; ?>
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col"><?= __('Sr') ?></th>
								<th scope="col"><?= $this->Paginator->sort('voucher_no') ?></th>
								<th scope="col"><?= $this->Paginator->sort('transaction_date') ?></th>
								<th scope="col"><?= $this->Paginator->sort('reference_no') ?></th>
								<th scope="col" class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($salesVouchers as $salesVoucher): ?>
								<tr>
									<td><?= h(++$page_no) ?></td>
									<td><?= $this->Number->format($salesVoucher->voucher_no) ?></td>
									<td><?= h(date("d-m-Y",strtotime($salesVoucher->transaction_date))) ?></td>
									<td><?= h($salesVoucher->reference_no) ?></td>
									<td class="actions">
										<?= $this->Html->link(__('View'), ['action' => 'view', $salesVoucher->id]) ?>
										<?= $this->Html->link(__('Edit'), ['action' => 'edit', $salesVoucher->id]) ?>
										<?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $salesVoucher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $salesVoucher->id)]) ?>
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