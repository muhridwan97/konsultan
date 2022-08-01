<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Ownership Histories</h3>
	</div>
	<div class="box-body">
		<table class="table no-datatable" id="table-ownershiphistory">
			<thead>
				<tr>
					<th>No</th>
					<th>Owner</th>
					<th>Changed Date</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php $no=1;
					foreach($ownershiphistories as $ownershiphistory) : ?>
					<tr>
						<td><?= $no++ ?></td>
						<td><?= $ownershiphistory['name'] ?></td>
						<td><?= $ownershiphistory['change_date'] ?></td>
						<td><?= $ownershiphistory['description'] ?></td>
					</tr>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-ownershiphistory">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Delete Ownership</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to delete ownership history 
                        <strong id="ownershiphistory-title"></strong>?</p>
                    <p class="small text-danger">
                        This action will perform soft delete, actual data still exist on database.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete Ownership</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php echo end($ownershiphistories)['change_date']; ?>
<script>
	var dateRangePickerSettingsEnd = {
		singleDatePicker: true,
		timePicker: true,
		timePicker24Hour: true,
		timePickerSeconds: true,
		<?= !is_null(end($ownershiphistories)['change_date']) ? "minDate:'".(new DateTime(end($ownershiphistories)['change_date']))->format('d F Y H:i:s')."'," : "" ?>
		locale: {
			format: 'DD MMMM YYYY HH:mm:ss'
		}
	}
</script>