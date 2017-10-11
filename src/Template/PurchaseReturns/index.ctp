<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PurchaseReturn[]|\Cake\Collection\CollectionInterface $purchaseReturns
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Purchase Return'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Purchase Invoices'), ['controller' => 'PurchaseInvoices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Purchase Invoice'), ['controller' => 'PurchaseInvoices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Purchase Return Rows'), ['controller' => 'PurchaseReturnRows', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Purchase Return Row'), ['controller' => 'PurchaseReturnRows', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="purchaseReturns index large-9 medium-8 columns content">
    <h3><?= __('Purchase Returns') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('purchase_invoice_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('voucher_no') ?></th>
                <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('transaction_date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('total_amount') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchaseReturns as $purchaseReturn): ?>
            <tr>
                <td><?= $this->Number->format($purchaseReturn->id) ?></td>
                <td><?= $purchaseReturn->has('purchase_invoice') ? $this->Html->link($purchaseReturn->purchase_invoice->id, ['controller' => 'PurchaseInvoices', 'action' => 'view', $purchaseReturn->purchase_invoice->id]) : '' ?></td>
                <td><?= h($purchaseReturn->voucher_no) ?></td>
                <td><?= $purchaseReturn->has('company') ? $this->Html->link($purchaseReturn->company->name, ['controller' => 'Companies', 'action' => 'view', $purchaseReturn->company->id]) : '' ?></td>
                <td><?= h($purchaseReturn->transaction_date) ?></td>
                <td><?= $this->Number->format($purchaseReturn->total_amount) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $purchaseReturn->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $purchaseReturn->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $purchaseReturn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $purchaseReturn->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
