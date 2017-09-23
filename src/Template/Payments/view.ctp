<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Payment $payment
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Payment'), ['action' => 'edit', $payment->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Payment'), ['action' => 'delete', $payment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $payment->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Payments'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Payment'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Payment Rows'), ['controller' => 'PaymentRows', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Payment Row'), ['controller' => 'PaymentRows', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="payments view large-9 medium-8 columns content">
    <h3><?= h($payment->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $payment->has('company') ? $this->Html->link($payment->company->name, ['controller' => 'Companies', 'action' => 'view', $payment->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($payment->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= $this->Number->format($payment->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($payment->transaction_date) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Narration') ?></h4>
        <?= $this->Text->autoParagraph(h($payment->narration)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Payment Rows') ?></h4>
        <?php if (!empty($payment->payment_rows)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Payment Id') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col"><?= __('Type') ?></th>
                <th scope="col"><?= __('Ledger Id') ?></th>
                <th scope="col"><?= __('Debit') ?></th>
                <th scope="col"><?= __('Credit') ?></th>
                <th scope="col"><?= __('Mode Of Payment') ?></th>
                <th scope="col"><?= __('Cheque No') ?></th>
                <th scope="col"><?= __('Cheque Date') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($payment->payment_rows as $paymentRows): ?>
            <tr>
                <td><?= h($paymentRows->id) ?></td>
                <td><?= h($paymentRows->payment_id) ?></td>
                <td><?= h($paymentRows->company_id) ?></td>
                <td><?= h($paymentRows->type) ?></td>
                <td><?= h($paymentRows->ledger_id) ?></td>
                <td><?= h($paymentRows->debit) ?></td>
                <td><?= h($paymentRows->credit) ?></td>
                <td><?= h($paymentRows->mode_of_payment) ?></td>
                <td><?= h($paymentRows->cheque_no) ?></td>
                <td><?= h($paymentRows->cheque_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'PaymentRows', 'action' => 'view', $paymentRows->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'PaymentRows', 'action' => 'edit', $paymentRows->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'PaymentRows', 'action' => 'delete', $paymentRows->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paymentRows->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
