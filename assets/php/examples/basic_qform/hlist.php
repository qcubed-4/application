<?php

use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\FormBase;
use QCubed\Control\HList;
use QCubed\Control\HListItem;
use QCubed\Css\OrderedListType;
use QCubed\Css\UnorderedListStyleType;
use QCubed\Exception\Caller;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{
    protected HList $lstProjects;

    // Initialize our Controls during the Form Creation process

    /**
     * @throws Caller
     */
    protected function formCreate(): void
    {
        // Define the ListBox and create the first list item as 'Select One'
        $this->lstProjects = new HList($this);
        $this->lstProjects->setDataBinder(array($this, 'lstProjects_Bind'));
        $this->lstProjects->UnorderedListStyle = UnorderedListStyleType::Square;

    }

    /**
     * Populates the list of projects with their associated team members.
     * This method binds data from the Project model to the list box with nested person entries.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function lstProjects_Bind(): void
    {
        $clauses[] = QQ::expandAsArray(QQN::project()->PersonAsTeamMember);
        $objProjects = Project::queryArray(QQ::all(), $clauses);

        foreach ($objProjects as $objProject) {
            $item = new HListItem ($objProject->Name);
            $item->Tag = 'ol';
            $item->getSubTagStyler()->OrderedListType = OrderedListType::LowercaseRoman;
            foreach ($objProject->_PersonAsTeamMemberArray as $objPerson) {
                /****
                 * Here we add a subitem to each item before adding the item to the main list.
                 */
                $item->addItem($objPerson->FirstName . ' ' . $objPerson->LastName);
            }
            $this->lstProjects->addItem($item);
        }
    }

}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine looks for the file intro.tpl.php to use as the HTML template file.
ExamplesForm::run('ExamplesForm');
