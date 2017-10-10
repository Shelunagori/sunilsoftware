<?php
/**
 * @Author: PHP Poets IT Solutions Pvt. Ltd.
 */
$this->set('title', 'Journal Voucher');
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
					<span class="caption-subject font-green-sharp bold ">Journal Voucher</span>
				</div>
				<div class="actions">
				</div>
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label><b>Voucher No</b> :</label>&nbsp;&nbsp;
							<?= h('#'.str_pad($journalVoucher->voucher_no, 4, '0', STR_PAD_LEFT)) ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label><b>Transaction Date</b> :</label>
							<?php echo $journalVoucher->transaction_date; ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label><b>Reference No</b> :</label>
							<?php echo $journalVoucher->reference_no; ?>
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
								if(!empty($journalVoucher->journal_voucher_rows))
								{
									$i=0; 
									foreach($journalVoucher->journal_voucher_rows as $journal_voucher_row){	
								?>
									<tr class="MainTr" >
										<td width="10%">
											<?php echo $journal_voucher_row->cr_dr; ?>
										</td>
										<td width="65%">
										<?php echo $journal_voucher_row->ledger->name; 
										?>
											<div class="window" style="margin:auto;">
											<?php
											if(!empty($journal_voucher_row->reference_details)){
											?>
												<table width="90%" class="table table-condensed"><tbody>
												<?php
												    $j=0;$total_amount_dr=0;$total_amount_cr=0;$colspan=0;
												    foreach($journal_voucher_row->reference_details as $reference_detail)
													{
												?>
													<tr>
														<td width="20%">
															<?php 
															echo $reference_detail->type; ?>
														</td>
														
														<td width="">
															<?php echo $reference_detail->type;?>
														</td>
														<td width="20%" style="padding-right:0px;">
															<?php
															$value="";
															$cr_dr="";
															
															if(!empty($reference_detail->debit))
															{
																$value=$reference_detail->debit;
																$total_amount_dr=$total_amount_dr+$reference_detail->debit;
																$cr_dr="Dr";
																$name="debit";
															}
															else
															{
																$value=$reference_detail->credit;
																$total_amount_cr=$total_amount_cr+$reference_detail->credit;
																$cr_dr="Cr";
																$name="credit";
															}

															echo $value; ?>
														</td>
														<td width="10%" style="padding-left:0px;">
															<?php 
															echo $cr_dr; ?>
														</td>
														<td align="center"></td>
													</tr>
													<?php $j++;} 
													if($total_amount_dr>$total_amount_cr)
													{
														$total = $total_amount_dr-$total_amount_cr;
														$type="Dr";
													}
													if($total_amount_dr<$total_amount_cr)
													{
														$total = $total_amount_cr-$total_amount_dr;
														$type="Cr";
													}
													?>
												</tbody>
												<tfoot>
												    <tr class="remove_ref_foot">
														<td colspan="2"></td>
														<td><input type="text" class="form-control input-sm rightAligntextClass total calculation ttl noBorder" readonly value="<?php echo $total;?>"></td>
														<td><?php echo @$type;?></td>
													</tr>
												</tfoot>
												</table>
											<?php } ?>
											<?php
											if(!empty($journal_voucher_row->mode_of_payment)){
											?>
											<table width='90%'>
												<tbody>
													<tr>
														<td width="30%">
															<?php 
															echo $journal_voucher_row->mode_of_payment; ?>
														</td>
														<td width="30%">
															<?php echo $journal_voucher_row->cheque_no; ?> 
														</td>
														<td width="30%">
															<?php echo date("d-m-Y",strtotime($journal_voucher_row->cheque_date)); ?>
														</td>
													</tr>
												</tbody>
												<tfoot>
												<td colspan='4'></td>
												</tfoot>
											</table>
											<?php } ?>
											</div>
										</td>
										<td width="10%">
										<?php if(!empty($journal_voucher_row->debit)){?>
											<?php echo $journal_voucher_row->debit; ?>
										<?php } ?>
										</td>
										<td width="10%">
										<?php if(!empty($journal_voucher_row->credit)){?>
											<?php echo $journal_voucher_row->credit; ?>
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
										<td><?php echo $journalVoucher->total_debit_amount;?></td>
										<td><?php echo $journalVoucher->total_credit_amount;?></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label>Narration :</label>
								<?php echo $journalVoucher->narration; ?>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>