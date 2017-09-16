<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\CreditNote $creditNote
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Credit Note'), ['action' => 'edit', $creditNote->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Credit Note'), ['action' => 'delete', $creditNote->id], ['confirm' => __('Are you sure you want to delete # {0}?', $creditNote->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Credit Notes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Credit Note'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Party Ledgers'), ['controller' => 'Ledgers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Party Ledger'), ['controller' => 'Ledgers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customers'), ['controller' => 'Customers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customer'), ['controller' => 'Customers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Credit Note Rows'), ['controller' => 'CreditNoteRows', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Credit Note Row'), ['controller' => 'CreditNoteRows', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="creditNotes view large-9 medium-8 columns content">
    <h3><?= h($creditNote->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Sales Invoice No') ?></th>
            <td><?= h($creditNote->sales_invoice_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $creditNote->has('company') ? $this->Html->link($creditNote->company->name, ['controller' => 'Companies', 'action' => 'view', $creditNote->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Party Ledger') ?></th>
            <td><?= $creditNote->has('party_ledger') ? $this->Html->link($creditNote->party_ledger->name, ['controller' => 'Ledgers', 'action' => 'view', $creditNote->party_ledger->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sales Ledger') ?></th>
            <td><?= $creditNote->has('sales_ledger') ? $this->Html->link($creditNote->sales_ledger->name, ['controller' => 'Ledgers', 'action' => 'view', $creditNote->sales_ledger->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($creditNote->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Voucher No') ?></th>
            <td><?= $this->Number->format($creditNote->voucher_no) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount Before Tax') ?></th>
            <td><?= $this->Number->format($creditNote->amount_before_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Cgst') ?></th>
            <td><?= $this->Number->format($creditNote->total_cgst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Sgst') ?></th>
            <td><?= $this->Number->format($creditNote->total_sgst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total Igst') ?></th>
            <td><?= $this->Number->format($creditNote->total_igst) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount After Tax') ?></th>
            <td><?= $this->Number->format($creditNote->amount_after_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Transaction Date') ?></th>
            <td><?= h($creditNote->transaction_date) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Credit Note Rows') ?></h4>
        <?php if (!empty($creditNote->credit_note_rows)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Credit Note Id') ?></th>
                <th scope="col"><?= __('Item Id') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Rate') ?></th>
                <th scope="col"><?= __('Taxable Value') ?></th>
                <th scope="col"><?= __('Gst Figure Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($creditNote->credit_note_rows as $creditNoteRows): ?>
            <tr>
                <td><?= h($creditNoteRows->id) ?></td>
                <td><?= h($creditNoteRows->credit_note_id) ?></td>
                <td><?= h($creditNoteRows->item_id) ?></td>
                <td><?= h($creditNoteRows->quantity) ?></td>
                <td><?= h($creditNoteRows->rate) ?></td>
                <td><?= h($creditNoteRows->taxable_value) ?></td>
                <td><?= h($creditNoteRows->gst_figure_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'CreditNoteRows', 'action' => 'view', $creditNoteRows->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'CreditNoteRows', 'action' => 'edit', $creditNoteRows->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'CreditNoteRows', 'action' => 'delete', $creditNoteRows->id], ['confirm' => __('Are you sure you want to delete # {0}?', $creditNoteRows->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
