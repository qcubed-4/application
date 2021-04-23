<?php
use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Control\Image;
use QCubed\Event\Click;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Table;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    // Declare the DataGrid
    protected $dtgPersons;

    protected function formCreate()
    {
        // Define the DataGrid
        $this->dtgPersons = new Table($this);

        $col = $this->dtgPersons->createCallableColumn('Full Name', [$this, 'renderFullName']);
        $col->HtmlEntities = false;
        $col = $this->dtgPersons->createCallableColumn('Picture', [$this, 'renderImage']);
        $col->HtmlEntities = false;
        $col = $this->dtgPersons->createCallableColumn('', [$this, 'renderButton']);
        $col->HtmlEntities = false;
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');
    }

    public function renderFullName(Person $objPerson)
    {
        return "<em>" . $objPerson->FirstName . "</em> " . $objPerson->LastName;
    }

    public function renderImage(Person $objPerson)
    {
        $intPersonId = $objPerson->Id;
        $objControlId = "personImage" . $intPersonId;

        if (!$objControl = $this->getControl($objControlId)) {
            $objControl = new Image($this->dtgPersons, $objControlId);

            $imagePath = "../images/emoticons/" . $intPersonId . ".png";

            if (file_exists($imagePath)) {
                // Beautiful images are courtesy of Yellow Icon at http://yellowicon.com/downloads/page/4
                $objControl->ImageUrl = $imagePath;
            } else {
                $objControl->ImageUrl = "../images/emoticons/1.png"; // fail-over case: default image
            }
        }

        // We pass the parameter of "false" to make sure the control doesn't render
        // itself RIGHT HERE - that it instead returns its string rendering result.
        return $objControl->render(false);
    }

    public function renderButton(Person $objPerson)
    {
        $objControlId = "editButton" . $objPerson->Id;

        if (!$objControl = $this->getControl($objControlId)) {
            $objControl = new Button($this->dtgPersons, $objControlId);
            $objControl->Text = "Edit Person #" . $objPerson->Id;

            $objControl->addAction(new Click(), new Ajax("renderButton_Click"));
            $objControl->ActionParameter = $objPerson->Id;
        }

        // We pass the parameter of "false" to make sure the control doesn't render
        // itself RIGHT HERE - that it instead returns its string rendering result.
        return $objControl->render(false);
    }

    public function renderButton_Click(ActionParams $params)
    {
        $intPersonId = intval($params->ActionParameter);

        Application::displayAlert("In a real application, you'd be redirected to the page that edits person #" . $intPersonId);

        // You'd do something like this in a real application:
        // \QCubed\Project\Application::redirect("person_edit.php?intPersonId=" . $intPersonId);
    }

    protected function dtgPersons_Bind()
    {
        // We load the data source, and set it to the datagrid's DataSource parameter
        $this->dtgPersons->DataSource = Person::loadAll();
    }
}

ExampleForm::run('ExampleForm');

