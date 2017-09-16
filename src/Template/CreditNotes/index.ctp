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
					<span class="caption-subject font-green-sharp bold ">Credit Note List</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col">Sr. No.</th>
								<th scope="col">Voucher No.</th>
								<th scope="col">Transaction Date</th>
								<th scope="col">Party</th>
								<th scope="col">Amount Before Tax</th>
								<th scope="col">Total CGST</th>
								<th scope="col">Total SGST</th>
								<th scope="col">Total IGST</th>
								<th scope="col">net Amount</th>
								<th scope="col" class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php 	$i=0;      
									foreach ($creditNotes as $creditNote): 
									$i++;
							?>
							<tr>
								<td><?= h($i) ?></td>
								<td><?= h($creditNote->voucher_no) ?></td>
								<td><?= h($creditNote->transaction_date) ?></td>
								<td><?= h($creditNote->party_ledger->name) ?></td>
								<td class="rightAligntextClass"><?= $this->Number->format($creditNote->amount_before_tax) ?></td>
								<td class="rightAligntextClass"><?= $this->Number->format($creditNote->total_cgst) ?></td>
								<td class="rightAligntextClass"><?= $this->Number->format($creditNote->total_sgst) ?></td>
								<td class="rightAligntextClass"><?= $this->Number->format($creditNote->total_igst) ?></td>
								<td class="rightAligntextClass"><?= $this->Number->format($creditNote->amount_after_tax) ?></td>
								<td class="actions">
									<?= $this->Html->link(__('Edit'), ['action' => 'edit', $creditNote->id]) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?= $this->Html->link(__('View Bill'), ['action' => 'credit_note_bill', $creditNote->id]) ?>
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