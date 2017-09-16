<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Edit Customer');
?>
<div class="row">
	<div class="col-md-8">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Edit Customer</span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($customer,['onsubmit'=>'return checkValidation()']) ?>
				<div class="row">
					<div class="col-md-12">

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Customer Name <span class="required">*</span></label>
									<?php echo $this->Form->control('name',['class'=>'form-control input-sm','placeholder'=>'Customer Name','label'=>false,'autofocus']); ?>
								</div>
								<div class="form-group">
									<label>PAN</label>
									<?php echo $this->Form->control('pan',['class'=>'form-control input-sm','label'=>false,'placeholder'=>'PAN']); ?>
								</div>
								<div class="form-group">
									<label>Under  Accounting Group <span class="required">*</span></label>
									<?php echo $this->Form->control('accounting_group_id',['class'=>'form-control input-sm select2me','label'=>false,'options' => $accountingGroups,'required'=>'required','value'=>$customer->ledger->accounting_group_id]); ?>
								</div>
								<div class="form-group">
									<label>GSTIN </label>
									<?php echo $this->Form->control('gstin',['class'=>'form-control input-sm gst','placeholder'=>'Eg:22ASDFR0967W6Z5','label'=>false,'autofocus']); ?>
								</div>
								<div class="form-group">
									<label>Mobile </label>
									<?php echo $this->Form->control('mobile',['class'=>'form-control input-sm','placeholder'=>'Mobile no','label'=>false,'autofocus','maxlength'=>10]); ?>
								</div>
								<div class="form-group" style="display:none;">
									<label>Bill to Bill Accounting </label>
									<?php 
									$option =[['value'=>'yes','text'=>'yes'],['value'=>'no','text'=>'no']];
									echo $this->Form->control('bill_to_bill_accounting',['class'=>'form-control input-sm','label'=>false, 'options' => $option,'required'=>'required','value'=>$customer->ledger->bill_to_bill_accounting]); ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>State <span class="required">*</span></label>
									<?php echo $this->Form->control('state_id',['class'=>'form-control input-sm select2me','label'=>false,'empty'=>'-State-', 'options' => $states,'required'=>'required']); ?>
								</div>
								<div class="form-group">
									<label>TAN</label>
									<?php echo $this->Form->control('tan',['class'=>'form-control input-sm','label'=>false,'placeholder'=>'TAN']); ?>
								</div>
								<div class="form-group">
									<label>Email</label>
									<?php echo $this->Form->control('email',['class'=>'form-control input-sm','label'=>false,'placeholder'=>'example@domain.com']); ?>
								</div>
								<div class="form-group">
									<label>Address</label>
									<?php echo $this->Form->control('address',['class'=>'form-control input-sm','label'=>false,'placeholder'=>'Address']); ?>
								</div>
							</div>
					   </div>
					   <div class="row">
							<div class="col-md-3" style="padding-right: 0px;">
								<div class="form-group" >
									<label>Opening balance value</label>
									<?php 
									$value="";
									if(!empty($account_entry->debit))
									{
										$value =$account_entry->debit;
									}
									else
									{
										$value =@$account_entry->credit;
									}
									echo $this->Form->control('opening_balance_value',['class'=>'rightAligntextClass form-control input-sm','label'=>false,'value'=>$value,'placeholder'=>'Opening Balance']);
									?>
								</div>
							</div>
							<div class="col-md-2" style="padding-left: 0px;padding-right:0;">
							    <label style="visibility:hidden;">s</label>
								<?php $option =[['value'=>'Dr','text'=>'Dr'],['value'=>'Cr','text'=>'Cr']];
								    $check="";
								    if(!empty($account_entry->debit))
									{
										$check ='Dr';
									}
									else
									{
										$check ='Cr';
									}
									echo $this->Form->control('debit_credit',['class'=>'form-control input-sm','label'=>false, 'options' => $option,'value'=>@$check]);
									?>

							</div>
						</div>
				   </div>
				</div>
				<?= $this->Form->button(__('Submit'),['class'=>'btn btn-success submit']) ?>
				<?= $this->Form->end() ?>
			</div>
		</div>
	</div>
</div>
<!-- BEGIN PAGE LEVEL STYLES -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->css('/assets/global/plugins/clockface/css/clockface.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<!-- END COMPONENTS PICKERS -->

	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->css('/assets/global/plugins/bootstrap-select/bootstrap-select.min.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<?php echo $this->Html->css('/assets/global/plugins/jquery-multi-select/css/multi-select.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/clockface/js/clockface.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/moment.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<!-- END COMPONENTS PICKERS -->
	
	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->script('/assets/global/plugins/bootstrap-select/bootstrap-select.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<?php echo $this->Html->script('/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<!-- BEGIN COMPONENTS PICKERS -->
	<?php echo $this->Html->script('/assets/admin/pages/scripts/components-pickers.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<!-- END COMPONENTS PICKERS -->

	<!-- BEGIN COMPONENTS DROPDOWNS -->
	<?php echo $this->Html->script('/assets/global/scripts/metronic.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/layout/scripts/layout.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/layout/scripts/quick-sidebar.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/layout/scripts/demo.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<?php echo $this->Html->script('/assets/admin/pages/scripts/components-dropdowns.js', ['block' => 'PAGE_LEVEL_SCRIPTS_JS']); ?>
	<!-- END COMPONENTS DROPDOWNS -->
<!-- END PAGE LEVEL SCRIPTS -->

<?php
	$js="
	$(document).ready(function() {	
		ComponentsPickers.init();
	});	
	function checkValidation()
	{
	        $('.submit').attr('disabled','disabled');
	        $('.submit').text('Submiting...');
    }
	$(document).on('blur', '.gst', function(e)
    { 
		var mdl=$(this).val();
		var numbers = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
		if(mdl.match(numbers))
		{
			
		}
		else
		{
			$(this).val('');
			return false;
		}
    });
	";
	

echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom')); 
?>