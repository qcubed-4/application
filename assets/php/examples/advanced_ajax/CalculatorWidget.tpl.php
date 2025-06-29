<?php $this->pnlValueDisplay->render(); ?>
<table>
    <tr>
        <td colspan="3"><?php $this->btnUpdate->render('CssClass=calculator_top_button'); ?> <?php $this->btnCancel->render('CssClass=calculator_top_button'); ?></td>
        <td><?= $this->pxyOperationControl->renderAsButton('/', '/', ['class'=>"calculator_button"]); ?></td>
    </tr>
    <tr>
        <td><?= $this->pxyNumberControl->renderAsButton('7', 7, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyNumberControl->renderAsButton('8', 8, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyNumberControl->renderAsButton('9', 9, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyOperationControl->renderAsButton('*', '*', ['class'=>"calculator_button"]); ?></td>
    </tr>
    <tr>
        <td><?= $this->pxyNumberControl->renderAsButton('4', 4, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyNumberControl->renderAsButton('5', 5, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyNumberControl->renderAsButton('6', 6, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyOperationControl->renderAsButton('-', '-', ['class'=>"calculator_button"]); ?></td>
    </tr>
    <tr>
        <td><?= $this->pxyNumberControl->renderAsButton('1', 1, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyNumberControl->renderAsButton('2', 2, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyNumberControl->renderAsButton('3', 3, ['class'=>"calculator_button"]); ?></td>
        <td><?= $this->pxyOperationControl->renderAsButton('+', '+', ['class'=>"calculator_button"]); ?></td>
    </tr>
    <tr>
        <td><?= $this->pxyNumberControl->renderAsButton('0', 0, ['class'=>"calculator_button"]); ?></td>
        <td><?php $this->btnPoint->Render('CssClass=calculator_button'); ?></td>
        <td><?php $this->btnClear->Render('CssClass=calculator_button'); ?></td>
        <td><?php $this->btnEqual->Render('CssClass=calculator_button'); ?></td>
    </tr>
</table>