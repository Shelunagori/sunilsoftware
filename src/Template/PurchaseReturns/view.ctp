<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PurchaseReturn $purchaseReturn
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Purchase Return'), ['action' => 'edit', $purchaseReturn->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Purchase Return'), ['action' => 'delete', $purchaseReturn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $purchaseReturn->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Returns'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Return'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Invoices'), ['controller' => 'PurchaseInvoices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Invoice'), ['controller' => 'PurchaseInvoices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Return Rows'), ['controller' => 'PurchaseReturnRows', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Return Row'), ['controller' => 'PurchaseReturnRows', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="purchaseReturns view large-9 medium-8 columns content">
    <h3><?= h($purchaseReturn->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Purchase Invoice') ?></th>
            <td><?= $purchaseReturn->has('purchase_invoice') ? $this->Html->link($purchaseReturn->purchase_invoice->id, ['controller' => 'PurchaseInvoices', 'action' => 'view', $purchaseReturn->purchase_invoice->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= h($purchaseReturn->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $purchaseReturn->has('company') ? $this->Html->link($purchaseReturn->company->name, ['controller' => 'Companies', 'action' => 'view', $purchaseReturn->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($purchaseReturn->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Amount') ?></th>
            <td><?= $this->Number->format($purchaseReturn->total_amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($purchaseReturn->transaction_date) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Purchase Return Rows') ?></h4>
        <?php if (!empty($purchaseReturn->purchase_return_rows)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Purchase Return Id') ?></th>
                <th scope="col"><?= __('Item Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Discount Percentage') ?></th>
                <th scope="col"><?= __('Discount Amount') ?></th>
                <th scope="col"><?= __('Pnf Percentage') ?></th>
                <th scope="col"><?= __('Pnf Amount') ?></th>
                <th scope="col"><?= __('Taxable Value') ?></th>
                <th scope="col"><?= __('Item Gst Figure Id') ?></th>
                <th scope="col"><?= __('Gst Value') ?></th>
                <th scope="col"><?= __('Round Off') ?></th>
                <th scope="col"><?= __('Net Amount') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($purchaseReturn->purchase_return_rows as $purchaseReturnRows): ?>
            <tr>
                <td><?= h($purchaseReturnRows->id) ?></td>
                <td><?= h($purchaseReturnRows->purchase_return_id) ?></td>
                <td><?= h($purchaseReturnRows->item_id) ?></td>
                <td><?= h($purchaseReturnRows->quantity) ?></td>
                <td><?= h($purchaseReturnRows->discount_percentage) ?></td>
                <td><?= h($purchaseReturnRows->discount_amount) ?></td>
                <td><?= h($purchaseReturnRows->pnf_percentage) ?></td>
                <td><?= h($purchaseReturnRows->pnf_amount) ?></td>
                <td><?= h($purchaseReturnRows->taxable_value) ?></td>
                <td><?= h($purchaseReturnRows->item_gst_figure_id) ?></td>
                <td><?= h($purchaseReturnRows->gst_value) ?></td>
                <td><?= h($purchaseReturnRows->round_off) ?></td>
                <td><?= h($purchaseReturnRows->net_amount) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'PurchaseReturnRows', 'action' => 'view', $purchaseReturnRows->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'PurchaseReturnRows', 'action' => 'edit', $purchaseReturnRows->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'PurchaseReturnRows', 'action' => 'delete', $purchaseReturnRows->id], ['confirm' => __('Are you sure you want to delete # {0}?', $purchaseReturnRows->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
