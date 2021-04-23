<?php require('../includes/header.inc.php'); ?>
	<?php $this->renderBegin(); ?>

	<style type="text/css">
		.image_canvas {border-width: 4px; border-style: solid; border-color: #a9f;}
	</style>

	<div id="instructions">
		<h1>What time is it?</h1>

        <p>
		QCubed includes several simple Controls that assist with user input of dates and datetimes.
        </p>

        <p>
		<strong>DateTimePicker</strong> is the "default" control, in that the templates for ModelConnectors for tables with date
		or datetime columns will, by default, generate <strong>DateTimePicker</strong> instances.  While not "sexy" or glamourous by
		any stretch of the imagination, it offers an immense amount of utility, in that it allows for very distinct
		control over date, time and datetime components.  By contrast, the DHTML-based <strong>Calendar</strong> control offers, by definition,
		no support for any time-based component.
        </p>

        <p>
		<strong>DateTimeTextBox</strong> allows for textbox-based input of date and datetime values, utilizing DateTime's constructor
		to parse a wide number of date and datetime formats.
        </p>

		<p>
            <strong>Calendar</strong> is a jQuery-based visual calendar picker control.
        </p>

        <p>
            See also the JQuery UI Datepicker also included with QCubed.
        </p>
	</div>

<div id="demoZone">
	<div style="margin: 10px 0; background: #f6f6f6; border:1px solid #dedede; border-radius: 3px; display: inline-block; padding: 10px;">
		<?php $this->lblResult->render('HtmlEntities=false'); ?>
	</div>
	<div class="ui-helper-clearfix" style="margin-bottom: 20px;">
		<div style="float: left;">
			<strong>DateTimeTextBox</strong><br/>
			<?php $this->dtxDateTimeTextBox->render(); ?>
			<?php $this->btnDateTimeTextBox->render(); ?>
		</div>
		<div style="float: left; margin-left: 45px;">
			<strong>Calendar</strong><br/>
			<?php $this->calQJQCalendar->render(); ?>
			<?php $this->btnQJQCalendar->render(); ?>
		</div>
	</div>
	<div class="ui-helper-clearfix">
		<div style="float: left;">
			<strong>DateTimePicker</strong> (Date only)<br/>
			<?php $this->dtpDatePicker->render(); ?>
			<?php $this->btnDatePicker->render(); ?>
		</div>
		<div style="float: left; margin-left: 45px;">
			<strong>DateTimePicker</strong> (Date and Time)<br/>
			<?php $this->dtpDateTimePicker->render(); ?>
			<?php $this->btnDateTimePicker->render(); ?>
		</div>
	</div>
</div>

	<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>