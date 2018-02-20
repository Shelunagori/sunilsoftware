<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Create Minimum Privileges Amount');
?>
<div class="row">
	<div class="col-md-6">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Create Minimum Privileges Amount</span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($minimumPrivilageAmount) ?>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Amount <span class="required">*</span></label>
							<?php echo $this->Form->control('amount',['class'=>'form-control input-sm','placeholder'=>'Minimum Amount','type'=>'text',
							'label'=>false,'autofocus','required']); ?>
						</div>
						<div class="row"><span class="loading"></span>
							<div class="col-md-6">
								<div class="form-group">
									<?= $this->Form->button(__('Submit'),['class'=>'btn btn-success submit']) ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?= $this->Form->end() ?>
			</div>
		</div>
	</div>
</div>

