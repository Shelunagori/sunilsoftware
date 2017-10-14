<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Contra Voucher');
?>
<style>
.noBorder{
	border:none;
}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Contra Voucher</span>
				</div>
				<div class="actions">
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label><b>Voucher No</b> :</label>&nbsp;&nbsp;
							<?= h(str_pad($contraVoucher->voucher_no, 4, '0', STR_PAD_LEFT)) ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label><b>Transaction Date</b> :</label>
							<?php echo $contraVoucher->transaction_date; ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label><b>Reference No</b> :</label>
							<?php echo $contraVoucher->reference_no; ?>
						</div>
					</div>
				</div>
				<div class="row">
						<div class="table-responsive">
							<table id="MainTable" class="table table-condensed table-striped" width="100%">
								<thead>
									<tr>
										<td></td>
										<td>Particulars</td>
										<td>Debit</td>
										<td>Credit</td>
										<td width="10%"></td>
									</tr>
								</thead>
								<tbody id='MainTbody' class="tab">
								<?php
								if(!empty($contraVoucher->contra_voucher_rows))
								{
									$i=0; 
									foreach($contraVoucher->contra_voucher_rows as $contra_voucher_row){	
								?>
									<tr class="MainTr" >
										<td width="10%">
											<?php echo $contra_voucher_row->cr_dr; ?>
										</td>
										<td width="65%">
										<?php echo $contra_voucher_row->ledger->name; 
										?>
											<div class="window" style="margin:auto;">
											<?php
											if(!empty($contra_voucher_row->mode_of_payment)){
											?>
											<table width='90%' class="table table-condensed">
												<tbody>
													<tr>
														<td width="30%">
															<?php 
															echo $contra_voucher_row->mode_of_payment; ?>
														</td>
														<td width="30%">
															<?php echo $contra_voucher_row->cheque_no; ?> 
														</td>
														<td width="30%">
															<?php echo date("d-m-Y",strtotime($contra_voucher_row->cheque_date)); ?>
														</td>
													</tr>
												</tbody>
											</table>
											<?php } ?>
											</div>
										</td>
										<td width="10%">
										<?php if(!empty($contra_voucher_row->debit)){?>
											<?php echo $contra_voucher_row->debit; ?>
										<?php } ?>
										</td>
										<td width="10%">
										<?php if(!empty($contra_voucher_row->credit)){?>
											<?php echo $contra_voucher_row->credit; ?>
										<?php } ?>
										</td>
											<td align="center"  width="10%">
										</td>
									</tr>
								<?php $i++; } } ?>
								</tbody>
								<tfoot>
									<tr style="border-top:double;">
										<td colspan="2" ></td>
										<td><?php echo $contraVoucher->totalMainDr;?></td>
										<td><?php echo $contraVoucher->totalMainCr;?></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label>Narration :</label>
								<?php echo $contraVoucher->narration; ?>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>