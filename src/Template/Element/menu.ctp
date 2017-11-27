<?php

if(!isset($active_menu))
{
    $active_menu = '';
}
?>
<?php 
echo '<li>'.$this->Html->link($this->Html->tag('i', '', ['class' => 'icon-home']).'Dashboard', '/Users/Dashboard',['escape' => false]).'</li>';
?>
<li class="start ">
	<a href="javascript:;">
	<i class="fa fa-info-circle"></i>
	<span class="title">Sales Invoice</span>
	<span class="arrow "></span>
	</a>
	<ul class="sub-menu">
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/SalesInvoices/Add',['escape' => false]); ?></li>
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/SalesInvoices',['escape' => false]); ?></li>
	</ul>
</li>
<li class="start ">
	<a href="javascript:;">

	<i class="fa fa-retweet"></i>
	<span class="title">Sales Return</span>
	<span class="arrow "></span>
	</a>
	<ul class="sub-menu">
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/SalesInvoices/saleReturnIndex',['escape' => false]); ?></li>
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/SaleReturns',['escape' => false]); ?></li>
	</ul>
</li>
<li class="start ">
	<a href="javascript:;">
	<i class="fa fa-tasks"></i>
	<span class="title">GRN</span>
	<span class="arrow "></span>
	</a>
	<ul class="sub-menu">
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/Grns/Add',['escape' => false]); ?></li>
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/Grns',['escape' => false]); ?></li>
	</ul>
</li>
<li class="start ">
	<a href="javascript:;">

	<i class="fa fa-paragraph"></i>
	<span class="title">Purchase Invoice</span>
	<span class="arrow "></span>
	</a>
	<ul class="sub-menu">
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/Grns/PurchaseInvoiceAdd',['escape' => false]); ?></li>
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/PurchaseInvoices',['escape' => false]); ?></li>
	</ul>
</li>
<li class="start ">
	<a href="javascript:;">

	<i class="fa fa-pied-piper"></i>
	<span class="title">Purchase Return</span>
	<span class="arrow "></span>
	</a>
	<ul class="sub-menu">
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-plus-square']).' Create', '/PurchaseInvoices/PurchaseInvoiceReturn',['escape' => false]); ?></li>
		<li><?php echo $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-list-ul']).' List', '/PurchaseReturns',['escape' => false]); ?></li>
	</ul>
</li>


<?php 
echo '<li>'.$this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-barcode']).'Generate Barcode', '/Items/generateBarcode',['escape' => false]).'</li>';
?>
<?php 
echo '<li>'.$this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-folder-open']).'Reports', '/Users/reports',['escape' => false]).'</li>';
?>
<?php 
echo '<li>'.$this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-bank']).'Vouchers', '/Users/voucherSetup',['escape' => false]).'</li>';
?>
<?php 
echo '<li>'.$this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-cab']).'Masters & Setup', '/Users/masterSetup',['escape' => false]).'</li>';
?>

<?php 
echo '<li>'.$this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-lock']).'Logout', '/Users/logout',['escape' => false]).'</li>';
?>