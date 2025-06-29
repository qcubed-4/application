<?php use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\QString;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div id="instructions">
		<h1>Implementing a Customized loadBy() or loadArrayBy()</h1>

		<p>By using the <strong>instantiateDbResult()</strong> method that is code generated for you in each
		generated class, it is very simple to create your own custom <strong>loadBy()</strong> or <strong>loadArrayBy()</strong>
		method using your own custom SQL. Specify a custom Load query by using a <strong>QCubed-4 Query</strong>, or by writing your
		own SQL statement and passing the results into <strong>InstantiateDbResult</strong>. The code-generated logic will take care
		of the rest, transforming your DB result into an array of that object.</p>

		<p>In our example here, we have a custom load function to get an array of all
		<strong>Project</strong> objects where the budget is over a given amount.  We pass this amount
		as a parameter to <strong>loadArrayByBudgetMinimum()</strong>.</p>
	</div>

<div id="demoZone">
<?php
	// Let's define our Project SubClass

	// Note: Typically, this code would be in includes/data_objects/Project.class.php
	// but the Project.class.php code has been pulled out and put here for demonstration
	// purposes.
	require(QCUBED_PROJECT_MODEL_GEN_DIR . '/' . 'ProjectGen.php');

/**
 * The Project class extends from ProjectGen and represents a project entity within the application.
 *
 * This class includes custom functionality and methods specifically designed to extend or
 * enhance the features provided by the generated base class.
 */
class Project extends ProjectGen {

        public function __toString(): string {
            return $this->Name;
        }

        // Create our Custom Load Method
		// Note that this custom load method is based on the sample LoadArrayBySample that is generated
		// in the Project custom subclass.  Because it utilizes the QCubed-4 Query mechanism,
		// we can easily take full advantage of any \QCubed\Query\QQ Clauses by taking it in as an optional parameter.

        /**
         * Loads an array of Project objects where the Budget is greater than or equal to the specified minimum value.
         *
         * @param float $fltBudgetMinimum The minimum budget value to filter the projects by.
         * @param mixed|null $objOptionalClauses Optional conditions or clauses to apply to the query.
         * @return array An array of Project objects that meet the specified criteria.
         * @throws InvalidCast
         * @throws Caller
         */
        public static function loadArrayByBudgetMinimum(float $fltBudgetMinimum, mixed $objOptionalClauses = null) : array {
			return Project::queryArray(
				QQ::greaterOrEqual(QQN::project()->Budget, $fltBudgetMinimum),
				$objOptionalClauses
			);
		}
	}
?>
	<h2>Load an Array of Projects Where the Budget >= $8,000</h2>
	<ul>
<?php
	// Let's load all Projects > $10,000 in budget
	$objProjectArray = Project::loadArrayByBudgetMinimum(8000);
	foreach ($objProjectArray as $objProject)
		_p('<li>' . QString::htmlEntities($objProject->Name) . ' (Budget: $' . QString::htmlEntities($objProject->Budget) . ')</li>', false);
?>
	</ul>
</div>

<?php require('../includes/footer.inc.php'); ?>