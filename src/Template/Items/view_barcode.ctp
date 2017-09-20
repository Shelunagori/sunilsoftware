<style>
@media print{
	
    .page {
        margin: 0;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        page-break-after: always;
    }
	
	td{
		width:198.42px !important;
		height:112.25px !important;
		padding:4px !important;
	}
	.hidden-print{
		display:none;
	}
}

</style>

<style type="text/css" media="print">
@page {
	width: 21cm;
	height: 29.7cm; 
    size: a4;   /* auto is the initial value */
    margin: 0px 0px 0px 0px;  /* this affects the margin in the printer settings */
}

</style>

<div style="width:100%;" class="maindiv">
<?php echo $this->Html->link('Print',array(),['escape'=>false,'class'=>'hidden-print','style'=>' background-color:blue;  font-size:18px; padding:5px; color:white; cursor:hand;  float: left','onclick'=>'javascript:window.print()();']);
 echo $this->Html->link('Close',['controller'=>'Items','action'=>'generateBarcode'],['escape'=>false,'class'=>'hidden-print','style'=>' background-color:blue;  font-size:18px; padding:5px; color:white; cursor:hand;  float: right']);
?>
	<table  width="100%" border="1px" style="font-size:14px; border-collapse: collapse; margin:5px;" >
	<tbody>
	<?php 
	//pr($item_barcodes);
	//exit;
	
	foreach($item_barcodes as $item){  ?>
		<tr>
		<?php	for($i=1;$i<=4;$i++){ ?>
				<td width="198.42px" height="112.25px" style="padding:4px;">Sunil Textiles</br>Item -<?=  $item->name.' ' ?></br>HSN Code -<?=$item->hsn_code.' ' ?></br><?php if($item->shade_id){ ?> Shade - <?=$item->shade->name.' ' ?><?php }  if($item->size_id){ ?>  Size -<?=$item->size->name.' ' ?></br><?php } ?><?= $this->Html->Image('barcode/'.$item->id.'.png') ?></br>Rs:<?=$item->sales_rate ?></td>
				<?php } ?>
			</tr>
		<?php 
	}
	?>

	</tbody>
	</table>
</div>
