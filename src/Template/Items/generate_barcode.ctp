<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Generate Barcode');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Generate Barcode</span>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($item) ?>
				<div class="row">
					<div class="col-md-4">
					    <div class="form-group">
							<label>All Same </label>
							<?php echo $this->Form->input('check', ['type'=>'checkbox','label' => false,'class' => 'form-control input-medium check']); ?>
						</div>	
					</div>
				</div>
				<div class="row">
					<?php	for($i=0; $i<10;$i++){ ?>
					<div class="col-md-4">
					    <div class="form-group">
							<label>Row <?php echo ' '.$i+1; ?></label>
							<?php echo $this->Form->input('item_name[]', ['empty'=>'---Select---','options'=>$itemOptions,'label' => false,'class' => 'form-control input-medium select2me item','required'=>'required']); ?>
						</div>	
						
					</div>
					<?php
					}
					?>
				</div>
				<?= $this->Form->button(__('Go'),['class'=>'btn btn-success']) ?>
				<?= $this->Form->end() ?>
			</div>
		</div>
	</div>
</div>

<!-- BEGIN PAGE LEVEL STYLES -->
	<!-- BEGIN COMPONENTS PICKERS -->

	<?php echo $this->Html->css('/assets/global/plugins/select2/select2.css', ['block' => 'PAGE_LEVEL_CSS']); ?>
	

	<?php echo $this->Html->script('/assets/global/plugins/select2/select2.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	
	
<?php
$js="
$(document).ready(function() {
	
	$('.item').die().live('change',function() 
		{
			var item=$(this).val();
			
			if($('.check').attr('checked'))
			{
				alert();
				$('.item').val(item);
				$('.item').select2();
				
			}
		});
	
});
";

echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom'));
?>