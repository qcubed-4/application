<?php use QCubed\Database\Service;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>SQL functions and math operations for QQuery</h1>

    <p>At times, you may need to create database queries which retrieve
        values which are calculated values or custom SQL values. In QCubed-4, we call these <strong>virtual attributes</strong>.
        Each virtual attribute must be given a name and a definition. The definition can be taken from a query built with
        QQ operations, a custom sub-SQL clause, or a fully custom SQL statement.
    </p>

	<p>This example demonstrates using virtual attributes to define values created with QQ operations.
        The standard SQL math operations included are: </p>
	<ul>
		<li><strong>QQ::Add</strong> for addition (+),</li>
		<li><strong>QQ::Sub</strong> for subtraction (-),</li>
		<li><strong>QQ::Mul</strong> for multiplication (*),</li>
		<li><strong>QQ::Div</strong> for division (/),</li>
		<li><strong>QQ::Neg</strong> the unary negative (-),</li>
	</ul>

	<p>You can also use <strong>QQ::MathOp</strong> to apply any math operator that your particular flavor of SQL might
		provide. For example, you can use QQ::MathOp to execute a bitwise shift operation ("&lt;&lt;") if you are using
		MySQL or Postgres, even though that operator is not included in the SQL standard.
	</p>


	<p>Similarly, you can get the results of standard SQL functions, like:
	<ul>
	<li><strong>QQ::Abs</strong> for the absolute value (ABS), </li>
	<li><strong>QQ::Ceil</strong> for the smallest integer value not less than the argument (CEIL), </li>
	<li><strong>QQ::Floor</strong> for the largest integer value not greater than the argument (FLOOR), </li>
	<li><strong>QQ::Mod</strong> for the remainder (MOD), </li>
	<li><strong>QQ::Power</strong> for the argument raised to the specified power (POWER), </li>
	<li><strong>QQ::Sqrt</strong> for the square root of the argument (SQRT), </li>
	</ul>
	<p>And, you can use <strong>QQ::Func</strong> to get the results of any SQL function that your particular flavor of
	SQL provides.</p>
</div>

<div id="demoZone">
	<h2>Select names of project managers whose projects are over budget by at least $20</h2>
<?php

	Person::getDatabase()->enableProfiling();
	$objPersonArray = Person::queryArray(
		/* Only return the persons who have AT LEAST ONE overdue project */
		QQ::greaterThan(QQ::sub(QQN::person()->ProjectAsManager->Spent, QQN::person()->ProjectAsManager->Budget), 20)
	);

	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>
	<p><?php Person::getDatabase()->outputProfiling(); ?></p>

	<h2>The same as above, but also create a calculated virtual field and sort with it</h2>
<?php

	Person::getDatabase()->enableProfiling();
	$objPersonArray = Person::QueryArray(
		/* Only return the persons who have AT LEAST ONE overdue project */
		QQ::GreaterThan(
			QQ::Virtual('diff', QQ::Sub(
				QQN::Person()->ProjectAsManager->Spent
				, QQN::Person()->ProjectAsManager->Budget
			))
			, 20
		),
		QQ::Clause(
			/* The most overdue first */
			QQ::OrderBy(QQ::Virtual('diff'), 'DESC')
			/* Required to access this field with GetVirtualAttribute */
			, QQ::Expand(QQ::Virtual('diff'))
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName) . ' : ' . $objPerson->GetVirtualAttribute('diff');
		_p('<br/>', false);
	}
?>
	<p><?php Person::getDatabase()->outputProfiling(); ?></p>

	<h2>The same as above and filter out most of the other fields by using a Select clause</h2>
	<p>This also demonstrates how to use the \QCubed\Query\QQ::MathOp and \QCubed\Query\QQ::Neg functions. </p>
<?php

	Service::getDatabase(1)->EnableProfiling();
	$objPersonArray = Person::QueryArray(
		/* Only return the persons who have AT LEAST ONE overdue project */
		/* Note below we are adding a negative. This is for demo purposes. We could have just used \QCubed\Query\QQ: Subs above. */
		QQ::GreaterThan(
			QQ::Virtual('diff', QQ::MathOp(
				'+', // Note the plus operation sign here
				QQN::Person()->ProjectAsManager->Spent
				, QQ::Neg(QQN::Person()->ProjectAsManager->Budget)
			))
			, 20
		),
		QQ::Clause(
			/* The most overdue first */
			QQ::OrderBy(QQ::Virtual('diff'), 'DESC')
			/* Required to access this field with GetVirtualAttribute */
			, QQ::Expand(QQ::Virtual('diff'))
			, QQ::Select(array(
				QQ::Virtual('diff')
				, QQN::Person()->FirstName
				, QQN::Person()->LastName
			))
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName) . ' : ' . $objPerson->GetVirtualAttribute('diff');
		_p('<br/>', false);
	}
?>
	<p><?php Service::getDatabase(1)->OutputProfiling(); ?></p>

	<h2>SQL Function Example</h2>
	<p>Use the \QCubed\Query\QQ::Abs and \QCubed\Query\QQ::Sub functions to retrieve projects both over-budget and under-budget by $20.</p>
<?php

	Service::getDatabase(1)->EnableProfiling();
	$objPersonArray = Person::QueryArray(
		/* Only return the persons who have AT LEAST ONE overdue project */
		QQ::GreaterThan(
			QQ::Virtual('absdiff', QQ::Abs(
				QQ::Sub(
					QQN::Person()->ProjectAsManager->Spent
					, QQN::Person()->ProjectAsManager->Budget
				)
			))
			, 20
		),
		QQ::Clause(
			/* The most over budget first */
			QQ::OrderBy(QQ::Virtual('absdiff'), 'DESC')
			/* Required to access this field with GetVirtualAttribute */
			, QQ::Expand(QQ::Virtual('absdiff'))
			, QQ::Select(array(
				QQ::Virtual('absdiff')
				, QQN::Person()->FirstName
				, QQN::Person()->LastName
			))
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName) . ' : ' . $objPerson->GetVirtualAttribute('diff');
		_p('<br/>', false);
	}
?>
	<p><?php Service::getDatabase(1)->OutputProfiling(); ?></p>
</div>

<?php require('../includes/footer.inc.php'); ?>