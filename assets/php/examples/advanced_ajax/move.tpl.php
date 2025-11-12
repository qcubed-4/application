<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Making a Control Moveable</h1>

	<p>Here we demonstrate the moveable controls capability of QCubed, also known as 
		"Drag and Drop." All dragging, dropping and resizing capabilities are implemented
		through an interface to jQuery UI. Seeing the examples and reviewing the documentation
		on <strong>Draggable</strong>, <strong>Droppable</strong> and <strong>Resizable</strong> at the <a href="http://jqueryui.com/">jQuery UI Web</a> site
		will help you understand more about these capabilities.</p>

    <p>Any <strong>control</strong> can be moved simply by setting the <strong>Moveable</strong> property of the control.
        Controls can also be used as "move handles."
        A "move handle" is anything that can be clicked to start a movement.
        For example, in a typical graphical user interface (such as Windows
        or macOS), you can't simply click on a window to move it. You can only click on its <strong>title bar</strong> to move it.
        So, while the window itself is the object that can be moved, the <strong>title bar</strong> of the window is the "move handle."
        And in this case, the "move handle" is directed at moving both the window itself and the window it is attached to.</p>

    <p>In this example, we define a simple <strong>panel</strong> and make it moveable.
        We also have a <strong>textbox</strong> that is bound to a move handle.
        If we simply made the textbox moveable, we would no longer be able to click on it and change the text in the box.</p>

	<p>When you make a control Moveable, you can then access the <strong>DragObj</strong> attribute of
		the control to get access to the <strong>draggable</strong> jQuery UI routines.</p>
</div>

<div id="demoZone">
	<?php $this->pnlPanel->render('Cursor=move', 'BackColor=#f6f6f6', 'Width=130', 'Height=50', 'Padding=10', 'BorderWidth=1'); ?>
	<?php $this->pnlParent->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>