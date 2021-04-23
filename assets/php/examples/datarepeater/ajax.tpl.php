<?php require('../includes/header.inc.php'); ?>
	<?php $this->renderBegin(); ?>

	<div id="instructions">
		<h1>The DataRepeater</h1>

		<p>The main difference between a <strong>DataGrid</strong> and a <strong>DataRepeater</strong> is that while a
		<strong>DataGrid</strong> is in a table
		and has structure to help define how that table should be rendered, a <strong>DataRepeater</strong>
		lets you define your own structure for each item.  You have a few different ways to do this:
		<ul>
			<li>Specify a template file which will be rendered for each item visible.</li>
			<li>Subclass the DataRepeater object and override the <strong>GetItemHtml</strong> method, or the
				<strong>GetItemAttributes</strong> and <strong>GetItemInnerHtml</strong> methods.</li>
			<li>Provide rendering callbacks, either with the <strong>ItemHtmlCallback</strong>, or the combination of the
				<strong>ItemAttributesCallback</strong> and  <strong>ItemInnerHtmlCallback</strong> attributes.</li>
		</ul>
		<p>The <strong>DataRepeaters</strong> each have a <strong>Paginator</strong> defined with them. Note that
		because the <strong>Paginator</strong> is rendered by the <i>form</i> (as opposed to the example
		with <strong>DataGrid</strong> where the <i>datagrid</i> rendered the paginator), we will set the <i>form</i>
		as the paginator's parent.</p>

		<p>Also, note that DataRepeater allows you to set <i>two</i> paginators: a <strong>Paginator</strong> and a
		<strong>PaginatorAlternate</strong>.  This is to offer listing pages which have the paginator at the
		top and at the bottom of the page.

		<p>The variables <strong>$_FORM</strong>, <strong>$_CONTROL</strong> and <strong>$_ITEM</strong> are pre-defined
		for your template, and are set to the current <strong>Form</strong>, the <strong>DataRepeater</strong> object, and the data source item currently
			being drawn.</p>
	</div>

	<div id="demoZone">
		<div style="border:solid 1px gray">
			<?php $this->dtrPersons->Paginator->render(); ?>

			<?php $this->dtrPersons->render(); ?>

			<?php $this->dtrPersons->PaginatorAlternate->render(); ?>
		</div>
		<br />
		<div style="border:solid 1px gray">
			<?php $this->dtrBig->Paginator->render(); ?>
			<?php $this->dtrBig->render(); ?>
		</div>

	</div>

	<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>