<div class="row">
	<div class="col-md-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-bar-chart font-green-sharp hide"></i>
					<span class="caption-subject font-green-sharp bold ">Inter Location stock Transfer Vouchers</span>
				</div>
			</div>
			<div class="actions"> 
					<input type="text" class="form-control input-sm pull-right" placeholder="Search..." id="search3"  style="width: 200px;">
					<?php if(@$status=='pending' or @$status==''){
						$class1="btn btn-xs blue";
						$class2="btn btn-default";
					}else{
						$class1="btn btn-default";
						$class2="btn btn-xs blue";
					}
					?>
						<?php echo $this->Html->link('Pending',['controller'=>'IntraLocationStockTransferVouchers','action' => 'index/Pending'],['escape'=>false,'class'=>$class1,'style'=>'padding: 1px 5px;']); ?>
						<?php echo $this->Html->link('Approve',['controller'=>'IntraLocationStockTransferVouchers','action' => 'index/Approved'],['escape'=>false,'class'=>$class2,'style'=>'padding: 1px 5px;']); ?>&nbsp;
					<?php  ?>
					
				</div>
			<div class="portlet-body">
				<div class="table-responsive">
					<?php $page_no=$this->Paginator->current('intraLocationStockTransferVoucher'); $page_no=($page_no-1)*20; ?>
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th scope="col" class="actions"><?= __('Sr') ?></th>
								<th scope="col"><?= $this->Paginator->sort('voucher_no') ?></th>
								<th scope="col"><?= $this->Paginator->sort('transaction_date') ?></th>
								<th scope="col"><?= $this->Paginator->sort('Transfer From Location') ?></th>
								<th scope="col"><?= $this->Paginator->sort('Transfer To Location') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i=0; foreach ($intraLocationStockTransferVouchers as $intraLocationStockTransferVoucher): ?>
            <tr>
                <td><?= $this->Number->format($i+1) ?></td>
				<td><?= h('#'.str_pad($intraLocationStockTransferVoucher->voucher_no, 4, '0', STR_PAD_LEFT)) ?></td>
				<td><?= h($intraLocationStockTransferVoucher->transaction_date) ?></td>
                <td><?= h($intraLocationStockTransferVoucher->TransferFromLocations->name) ?></td>
                <td><?= h($intraLocationStockTransferVoucher->TransferToLocations->name) ?></td>
               <td class="actions">
			        <?php
						if($status=='approved' && $intraLocationStockTransferVoucher->transfer_to_location_id==$location_id)
						{
							$view ="viewApproved";
							$edit = "editApproved";
						}
						elseif($status=='pending'){
							$view ="view";
							$edit = "edit";
						}
						if(!empty($view) && !empty($edit)){
					?>
                    <?= $this->Html->link(__('View'), ['action' => @$view, $intraLocationStockTransferVoucher->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => @$edit, $intraLocationStockTransferVoucher->id]) ?>
						<?php  }
					if($status!='approved' && $intraLocationStockTransferVoucher->transfer_to_location_id==$location_id)
					{
					echo  $this->Html->link(__('Approve'), ['action' => 'Approved', $intraLocationStockTransferVoucher->id]); }?>
                </td>
            </tr>
            <?php $i++; endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
