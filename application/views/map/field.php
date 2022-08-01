<?php
$blocks = ["AX", "BX", "CX", "DX"];
$slots = 25;
?>

<?php foreach ($blocks as $block): ?>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Block <?= $block ?></h3>
        </div>

        <div class="box-body">
            <?php for ($i = 1; $i <= $slots; $i++): ?>
                <?php $slot = str_pad($i, 2, "0", STR_PAD_LEFT) ?>
                <p class="lead mb10">
                    <?= $block . "-" . $slot ?>
                </p>
                <table class="table table-bordered table-hover mb20 responsive">
                    <?php for ($x = 5; $x >= 1; $x--): ?>
                        <?php $tier = str_pad($x, 2, "0", STR_PAD_LEFT); ?>
                        <tr>
                            <td style="width: 25px" class="text-center success responsive-hide"><?= $tier ?></td>
                            <?php for ($y = 1; $y <= 9; $y++): ?>
                                <?php
                                $row = str_pad($y, 2, "0", STR_PAD_LEFT);
                                $position = $block . "-" . $slot . "-" . $row . "-" . $tier;
                                $containerNo = '';
                                $fieldClass = '';
                                foreach ($containers as $container) {
                                    if ($container['position'] == $position) {
                                        $fieldClass = 'bg-blue';
                                        $containerNo .= $container['no_container'];
                                        if (!empty($containerNo)) {
                                            $containerNo .= '<br>';
                                        }
                                    }
                                }
                                ?>
                                <td class="text-center <?= $fieldClass ?>" id="<?= $position ?>">
                                    <p class="mb0"><?= $containerNo ?></p>
                                    <small class="<?= empty($containerNo) ? 'text-muted' : '' ?>">
                                        <?= $position ?>
                                    </small>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                    <tfoot>
                    <tr>
                        <td></td>
                        <?php for ($row = 1; $row <= 9; $row++) : ?>
                            <td class="text-center success">
                                <?= str_pad($row, 2, "0", STR_PAD_LEFT) ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    </tfoot>
                </table>
            <?php endfor; ?>
        </div>
    </div>

<?php endforeach; ?>
