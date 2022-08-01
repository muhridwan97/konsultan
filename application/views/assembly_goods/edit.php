<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Item</h3>
    </div>
    <form action="<?= site_url('assembly_goods/update/'.$item['id']) ?>" role="form" method="post" id="form-edit-assembly-goods">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('goods') == '' ?: 'has-error'; ?> goods-class">
                <label for="goods">Goods</label>
                 <input type="text" class="form-control" id="goods" name="goods"
                               placeholder="Enter Goods" disabled
                               required maxlength="50" value="<?= set_value('goods', $goods['name']) ?>">
                <?= form_error('goods', '<span class="help-block">', '</span>'); ?>
            </div>

             <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Input Item Assembly Goods</h3>
                </div>
                <div class="box-body">
                    <button class="btn btn-sm btn-success btn-add-record-assembly-goods pull-right" type="button">
                        <i class="fa ion-plus"></i>
                    </button>
                    <div style="padding-right: 40px">
                        <select class="form-control select2 select2-ajax select-assembly-goods"
                                data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                data-key-id="id" data-key-label="name"
                                name="id_goods" id="id_goods"
                                data-placeholder="Select goods" required>
                                <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Assembly Goods Record</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-assembly-goods">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Goods</th>
                        <th>Quantity</th>
                        <th class="text-center" style="width: 30px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="4" class="text-center">No data</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Item</button>
        </div>
    </form>
</div>
<script src="<?= base_url('assets/app/js/goods_assembly.js') ?>"></script>