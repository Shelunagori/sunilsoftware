<div class="row">
	<div class="col-md-9">
		<div class="portlet light ">
			<div class="portlet-body">
			<div class="row">
				<div class="col-md-12">
						<div class="col-md-3">
							<?php echo $this->Form->control('from',['placeholder'=>'Date From','class'=>'form-control input-sm date-picker from','data-date-format'=>'dd-mm-yyyy','label'=>false,'type'=>'text','value'=>@$from]); ?>
						</div>
						<div class="col-md-3">
							<?php echo $this->Form->control('to',['placeholder'=>'Date To','class'=>'form-control input-sm date-picker go','data-date-format'=>'dd-mm-yyyy','label'=>false,'type'=>'text','value'=>@$to]); ?>
						</div>
						
						<div class="col-md-2">
							<?= $this->Form->button($this->Html->tag('i', '', ['class'=>'fa fa-search']) . __(' Go'),['class'=>'btn btn-success']); ?>
						</div>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>