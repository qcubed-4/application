<?php use QCubed\QString;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
    <h1>QQ and Reverse Relationships</h1>

    <p>The power of QCubed-4 ORM is the ability not just to code generate the code to handle foreign key
        relationships, but also the ability to have that code handle the "reverse" foreign key relationships.
        So in the Example Site data model, we're talking about not just a <strong>Project</strong> and its <strong>ManagerPerson</strong>
        property... but we're also talking about a Person and methods like <strong>getProjectAsManagerArray()</strong>.</p>

    <p><strong>QCubed-4 Query</strong> also has this built-in capability, which works very similarly to the way <strong>QQ</strong> handles Associations.
        And this should make sense—from <strong>Person's</strong> point of view, it has a "-to-Many" relationship with <strong>Project</strong> as a Manager
        (via the reverse relationship), and it has a "-to-Many" relationship with <strong>Project</strong> as a Team Member (via the
        association table). Therefore, <strong>QQ</strong> has the ability to perform the full set of <strong>QQ</strong> functionality
        (including conditions, expansions, ordering, grouping, etc.) on tables related via these reverse relationships
        just as it would on tables related via a direct foreign key or association table.</p>

    <p>The naming standards for the relationship as well as the differences between <strong>expand()</strong> vs. <strong>expandAsArray()</strong>
        are all the exact same as the case with association tables.</p>
</div>

<div id="demoZone">

    <h2>Get All People, Specifying the Project They Manage (if any), for Projects that have 'ACME' or 'HR' in it</h2>
    <p><em>Notice how some people may be listed twice if they manage more than one project.</em></p>
    <ul>
<?php
$objPersonArray = Person::queryArray(
    QQ::orCondition(
        QQ::like(QQN::person()->ProjectAsManager->Name, '%ACME%'),
        QQ::like(QQN::person()->ProjectAsManager->Name, '%HR%')
    ),
    // Let's expand on the Project, itself
    QQ::clause(
        QQ::expand(QQN::person()->ProjectAsManager),
        QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)
    )
);

foreach ($objPersonArray as $objPerson) {
    printf('<li>%s %s (managing the "%s" project)</li>',
        QString::htmlEntities($objPerson->FirstName),
        QString::htmlEntities($objPerson->LastName),
        QString::htmlEntities($objPerson->_ProjectAsManager->Name), false);
}
?>
    </ul>
    <h3>Same as above, but this time, use expandAsArray()</h3>
    <em>Notice how each person is only listed once... but each person has an internal/virtual <strong>_ProjectAsManagerArray</strong> which may list more than one project.</em></p>
    <ul>
<?php
$objPersonArray = Person::queryArray(
    QQ::orCondition(
        QQ::like(QQN::person()->ProjectAsManager->Name, '%ACME%'),
        QQ::like(QQN::person()->ProjectAsManager->Name, '%HR%')
    ),
    // Let's expandasarray on the Project, itself
    QQ::clause(
        QQ::expandAsArray(QQN::person()->ProjectAsManager),
        QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)
    )
);

foreach ($objPersonArray as $objPerson) {
    _p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName, false);

    // Now, instead of using the _ProjectAsManager virtual attribute, we will use
    // the _ProjectAsManagerArray virtual attribute, which gives us an array of Project objects
    $strProjectNameArray = array();
    foreach ($objPerson->_ProjectAsManagerArray as $objProject){
        $strProjectNameArray[] = QString::htmlEntities($objProject->Name);
    }
    printf(' via: %s</li>', implode(', ', $strProjectNameArray));
}
?>
    </ul>
</div>

<?php require('../includes/footer.inc.php'); ?>