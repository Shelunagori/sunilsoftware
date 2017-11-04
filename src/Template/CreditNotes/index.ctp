<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Credit Note Voucher');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Credit Note Voucher</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-responsive">
					<?php $page_no=$this->Paginator->current('creditNotes');
					 $page_no=($page_no-1)*20; ?>
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col"><?= __('Sr') ?></th>
								<th scope="col"><?= $this->Paginator->sort('voucher_no') ?></th>
								<th scope="col"><?= $this->Paginator->sort('transaction_date') ?></th>
								<th scope="col" class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($creditNotes as $credit_note): ?>
								<tr>
									<td><?= h(++$page_no) ?></td>
									<td><?= h(str_pad($credit_note->voucher_no, 4, '0', STR_PAD_LEFT)) ?></td>
									<td><?= h(date("d-m-Y",strtotime($credit_note->transaction_date))) ?></td>
									<td class="actions">
										<?= $this->Html->link(__('View'), ['action' => 'view', $credit_note->id]) ?>
										<?= $this->Html->link(__('Edit'), ['action' => 'edit', $credit_note->id]) ?>
										<!--<?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $credit_note->id], ['confirm' => __('Are you sure you want to delete # {0}?', $credit_note->id)]) ?> -->
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