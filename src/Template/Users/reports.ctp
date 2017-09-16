<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Report Setup');
?>
<div class="row">
	<div class="col-md-6">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-cursor font-purple-intense hide"></i>
					<span class="caption-subject font-blue-steel bold ">Reports</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-6">
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => '']).'Trail Balance', '/Ledgers/trial_balance',['escape' => false, 'class'=>'list-group-item']); ?>
							</div>
					</div>
					<div class="col-md-6">
						<div class="list-group">
							<?php echo $this->Html->link($this->Html->tag('i', '', ['class' => '']).'Account Ledger', '/Ledgers/accountLedger',['escape' => false, 'class'=>'list-group-item']); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



