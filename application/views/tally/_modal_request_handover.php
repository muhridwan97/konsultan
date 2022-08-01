<div class="modal fade" style="overflow:hidden;" role="dialog" id="modal-request-handover">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">

                <div class="modal-header">
                    <h4 class="modal-title">Request Handover</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to request handover
                        <strong id="job-title"></strong>?
                    </p>
                    <br>
                    <div class="row">
                        <div class="col-md-2" >
                            <div class="form-group">
                            <label for="handover_name">Handover Users</label>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="input-group col-xs-12">
                                    <select class="form-control select2" data-key-id="id" required data-key-label="handover_user_id"  name="handover_user_id" id="handover_user_id" data-placeholder="Select User" style="width: 100%;">
                                        <option value=""></option>   
                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>   
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">Request</button>
                </div>
            </form>
        </div>
    </div>
</div>