<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\SalesVoucher $salesVoucher
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Sales Voucher'), ['action' => 'edit', $salesVoucher->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Sales Voucher'), ['action' => 'delete', $salesVoucher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $salesVoucher->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Sales Vouchers'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sales Voucher'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Sales Voucher Rows'), ['controller' => 'SalesVoucherRows', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sales Voucher Row'), ['controller' => 'SalesVoucherRows', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="salesVouchers view large-9 medium-8 columns content">
    <h3><?= h($salesVoucher->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $salesVoucher->has('company') ? $this->Html->link($salesVoucher->company->name, ['controller' => 'Companies', 'action' => 'view', $salesVoucher->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($salesVoucher->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= $this->Number->format($salesVoucher->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($salesVoucher->transaction_date) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Narration') ?></h4>
        <?= $this->Text->autoParagraph(h($salesVoucher->narration)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Sales Voucher Rows') ?></h4>
        <?php if (!empty($salesVoucher->sales_voucher_rows)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Sales Voucher Id') ?></th>
                <th scope="col"><?= __('Cr Dr') ?></th>
                <th scope="col"><?= __('Ledger Id') ?></th>
                <th scope="col"><?= __('Debit') ?></th>
                <th scope="col"><?= __('Credit') ?></th>
                <th scope="col"><?= __('Mode Of Payment') ?></th>
                <th scope="col"><?= __('Cheque No') ?></th>
                <th scope="col"><?= __('Cheque Date') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($salesVoucher->sales_voucher_rows as $salesVoucherRows): ?>
            <tr>
                <td><?= h($salesVoucherRows->id) ?></td>
                <td><?= h($salesVoucherRows->sales_voucher_id) ?></td>
                <td><?= h($salesVoucherRows->cr_dr) ?></td>
                <td><?= h($salesVoucherRows->ledger_id) ?></td>
                <td><?= h($salesVoucherRows->debit) ?></td>
                <td><?= h($salesVoucherRows->credit) ?></td>
                <td><?= h($salesVoucherRows->mode_of_payment) ?></td>
                <td><?= h($salesVoucherRows->cheque_no) ?></td>
                <td><?= h($salesVoucherRows->cheque_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'SalesVoucherRows', 'action' => 'view', $salesVoucherRows->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'SalesVoucherRows', 'action' => 'edit', $salesVoucherRows->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'SalesVoucherRows', 'action' => 'delete', $salesVoucherRows->id], ['confirm' => __('Are you sure you want to delete # {0}?', $salesVoucherRows->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
