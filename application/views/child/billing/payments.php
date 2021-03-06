<?php $this->load->view('children/nav'); ?>
<div class="row">
	<?php $this->load->view('children/accounting/invoice_nav'); ?>
	<div class="col-lg-10 col-md-10 col-sm-10">
		<div class="card">
			<div class="card-header">
				<div class="card-title"><?php echo lang('payments_header'); ?></div>
			</div>
			<div class="card-body">
				<table class="table table-stripped">
					<th><?php echo lang('invoice'); ?></th>
					<th><?php echo lang('amount'); ?></th>
					<th><?php echo lang('date'); ?></th>
					<th><?php echo lang('method'); ?></th>
					<th><?php echo lang('notes'); ?></th>
					<?php foreach($payments->result() as $row): ?>
						<tr>
							<td>
								<?php echo anchor('invoice/view/' . $row->invoice_id, $row->invoice_id); ?>
							</td>
							<td>
								<?php echo session('currency_symbol') . ' ' . $row->amount_paid; ?>
							</td>
							<td>
								<?php echo $row->date_paid; ?>
							</td>
							<td>
								<?php echo $this->invoice->pay_method($row->method); ?>
							</td>
							<td>
								<?php echo $row->remarks; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
</div>