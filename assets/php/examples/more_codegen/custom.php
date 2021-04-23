<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div id="instructions">
		<h1>Implementing Custom Business Logic</h1>
		
		<p>Almost no application can be purely code generated. As you develop your application, you will likely
		have to implement your own custom business rules and functionality.</p>
		
		<p>At the object level, these business rules can be implemented in the custom subclasses. In our
		example, we make up a fictional business rule of <strong>getPrice()</strong> for our <strong>Project</strong>. This <strong>getPrice()</strong>
		method takes in a "discount percentage" parameter, and uses it to recalculate the budget, incorporating the
		discount and adding 8.25% tax.</p>

		<p>Note how we can do this within the custom subclass. Any modifications we make in the custom
		subclass will never be overwritten on subsequent re-generations of the code.</p>
	</div>

<div id="demoZone">
<?php
	// Let's define our Project SubClass

	// Note: Typically, this code would be in the includes/data_objects/Project.class.php
	// but the Project.class.php code has been pulled out and put here for demonstration
	// purposes.
	require(QCUBED_PROJECT_MODEL_GEN_DIR . '/ProjectGen.php');
	class Project extends ProjectGen {
		const TAX_PERCENTAGE = .0825;

		public function __toString() {
		    return $this->Name;
        }

		public function getPrice($fltDiscount) {
			// Note that strBudget is a DECIMAL type
			// Use the bcmath library if you need better precision than float
			$fltPrice = floatval($this->Budget);
			$fltPrice = $fltPrice * (1.0 - $fltDiscount);
			$fltPrice = $fltPrice * (1.0 + Project::TAX_PERCENTAGE);

			return $fltPrice;
		}
	}

	// Let's load a Project object -- let's select the Project with ID #3
	$objProject = Project::load(3);
?>

	<h2>Load a Project Object and Use the New GetPrice Method</h2>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/>
	Project Budget: $<?php _p($objProject->Budget); ?><br/>
	<strong>GetPrice</strong> @ 0% Discount: $<?php _p($objProject->getPrice(0)); ?><br/>
	<strong>GetPrice</strong> @ 10% Discount: $<?php _p($objProject->getPrice(.1)); ?><br/>
</div>

<?php require('../includes/footer.inc.php'); ?>