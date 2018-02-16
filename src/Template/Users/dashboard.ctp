<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */

$this->set('title', 'Dashboard');
?>
<!--
<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat blue-madison">
						<div class="visual">
							<i class="fa fa-comments"></i>
						</div>
						<div class="details">
							<div class="number">
								 <?=$total_invoice ?>
							</div>
							<div class="desc">
								 Total Sales Invoice
							</div>
						</div>
						
						<a class="more" href="../SalesInvoices/">
						Sales Invoice <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat red-intense">
						<div class="visual">
							<i class="fa fa-bar-chart-o"></i>
						</div>
						<div class="details">
							<div class="number">
								<?php echo $this->Money->moneyFormatIndia($total_sale); ?>
							</div>
							<div class="desc">
								 Total Sales
							</div>
						</div>
						<a class="more" href="../SalesInvoices/report?from_date=&to_date=&party_ledger_id=&invoice_no=">
						Sales Report<i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat green-haze">
						<div class="visual">
							<i class="fa fa-shopping-cart"></i>
						</div>
						<div class="details">
							<div class="number">
								<?= $total_invoice1 ?>
								
							</div>
							<div class="desc">
								 Total Purchase Invoices
							</div>
						</div>
						<a class="more" href="../PurchaseInvoices/">
						Purchase Invoices <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				
			</div>
-->