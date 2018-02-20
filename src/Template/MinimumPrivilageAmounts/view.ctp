<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\MinimumPrivilageAmount $minimumPrivilageAmount
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Minimum Privilage Amount'), ['action' => 'edit', $minimumPrivilageAmount->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Minimum Privilage Amount'), ['action' => 'delete', $minimumPrivilageAmount->id], ['confirm' => __('Are you sure you want to delete # {0}?', $minimumPrivilageAmount->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Minimum Privilage Amounts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Minimum Privilage Amount'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="minimumPrivilageAmounts view large-9 medium-8 columns content">
    <h3><?= h($minimumPrivilageAmount->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $minimumPrivilageAmount->has('company') ? $this->Html->link($minimumPrivilageAmount->company->name, ['controller' => 'Companies', 'action' => 'view', $minimumPrivilageAmount->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($minimumPrivilageAmount->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Amount') ?></th>
            <td><?= $this->Number->format($minimumPrivilageAmount->amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created On') ?></th>
            <td><?= h($minimumPrivilageAmount->created_on) ?></td>
        </tr>
    </table>
</div>
