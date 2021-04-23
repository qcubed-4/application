<?php
use QCubed\Control\HList;
use QCubed\Control\HListItem;
use QCubed\Css\OrderedListType;
use QCubed\Css\UnorderedListStyleType;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends \QCubed\Project\Control\FormBase
{

    protected $lstProjects;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        // Define the ListBox, and create the first listitem as 'Select One'
        $this->lstProjects = new HList($this);
        $this->lstProjects->setDataBinder(array($this, 'lstProjects_Bind'));
        $this->lstProjects->UnorderedListStyle = UnorderedListStyleType::Square;

    }

    /**
     * Add the items to the project list.
     */
    public function lstProjects_Bind()
    {
        $clauses[] = QQ::expandAsArray(QQN::project()->PersonAsTeamMember);
        $objProjects = Project::queryArray(QQ::all(), $clauses);

        foreach ($objProjects as $objProject) {
            $item = new HListItem ($objProject->Name);
            $item->Tag = 'ol';
            $item->getSubTagStyler()->OrderedListType = OrderedListType::LowercaseRoman;
            foreach ($objProject->_PersonAsTeamMemberArray as $objPerson) {
                /****
                 * Here we add a sub-item to each item before adding the item to the main list.
                 */
                $item->addItem($objPerson->FirstName . ' ' . $objPerson->LastName);
            }
            $this->lstProjects->addItem($item);
        }
    }

}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include file
ExamplesForm::run('ExamplesForm');
