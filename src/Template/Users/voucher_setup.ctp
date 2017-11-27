<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Voucher Setup');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-cursor font-purple-intense hide"></i>
					<span class="caption-subject font-blue-steel bold ">Vouchers</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-3">
						<span class="caption-subject bold ">Sales Voucher</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/salesVouchers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/salesVouchers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Purchase Voucher</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/PurchaseVouchers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/PurchaseVouchers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Credit Note</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/CreditNotes/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/CreditNotes',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Debit Note</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/DebitNotes/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/DebitNotes',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Receipt Voucher</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/Receipts/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/Receipts',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Payment Voucher</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/Payments/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/Payments',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Journal Voucher</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/JournalVouchers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/JournalVouchers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Contra Voucher</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/ContraVouchers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/ContraVouchers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-3">
						<span class="caption-subject bold ">Stock Journals</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/StockJournals/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/StockJournals',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>



