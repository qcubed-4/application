<?php

use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Control\CheckboxList;
use QCubed\Control\Label;
use QCubed\Project\Control\ListBox;
use QCubed\Event\Change;
use QCubed\Project\Control\FormBase;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

/**
 * This form demonstrates how to use a ListBox and a CheckboxList
 * in QCubed with AJAX interactions. It displays persons from the database
 * and allows selecting them in different formats.
 */
class ExamplesForm extends FormBase
{
    // UI elements used in the form: a label, two list boxes, and a checkbox list
    protected Label $lblMessage;
    protected ListBox $lstPersons;
    protected ListBox $lstProjectPeople;
    protected CheckboxList $chkPersons;

    /**
     * Initializes all controls on the form.
     */
    protected function formCreate(): void
    {
        // Label used to display the user's current selection
        $this->lblMessage = new Label($this);
        $this->lblMessage->Text = '<None>';

        // First list box: displays all persons from the database
        $this->lstPersons = new ListBox($this);
        $this->lstPersons->addItem('- Select One -', null); // Default placeholder option

        // Load all persons, ordered by last name and first name
        $objPersons = Person::loadAll(QQ::clause(QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)));
        if ($objPersons) {
            foreach ($objPersons as $objPerson) {
                // Display each person in "Last, First" format, with ID as value
                $this->lstPersons->addItem($objPerson->LastName . ', ' . $objPerson->FirstName, $objPerson->Id);
            }
        }

        // Attach an AJAX event handler when the selection changes
        $this->lstPersons->addAction(new Change(), new Ajax('lstPersons_Change'));

        // Second list box: groups persons under their respective projects
        $this->lstProjectPeople = new ListBox($this);
        $this->lstProjectPeople->addItem('- Select a person based on the project', null);

        // Use expandAsArray to load related persons (team members) for each project
        $clauses[] = QQ::expandAsArray(QQN::project()->PersonAsTeamMember);
        $objProjects = Project::queryArray(QQ::all(), $clauses);

        // Build grouped options: each person appears under their project name
        foreach ($objProjects as $objProject) {
            $projectName = $objProject->Name;
            $members = $objProject->_PersonAsTeamMemberArray ?? [];

            foreach ($members as $objPerson) {
                $personName = $objPerson->FirstName . ' ' . $objPerson->LastName;

                $this->lstProjectPeople->addItem(
                    $personName,        // Text shown in the list
                    $objPerson->Id,     // Value of the item
                    false,                      // Not selected by default
                    false,                      // Not disabled
                    $projectName        // Group label (optgroup)
                );
            }
        }

        // AJAX callback when project-based list box value changes
        $this->lstProjectPeople->addAction(new Change(), new Ajax('lstProjectPeople_Change'));

        // Checkbox list: allows selecting multiple persons at once
        $this->chkPersons = new CheckboxList($this);
        if ($objPersons) {
            foreach ($objPersons as $objPerson) {
                // Each checkbox shows "First Last" with the person's ID as value
                $this->chkPersons->addItem($objPerson->FirstName . ' ' . $objPerson->LastName, $objPerson->Id);
            }
        }

        // Display checkboxes in two columns
        $this->chkPersons->RepeatColumns = 2;

        // AJAX callback when any checkbox is selected or unselected
        $this->chkPersons->addAction(new Change(), new Ajax('chkPersons_Change'));
    }

    /**
     * Triggered when a person is selected from the first list box (lstPersons).
     * Displays the selected person's full name and ID in the label.
     */
    protected function lstPersons_Change(ActionParams $params): void
    {
        $intPersonId = intval($this->lstPersons->SelectedValue);
        $objPerson = Person::load($intPersonId);

        if ($intPersonId) {
            $this->lblMessage->Text = sprintf('%s %s, Person ID of %s', $objPerson->FirstName, $objPerson->LastName, $objPerson->Id);
        } else {
            $this->lblMessage->Text = '<None>';
        }
    }

    /**
     * Triggered when a person is selected from the project-based list box.
     * Displays the selected person's name in the label.
     */
    public function lstProjectPeople_Change(ActionParams $params): void
    {
        $personId = $this->lstProjectPeople->SelectedValue;

        if ($personId) {
            $objPerson = Person::load($personId);

            if ($objPerson) {
                $this->lblMessage->Text = 'You chose: ' . $objPerson->FirstName . ' ' . $objPerson->LastName;
            } else {
                $this->lblMessage->Text = 'Person not found!';
            }
        } else {
            $this->lblMessage->Text = 'Please select a person!';
        }
    }

    /**
     * Triggered when checkboxes are toggled in the CheckboxList.
     * Displays a comma-separated list of selected names.
     */
    protected function chkPersons_Change(ActionParams $params): void
    {
        $names = $this->chkPersons->SelectedNames;

        if ($names) {
            $this->lblMessage->Text = implode(", ", $names);
        } else {
            $this->lblMessage->Text = '<None>';
        }
    }
}

// Launch the form — QCubed will automatically look for the template file intro.tpl.php
ExamplesForm::run('ExamplesForm');
