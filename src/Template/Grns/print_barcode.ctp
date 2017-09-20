<!DOCTYPE html>
<html>
<head>
	<title>Page Title</title>
	<style type="text/css" media="print">
	@page {
		size: auto;   /* auto is the initial value */
		margin: 0px 0px 0px 0px;  /* this affects the margin in the printer settings */
	}
	.print{
	page-break-after:always;
	}
	</style>
</head>
<body style="margin: 0;padding: 0;">
	<?php 
	$ar=[];
	foreach($grn->grn_rows as $grn_row){
		for($i=0; $i<$grn_row->quantity; $i++){
			$ar[]=$grn_row;
		}
	} 
	foreach($grn->grn_rows as $grn_row){
		for($i=0; $i<$grn_row->quantity; $i++){
			$ar[]=$grn_row;
		}
	} 
	foreach($grn->grn_rows as $grn_row){
		for($i=0; $i<$grn_row->quantity; $i++){
			$ar[]=$grn_row;
		}
	} 
	foreach($grn->grn_rows as $grn_row){
		for($i=0; $i<$grn_row->quantity; $i++){
			$ar[]=$grn_row;
		}
	} 
	foreach($grn->grn_rows as $grn_row){
		for($i=0; $i<$grn_row->quantity; $i++){
			$ar[]=$grn_row;
		}
	} 
	foreach($grn->grn_rows as $grn_row){
		for($i=0; $i<$grn_row->quantity; $i++){
			$ar[]=$grn_row;
		}
	} 
	?>
	
		<?php 
		$r=0; $inc=0;
		foreach($ar as $arData){
			if($inc==0){ echo '<table style="width:793.70px;" class="print">'; }
			if($r==0){ echo '<tr>'; }
			?>
			<td width="198.42px" height="108px" style="font-size:11px;" valign="middle">
				<table width="100%" style="font-size:11px;line-height: 9px;">
					<tr>
						<td colspan="2"><?php echo $coreVariable['company_name']; ?></td>
					</tr>
					<tr>
						<td colspan="2">Item : <?= $arData->item->name ?></td>
					</tr>
					<tr>
						<td>HSN Code : <?= $arData->item->hsn_code.' ' ?></td>
						<td>Shade : <?= @$arData->item->shade->name.' ' ?></td>
					</tr>
					<tr>
						<td>Size : <?= @$arData->item->size->name.' ' ?></td>
						<td>Rs : <?=$arData->item->sales_rate ?></td>
					</tr>
				</table>
				<div align="center"><?= $this->Html->Image('barcode/'.$arData->item->id.'.png',['width'=>'130px;','height'=>'25px','style'=>'width:130px;height:25px;']) ?></div>
			</td>
			<?php
			
			if($r==4){ echo '</tr>'; }
			$r++;
			if($r==4){ $r=0; }
			$inc++;
			if($inc==40){ $inc=0; ?></table><?php }
			
			
		} ?>
	

</body>
</html>


