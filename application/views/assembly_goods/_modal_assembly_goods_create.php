<div class="modal fade" role="dialog" id="modal-assembly-goods-create">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" id="form-modal-assembly-goods-create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create New Assembly Goods</h4>
                </div>
                <div class="modal-body">
                    <div class="alert" role="alert" style="display: none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="messages"></div>
                    </div>

                    <div class="form-group <?= form_error('assembly_goods') == '' ?: 'has-error'; ?> assembly-goods-class">
                        <label for="assembly_goods">Assembly goods</label>
                         <select class="form-control select2 select2-ajax select-assembly-goods"
                                data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                data-key-id="id" data-key-label="name"
                                name="assembly_goods" id="assembly_goods"
                                data-placeholder="Select goods" required  style="width: 100%;">
                                <option value=""></option>
                        </select>
                        <?= form_error('name', '<span class="help-block">', '</span>'); ?>
                    </div>  

                    <div class="row">
                        <div class="col-md-6">
                                <label for="goods">Goods</label>
                                <select class="form-control select2 select2-ajax select-goods"
                                        data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                        data-key-id="id" data-key-label="name"
                                        name="id_goods" id="id_goods"
                                        data-placeholder="Select goods" required style="width: 100%;">
                                    <option value=""></option>
                                </select>
                                <?= form_error('goods', '<span class="help-block">', '</span>'); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('qty') == '' ?: 'has-error'; ?>">
                                <label for="qty">Quantity</label>
                                <input type="text" class="form-control" id="qty" name="qty"
                                       placeholder="Enter Quantity"
                                       required maxlength="50" value="<?= set_value('qty') ?>">
                                <?= form_error('qty', '<span class="help-block">', '</span>'); ?>
                            </div>                
                        </div>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Item description"
                                  maxlength="500"><?= set_value('description') ?></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Create New Assembly Goods</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/goods_assembly.js') ?>"></script>