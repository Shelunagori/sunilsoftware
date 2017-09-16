<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Master Setup');
?>
<div class="row">
	<div class="col-md-6">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-cursor font-purple-intense hide"></i>
					<span class="caption-subject font-blue-steel bold ">Stock info.</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-6">
						<span class="caption-subject bold ">Stock Groups</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/StockGroups/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/StockGroups',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-6">
						<span class="caption-subject bold ">Items</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/Items/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/Items',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-6">
						<span class="caption-subject bold ">Shades</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/Shades/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/Shades',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-6">
						<span class="caption-subject bold ">Units</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/Units/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/Units',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-6">
						<span class="caption-subject bold ">Sizes</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square menuCss']).' Create', '/Sizes/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul menuCss']).' List', '/Sizes',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-cursor font-purple-intense hide"></i>
					<span class="caption-subject font-blue-steel bold ">Account info.</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-6">
						<span class="caption-subject bold ">Accounting Groups</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/AccountingGroups/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/AccountingGroups',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-6">
						<span class="caption-subject bold ">Ledgers</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/Ledgers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/Ledgers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-6">
						<span class="caption-subject bold ">Customers</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/Customers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/Customers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
					<div class="col-md-6">
						<span class="caption-subject bold ">Suppliers</span>
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/Suppliers/Add',['escape' => false, 'class'=>'list-group-item']); ?>
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/Suppliers',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



