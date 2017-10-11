<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\ContraVoucher $contraVoucher
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Contra Voucher'), ['action' => 'edit', $contraVoucher->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Contra Voucher'), ['action' => 'delete', $contraVoucher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $contraVoucher->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Contra Vouchers'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Contra Voucher'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="contraVouchers view large-9 medium-8 columns content">
    <h3><?= h($contraVoucher->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Reference No') ?></th>
            <td><?= h($contraVoucher->reference_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $contraVoucher->has('company') ? $this->Html->link($contraVoucher->company->name, ['controller' => 'Companies', 'action' => 'view', $contraVoucher->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($contraVoucher->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= $this->Number->format($contraVoucher->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Credit Amount') ?></th>
            <td><?= $this->Number->format($contraVoucher->total_credit_amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Debit Amount') ?></th>
            <td><?= $this->Number->format($contraVoucher->total_debit_amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($contraVoucher->transaction_date) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Narration') ?></h4>
        <?= $this->Text->autoParagraph(h($contraVoucher->narration)); ?>
    </div>
</div>
