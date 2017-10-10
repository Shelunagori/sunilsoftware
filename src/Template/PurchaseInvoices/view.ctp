<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PurchaseInvoice $purchaseInvoice
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Purchase Invoice'), ['action' => 'edit', $purchaseInvoice->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Purchase Invoice'), ['action' => 'delete', $purchaseInvoice->id], ['confirm' => __('Are you sure you want to delete # {0}?', $purchaseInvoice->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Invoices'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Invoice'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Supplier Ledgers'), ['controller' => 'Ledgers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Supplier Ledger'), ['controller' => 'Ledgers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Invoice Rows'), ['controller' => 'PurchaseInvoiceRows', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Invoice Row'), ['controller' => 'PurchaseInvoiceRows', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="purchaseInvoices view large-9 medium-8 columns content">
    <h3><?= h($purchaseInvoice->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $purchaseInvoice->has('company') ? $this->Html->link($purchaseInvoice->company->name, ['controller' => 'Companies', 'action' => 'view', $purchaseInvoice->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Supplier Ledger') ?></th>
            <td><?= $purchaseInvoice->has('supplier_ledger') ? $this->Html->link($purchaseInvoice->supplier_ledger->name, ['controller' => 'Ledgers', 'action' => 'view', $purchaseInvoice->supplier_ledger->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount Before Tax') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->amount_before_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Cgst') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->total_cgst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Sgst') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->total_sgst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Igst') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->total_igst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount After Tax') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->amount_after_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Round Off') ?></th>
            <td><?= $this->Number->format($purchaseInvoice->round_off) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($purchaseInvoice->transaction_date) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Purchase Invoice Rows') ?></h4>
        <?php if (!empty($purchaseInvoice->purchase_invoice_rows)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Purchase Invoice Id') ?></th>
                <th scope="col"><?= __('Item Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Rate') ?></th>
                <th scope="col"><?= __('Discount Percentage') ?></th>
                <th scope="col"><?= __('Discount Amount') ?></th>
                <th scope="col"><?= __('Pnf Percentage') ?></th>
                <th scope="col"><?= __('Pnf Amount') ?></th>
                <th scope="col"><?= __('Taxable Value') ?></th>
                <th scope="col"><?= __('Net Amount') ?></th>
                <th scope="col"><?= __('Gst Figure Id') ?></th>
                <th scope="col"><?= __('Gst Value') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($purchaseInvoice->purchase_invoice_rows as $purchaseInvoiceRows): ?>
            <tr>
                <td><?= h($purchaseInvoiceRows->id) ?></td>
                <td><?= h($purchaseInvoiceRows->purchase_invoice_id) ?></td>
                <td><?= h($purchaseInvoiceRows->item_id) ?></td>
                <td><?= h($purchaseInvoiceRows->quantity) ?></td>
                <td><?= h($purchaseInvoiceRows->rate) ?></td>
                <td><?= h($purchaseInvoiceRows->discount_percentage) ?></td>
                <td><?= h($purchaseInvoiceRows->discount_amount) ?></td>
                <td><?= h($purchaseInvoiceRows->pnf_percentage) ?></td>
                <td><?= h($purchaseInvoiceRows->pnf_amount) ?></td>
                <td><?= h($purchaseInvoiceRows->taxable_value) ?></td>
                <td><?= h($purchaseInvoiceRows->net_amount) ?></td>
                <td><?= h($purchaseInvoiceRows->gst_figure_id) ?></td>
                <td><?= h($purchaseInvoiceRows->gst_value) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'PurchaseInvoiceRows', 'action' => 'view', $purchaseInvoiceRows->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'PurchaseInvoiceRows', 'action' => 'edit', $purchaseInvoiceRows->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'PurchaseInvoiceRows', 'action' => 'delete', $purchaseInvoiceRows->id], ['confirm' => __('Are you sure you want to delete # {0}?', $purchaseInvoiceRows->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
