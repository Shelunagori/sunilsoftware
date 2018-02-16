<?php
 $url_excel="/?".$url; 
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */ 
 //pr($reference_details->toArray());
//pr($remaining);exit;
$this->set('title', 'Stock Report');
?>

<?php
	if($status=='excel'){
		$date= date("d-m-Y"); 
	$time=date('h:i:a',time());

	$filename="salesreturn_report_".$date.'_'.$time;
	//$from_date=date('d-m-Y',strtotime($from_date));
	//$to_date=date('d-m-Y',strtotime($to_date));
	
	header ("Expires: 0");
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=".$filename.".xls");
	header ("Content-Description: Generated Report" ); 
	echo '<table border="1"><tr style="font-size:14px;"><td colspan="9" align="center" style="text-align:center;">'.$companies->name .'<br/>' .$companies->address .',<br/>'. $companies->state->name .'</span><br/>
				<span> <i class="fa fa-phone" aria-hidden="true"></i>'.  $companies->phone_no . ' | Mobile : '. $companies->mobile .'<br/> GSTIN NO:'.
				$companies->gstin .'</span></td></tr></table>';
	}

 ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
			<?php if($status!='excel'){ ?>
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Stock Report</span>
				</div>
				<div class="actions">
					<?php echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/ItemLedgers/StockReport/'.@$url_excel.'&status=excel',['class' =>'btn btn-sm green tooltips pull-right','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
				</div>
			</div>
			<div class="portlet-body">
			<form method="get">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
								<label>Stock Group</label>
									<?php echo $this->Form->control('stock_group_id',['class'=>'form-control input-sm select2me stock_group','label'=>false,'empty'=>'-Stock Group-', 'options' => $stockGroups, 'value'=> $stock_group_id]); ?>
								</div>
							</div>
							<div class="col-md-3 " id="account_sub_group_div">
								<div class="form-group">
								<label>Stock Sub Group</label>
									<?php echo $this->Form->control('stock_subgroup_id',['class'=>'form-control input-sm select2me stock_sub_group','label'=>false,'empty'=>'-Stock Group-', 'options' => $stockSubgroups, 'value'=>$stock_sub_group_id ]); ?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>To Date</label>
									<?php echo $this->Form->control('to_date',['class'=>'form-control input-sm date-picker to_date','data-date-format'=>'dd-mm-yyyy', 'label'=>false,'placeholder'=>'DD-MM-YYYY','type'=>'text','data-date-start-date'=>@$coreVariable[fyValidFrom],'data-date-end-date'=>@$coreVariable[fyValidTo],'value'=>date('d-m-Y',strtotime($to_date))]); ?></div>
								</div>
							
							<div class="col-md-2" >
								<div class="form-group" style="padding-top:22px;"> 
									<button type="submit" class="btn btn-xs blue input-sm srch"> Go</button>
								</div>
						</div>
						</div>
				</form>
			<?php } ?>
			<br/>
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" border="1">
						<thead>
							<tr>
							<th> SNo</th>
								<th scope="col"> Particulars </th>
								<th scope="col">Stock Group</th>
								<th scope="col">Size</th>
								<th scope="col">Shade</th>
								<th scope="col">Quantity</th>
								<th scope="col">Sales Rate</th>
								<th scope="col">Purchase Rate</th>
								<th scope="col">Closing Stock Value</th>
								
							</tr>
						</thead>
						<tbody><?php $sno = 1; 
							
								  foreach ($Items as $Item): 
								  //pr($Item); exit;
								 if(@$remaining[$Item->id]>0){
								?>
									<tr >
											<td><?php echo $sno++; ?></td>
											<td><?php echo $Item->name; ?></td>
											<td><?php if(@$Item->stock_group_id) { echo @$Item->stock_group->name; } else{ echo 'Primary'; }?></td>
											<td><?php if(@$Item->size_id) { echo @$Item->size->name; } else { echo '-'; } ?></td>
											<td><?php if(@$Item->shade_id){ echo @$Item->shade->name;  } else { echo '-'; } ?></td>
											<td align="right"><?php echo @$remaining[$Item->id]; ?></td>
											<td align="right"><?php echo @$Item->sales_rate; ?></td>
											<td align="right"><?php echo @$unit_rate[$Item->id]/$remaining[$Item->id]; ?></td>
											<td align="right"><?php echo @$unit_rate[$Item->id]; ?></td><?php
											@$closing_stock+= @$unit_rate[$Item->id]; ?>
										
											
										</tr>
								 <?php  } endforeach ?>
								 <tr><td colspan="8" align="right">Closing Stock</td><td align="right"><?=@$closing_stock ?></td></tr>
								
						</tbody>
					</table>
				</div>				
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
	<!-- BEGIN VALIDATEION -->
	<?php echo $this->Html->script('/assets/global/plugins/jquery-validation/js/jquery.validate.min.js', ['block' => 'PAGE_LEVEL_PLUGINS_JS']); ?>
	<!-- END VALIDATEION -->

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
		
		$('.stock_group').on('change',function() {
			
			$('#account_sub_group_div').html('Loading...');
			var stockGroupId=$(this).val();
			
			var url='".$this->Url->build(['controller'=>'StockGroups','action'=>'stockSubGroup']) ."';
			url=url+'/'+stockGroupId,
			$.ajax({
				url: url,
				type: 'GET',
			}).done(function(response) {
				$('#account_sub_group_div').html(response);
				$('.stock_sub_group').select2();
			});
		});
	ComponentsPickers.init();
	});
	";
echo $this->Html->scriptBlock($js, array('block' => 'scriptBottom'));  ?>
