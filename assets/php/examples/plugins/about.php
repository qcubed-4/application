<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions" class="full">
	<h1>Introduction to the Library Ecosystem</h1>

	<p>QCubed was built on a principle that the core distribution should be
		lightweight and extensible. A part of that vision is an easy-to-use
		library infrastructure. The library system has gone through a number of
		significant changes in an attempt to make a system that is flexible, but also
		compatible with future changes of the core. (Even a name change, libraries used to be called "plugins".)</p>

	<p>The current library architecture relies on <a href="http://getcomposer.org">Composer</a> for installation.
		Once you have Composer installed and working, installing libraries is a simple
		matter of executing the Composer "require" command. For example, to install the QCubed Bootstrap library,
        you would execute the following:</p>
    <code>
        composer require qcubed/bootstrap
    </code>


       <p>Once a library is installed, you can use Composer to
		monitor for updates to the libraries, and automatically install those updates. Simly execute
           <code>composer update</code> and all your libraries will be updated.
		See the <a href="https://www.github.com/qcubed/">QCubed Github</a> page for a
		list of composer installable libraries. </p>

	<p>Composer installs all files in a <strong>/vendor</strong> directory. It includes an autoloader,
		so you can immediately use library classes once they are installed, without the
		need to use <strong>include</strong> statements to include them. </p>

	<p>QCubed includes a <a
			href="../../_devtools/plugin_manager.php">Library Manager</a>
		component that lists out the libraries you have installed, and lets you
		access the example code included with them.</p>

</div>

<style>#viewSource { display: none; }</style>

<?php require('../includes/footer.inc.php'); ?>