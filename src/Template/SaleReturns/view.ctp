<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\SaleReturn $saleReturn
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Sale Return'), ['action' => 'edit', $saleReturn->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Sale Return'), ['action' => 'delete', $saleReturn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saleReturn->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Sale Returns'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sale Return'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customers'), ['controller' => 'Customers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customer'), ['controller' => 'Customers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Sales Ledgers'), ['controller' => 'Ledgers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sales Ledger'), ['controller' => 'Ledgers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Locations'), ['controller' => 'Locations', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Location'), ['controller' => 'Locations', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Sales Invoices'), ['controller' => 'SalesInvoices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sales Invoice'), ['controller' => 'SalesInvoices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Sale Return Rows'), ['controller' => 'SaleReturnRows', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Sale Return Row'), ['controller' => 'SaleReturnRows', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="saleReturns view large-9 medium-8 columns content">
    <h3><?= h($saleReturn->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $saleReturn->has('company') ? $this->Html->link($saleReturn->company->name, ['controller' => 'Companies', 'action' => 'view', $saleReturn->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Customer') ?></th>
            <td><?= $saleReturn->has('customer') ? $this->Html->link($saleReturn->customer->name, ['controller' => 'Customers', 'action' => 'view', $saleReturn->customer->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sales Ledger') ?></th>
            <td><?= $saleReturn->has('sales_ledger') ? $this->Html->link($saleReturn->sales_ledger->name, ['controller' => 'Ledgers', 'action' => 'view', $saleReturn->sales_ledger->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Party Ledger') ?></th>
            <td><?= $saleReturn->has('party_ledger') ? $this->Html->link($saleReturn->party_ledger->name, ['controller' => 'Ledgers', 'action' => 'view', $saleReturn->party_ledger->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Location') ?></th>
            <td><?= $saleReturn->has('location') ? $this->Html->link($saleReturn->location->name, ['controller' => 'Locations', 'action' => 'view', $saleReturn->location->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sales Invoice') ?></th>
            <td><?= $saleReturn->has('sales_invoice') ? $this->Html->link($saleReturn->sales_invoice->id, ['controller' => 'SalesInvoices', 'action' => 'view', $saleReturn->sales_invoice->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($saleReturn->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= $this->Number->format($saleReturn->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount Before Tax') ?></th>
            <td><?= $this->Number->format($saleReturn->amount_before_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Cgst') ?></th>
            <td><?= $this->Number->format($saleReturn->total_cgst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Sgst') ?></th>
            <td><?= $this->Number->format($saleReturn->total_sgst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Igst') ?></th>
            <td><?= $this->Number->format($saleReturn->total_igst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount After Tax') ?></th>
            <td><?= $this->Number->format($saleReturn->amount_after_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Round Off') ?></th>
            <td><?= $this->Number->format($saleReturn->round_off) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($saleReturn->transaction_date) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Sale Return Rows') ?></h4>
        <?php if (!empty($saleReturn->sale_return_rows)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Sale Return Id') ?></th>
                <th scope="col"><?= __('Item Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Rate') ?></th>
                <th scope="col"><?= __('Discount Percentage') ?></th>
                <th scope="col"><?= __('Taxable Value') ?></th>
                <th scope="col"><?= __('Net Amount') ?></th>
                <th scope="col"><?= __('Gst Figure Id') ?></th>
                <th scope="col"><?= __('Gst Value') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($saleReturn->sale_return_rows as $saleReturnRows): ?>
            <tr>
                <td><?= h($saleReturnRows->id) ?></td>
                <td><?= h($saleReturnRows->sale_return_id) ?></td>
                <td><?= h($saleReturnRows->item_id) ?></td>
                <td><?= h($saleReturnRows->quantity) ?></td>
                <td><?= h($saleReturnRows->rate) ?></td>
                <td><?= h($saleReturnRows->discount_percentage) ?></td>
                <td><?= h($saleReturnRows->taxable_value) ?></td>
                <td><?= h($saleReturnRows->net_amount) ?></td>
                <td><?= h($saleReturnRows->gst_figure_id) ?></td>
                <td><?= h($saleReturnRows->gst_value) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'SaleReturnRows', 'action' => 'view', $saleReturnRows->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'SaleReturnRows', 'action' => 'edit', $saleReturnRows->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'SaleReturnRows', 'action' => 'delete', $saleReturnRows->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saleReturnRows->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
