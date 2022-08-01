<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Goods Calculator</h3>
    </div>
    <form action="<?= site_url('calculator/convert') ?>" role="form" method="get">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                <label for="goods">Goods</label>
                <select class="form-control select2" id="goods" name="goods" data-placeholder="Select goods" required>
                    <option value=""></option>
                    <?php foreach ($goods as $item): ?>
                        <option value="<?= $item['id'] ?>" <?= set_select('goods', $item['id'], isset($_GET['goods']) ? $_GET['goods'] == $item['id'] : false) ?>>
                            <?= $item['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('goods', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('quantity') == '' ?: 'has-error'; ?>">
                <label for="quantity">Quantity</label>
                <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Amount of goods"
                       required
                       value="<?= set_value('quantity', get_url_param('quantity')) ?>">
                <?= form_error('quantity', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('unit_from') == '' ?: 'has-error'; ?>">
                        <label for="unit_from">Unit From</label>
                        <select class="form-control select2" id="unit_from" name="unit_from" data-placeholder="Select unit">
                            <option value=""></option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?= $unit['id'] ?>" <?= set_select('unit_from', $unit['id'], isset($_GET['unit_from']) ? $_GET['unit_from'] == $unit['id'] : false) ?>>
                                    <?= $unit['unit'] ?> (<?= $unit['description'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('unit_from', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('unit_to') == '' ?: 'has-error'; ?>">
                        <label for="unit_to">Unit To</label>
                        <select class="form-control select2" id="unit_to" name="unit_to" data-placeholder="Convert to unit">
                            <option value=""></option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?= $unit['id'] ?>" <?= set_select('unit_to', $unit['id'], isset($_GET['unit_to']) ? $_GET['unit_to'] == $unit['id'] : false) ?>>
                                    <?= $unit['unit'] ?> (<?= $unit['description'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('unit_to', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Convert Goods</button>
        </div>
    </form>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Conversion Result</h3>
    </div>
    <div class="box-body">
        <div class="form-group <?= form_error('result') == '' ?: 'has-error'; ?>">
            <p class="form-control input-lg" id="result">
                <?php if (isset($result)): ?>
                    <span id="copy-to-clipboard-target"><?= $result ?></span>
                    <?php if (!is_string($result)): ?>
                        <a href="#" class="pull-right" id="btn-copy-to-clipboard">
                            Copy to Clipboard
                        </a>
                    <?php endif ?>
                <?php else: ?>
                    <span class="text-muted">Result of conversion</span>
                <?php endif ?>
            </p>
            <?= form_error('result', '<span class="help-block">', '</span>'); ?>
        </div>
    </div>
</div>