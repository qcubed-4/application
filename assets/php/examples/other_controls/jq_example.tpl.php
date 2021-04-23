<?php require('../includes/header.inc.php'); ?>
	<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>jQuery UI Controls</h1>
	
	<p>QCubed includes a library of controls that implement the JQuery UI set of javascript interactive controls found
        at <a href="http://www.jquery.com/ui">jQuery UI</a> .
       </p>
	
	<p>Explore the variety of these controls on this page, and proceed to the <a href="js_return_param_example.php">next tutorial</a> 
	to learn how to attach events to these controls and use them in your Forms.</p>

    <p>QCubed comes with a default JQuery UI theme, but you can use JQuery UI Themeroller to create your own and point to
    it in your assets.cfg.php file.</p>



</div>

<div id="demoZone">
	<style type="text/css">
		.example { border: 1px solid #dedede; margin: 10px; padding: 10px;}
		.draggable, .resizable { background-color: #780000; color: #fff; cursor:move; height: 50px; padding: 10px; width: 100px; }
		.droppable { background-color: #333; color: #fff; height: 80px; width: 150px; }
		.selitem, .sortitem { background-color: #f6f6f6; border: 1px solid #dedede; margin: 3px; padding: 10px; width: 150px;}
		.selectable, .sortable { color: #333; background-color: #f6f6f6; width: 250px; padding: 10px; }
		.selectable .ui-selecting { background: #fff; color: #333; }
		.selectable .ui-selected { background: #780000; color: #fff; }
	</style>
	
	<div class="example"><h2>Draggable</h2>
		<?php $this->Draggable->render(); ?>
	</div>
	
	<div class="example"><h2>Droppable</h2>
		<?php $this->Droppable->render(); ?>
	</div>
	
	<div class="example"><h2>Resizable</h2>
		<?php $this->Resizable->render(); ?>
	</div>
	
	<div class="example"><h2>Selectable</h2>
		<p>Drag a box (aka lasso) with the mouse over the items.
			Items can be selected by click or drag while holding the Ctrl/Meta key, 
			allowing for multiple (non-contiguous) selections.</p>
		<?php $this->Selectable->render(); ?>
	</div>
	
	<div class="example"><h2>Sortable</h2>
		<p>Drag and drop to reorder</p>
		<?php $this->Sortable->render(); ?>
	</div>
	
	<div class="example"><h2>Accordion</h2>
		<?php $this->Accordion->render(); ?>
	</div>
	
	<div class="example"><h2>Autocomplete</h2>
		 <p>Type "c" to test</p>
		<?php $this->Autocomplete->renderWithName(); ?>
	</div>
	
	<div class="example"><h2>Ajax Autocomplete</h2>
		 <p>Type "s" to test</p>
			<?php $this->AjaxAutocomplete->renderWithName(); ?>
		<p>See the Autocomplete2 QCubed plugin for additional extensions to the Autocomplete control. </p>
	</div>
	
	<div class="example"><h2>Buttons</h2>
		<?php $this->Button->render(); ?>
		<?php $this->CheckBox->render(); ?>
		<?php $this->RadioButton->render(); ?>
		<?php $this->IconButton->render(); ?>
	</div>
	
	<div class="example"><h2>Lists</h2>
		<?php $this->CheckList1->renderWithName(); ?>
		<?php $this->CheckList2->renderWithName(); ?>
		<?php $this->RadioList1->renderWithName(); ?>
		<?php $this->RadioList2->renderWithName(); ?>
		<?php $this->SelectMenu->renderWithName(); ?>
	</div>
	
	<div class="example"><h2>Datepicker</h2>
		<?php $this->Datepicker->render(); ?>
	</div>
	
	<div class="example"><h2>DatepickerBox</h2>
		<?php $this->DatepickerBox->render(); ?>
	</div>
	
	<div class="example"><h2>Dialog box - floating..</h2>
        <?php $this->Dialog->render(); ?>
        <?php $this->btnShowDialog->render(); ?>
        <?php $this->txtDlgTitle->renderWithName(); ?>
        <?php $this->txtDlgText->renderWithName(); ?>

	</div>
	
	<div class="example"><h2>Progressbar</h2>
		<?php $this->Progressbar->render(); ?>
	</div>
	
	<div class="example"><h2>Slider</h2>
		<p><?php $this->Slider->render(); ?></p>
		<p><?php $this->Slider2->render(); ?></p>
	</div>
	
	<div class="example"><h2>Tabs</h2>
		<?php $this->Tabs->render(); ?>
	</div>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>