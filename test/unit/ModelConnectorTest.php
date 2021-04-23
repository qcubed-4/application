<?php
/**
 * ModelConnector tests
 * @package Tests
 */

/**
 *
 * @package Tests
 */
if(!class_exists('Person')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/Person.php';
}
if(!class_exists('Project')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/Project.php';
}
if(!class_exists('Login')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/Login.php';
}
if(!class_exists('Milestone')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/Milestone.php';
}
if(!class_exists('Address')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/Address.php';
}
if(!class_exists('PersonType')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/PersonType.php';
}
if(!class_exists('TwoKey')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/TwoKey.php';
}
if(!class_exists('ProjectStatusType')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/ProjectStatusType.php';
}
if(!class_exists('TypeTest')){
    require_once QCUBED_PROJECT_MODEL_DIR .'/TypeTest.php';
}


require_once(QCUBED_PROJECT_MODELCONNECTOR_DIR .'/TypeTestConnector.php');
require_once(QCUBED_PROJECT_MODELCONNECTOR_DIR .'/ProjectConnector.php');
require_once(QCUBED_PROJECT_MODELCONNECTOR_DIR .'/AddressConnector.php');
require_once(QCUBED_PROJECT_MODELCONNECTOR_DIR .'/PersonConnector.php');


class ModelConnectorTests extends \QCubed\Test\UnitTestCaseBase
{
    protected static $frmTest;

    /**
     * @beforeClass
     */
    public static function setUpClass()
    {
        global $_FORM;
        self::$frmTest = $_FORM;
    }

    public function testBasicControls()
    {
        $mctTypeTest = TypeTestConnector::create(self::$frmTest);

        $mctTypeTest->DateControl->DateTime = new \QCubed\QDateTime('10/10/2010');
        $mctTypeTest->DateTimeControl->DateTime = new \QCubed\QDateTime('11/11/2011');
        $mctTypeTest->TestIntControl->Value = 5;
        $mctTypeTest->TestFloatControl->Value = 3.5;
        $mctTypeTest->TestVarcharControl->Text = 'abcde';
        $mctTypeTest->TestTextControl->Text = 'ABCDE';
        $mctTypeTest->TestBitControl->Checked = true;

        $id = $mctTypeTest->saveTypeTest();

        $mctTypeTest2 = TypeTestConnector::create(self::$frmTest, $id);
        $dt = $mctTypeTest2->DateControl->DateTime;
        $this->assertTrue($dt->isEqualTo(new \QCubed\QDateTime('10/10/2010', null, \QCubed\QDateTime::DATE_ONLY_TYPE)), 'Date only type saved correctly through connector.');
        $dt = $mctTypeTest2->DateTimeControl->DateTime;
        $this->assertTrue($dt->isEqualTo(new \QCubed\QDateTime('11/11/2011')), 'Date time type saved correctly through connector.');
        $this->assertEquals(5, $mctTypeTest2->TestIntControl->Value, 'Integer control saved correctly.');
        $this->assertEquals(3.5, $mctTypeTest2->TestFloatControl->Value, 'Float type saved correctly.');
        $this->assertEquals('abcde', $mctTypeTest2->TestVarcharControl->Text, 'Varchar control type saved correctly through connector.');
        $this->assertEquals('ABCDE', $mctTypeTest2->TestTextControl->Text, 'Text type saved correctly through connector.');
        $this->assertEquals(true, $mctTypeTest2->TestBitControl->Checked, 'Bit saved correctly through connector.');

        $mctTypeTest2->deleteTypeTest();
    }

    public function testReference()
    {
        // test through list control
        $mctProject = ProjectConnector::create(self::$frmTest, 1);
        $lstControl = $mctProject->ManagerPersonIdControl;
        $this->assertTrue($lstControl instanceof \QCubed\Project\Control\ListBox);
        $this->assertEquals($lstControl->SelectedValue, 7, "Read manager as person value.");
        $lstControl->SelectedValue = 6;
        $mctProject->saveProject();

        $mctProject2 = ProjectConnector::create(self::$frmTest, 1);
        $objPerson = $mctProject2->Project->ManagerPerson;
        $this->assertEquals(6, $objPerson->Id, "Forward reference saved correctly through connector.");
        $mctProject2->Project->ManagerPersonId = 7;
        $mctProject2->Project->save();    // restore value

        // test refresh
        $mctProject->load(2);
        $this->assertEquals(4, $mctProject->ManagerPersonIdControl->SelectedValue, "Reloaded forward reference connector");


        // test through auto complete
        $mctAddress = AddressConnector::create(self::$frmTest);
        $lstControl = $mctAddress->PersonIdControl;
        $this->assertTrue($lstControl instanceof \QCubed\Project\Jqui\Autocomplete);
        $lstControl->SelectedValue = 2;
        $mctAddress->StreetControl->Text = 'Test Street';
        $mctAddress->CityControl->Text = 'Test City';
        $id = $mctAddress->saveAddress();

        $mctAddress2 = AddressConnector::create(self::$frmTest, $id);
        $objPerson = $mctAddress2->Address->Person;
        $this->assertNotNull($objPerson);
        $this->assertEquals('Kendall', $objPerson->FirstName, "Forward reference saved correctly through connector.");
        $mctAddress->deleteAddress();

        // test refresh
        $mctAddress->load(3);
        $this->assertEquals('New York', $mctAddress->CityControl->Text);
    }

    public function testReverseReference()
    {
        $mctPerson = PersonConnector::create(self::$frmTest, 7);
        $lstControl = $mctPerson->LoginControl;
        $this->assertTrue($lstControl instanceof \QCubed\Control\ListControl);
        $this->assertEquals($lstControl->SelectedValue, 4);
        $this->assertEquals($mctPerson->Person->Login->Username, 'kwolfe');

        // test save
        $lstControl->SelectedValue = 5;
        $mctPerson->savePerson();
        $this->assertEquals($mctPerson->Person->Login->Id, 5);
        // restore
        $lstControl->SelectedValue = 4;
        $mctPerson->savePerson();
        $this->assertEquals($mctPerson->Person->Login->Id, 4);

        // test refresh
        $mctPerson->load(3);
        $this->assertEquals($lstControl->SelectedValue, 2);
        $this->assertEquals($mctPerson->Person->Login->Username, 'brobinson');
    }

    public function testManyToMany()
    {
        $clauses = array(\QCubed\Query\QQ::expandAsArray(QQN::person()->ProjectAsTeamMember));
        $objPerson = Person::load(2, $clauses);
        $mctPerson = new PersonConnector(self::$frmTest, $objPerson);
        $lstControl = $mctPerson->ProjectAsTeamMemberControl;
        $this->assertTrue($lstControl instanceof \QCubed\Control\ListControl);
        $values = $lstControl->SelectedValues;
        sort($values);
        $this->assertEquals($values[0], 1);
        $this->assertEquals($values[1], 2);
        $this->assertEquals($values[2], 4);

        // test refresh
        $mctPerson->load(3, $clauses);
        $values = $lstControl->SelectedValues;
        sort($values);
        $this->assertEquals($values[0], 4);
        $this->assertEquals(count($values), 1);

        // Test save
        $lstControl->SelectedValues = [2,4];
        $mctPerson->savePerson();
        $a = Project::loadArrayByPersonAsTeamMember(3);
        $this->assertEquals(2, $a[0]->Id);
        $this->assertEquals(4, $a[1]->Id);

        $lstControl->SelectedValues = [4];
        $mctPerson->savePerson();
        $a = Project::loadArrayByPersonAsTeamMember(3);
        $this->assertEquals(4, $a[0]->Id);
    }

    public function testType1()
    {
        $mctProject = ProjectConnector::create(self::$frmTest, 3);
        $this->assertEquals($mctProject->ProjectStatusTypeIdControl->SelectedValue, 1);

        $mctProject->ProjectStatusTypeIdControl->SelectedValue = ProjectStatusType::Cancelled;
        $mctProject->saveProject();
        $this->assertEquals(ProjectStatusType::Cancelled, $mctProject->Project->ProjectStatusTypeId);

        // restore
        $mctProject->ProjectStatusTypeIdControl->SelectedValue = ProjectStatusType::Open;
        $mctProject->saveProject();
        $this->assertEquals(ProjectStatusType::Open, $mctProject->Project->ProjectStatusTypeId);

        $mctProject->load(1);
        $this->assertEquals($mctProject->ProjectStatusTypeIdControl->SelectedValue, 3);
    }

    public function testTypeMulti()
    {
        $mctPerson = PersonConnector::create(self::$frmTest, 3);
        $values = $mctPerson->PersonTypeControl->SelectedValues;
        $this->assertEquals(3, count($values));

        $values2 = $values;
        $values2[] = 5;

        $mctPerson->PersonTypeControl->SelectedValues = $values2;
        $mctPerson->savePerson();
        $values3 = $mctPerson->Person->getPersonTypeArray();
        $this->assertEquals(4, count($values3));
        $mctPerson->PersonTypeControl->SelectedValues = $values;
        $mctPerson->savePerson();
        $values3 = $mctPerson->Person->getPersonTypeArray();
        $this->assertEquals(3, count($values3));
    }

    /**
     * These tests check to see that the codegen_options.json file is being used during code generation.
     */
    public function testOverrides()
    {
        $mctAddress = AddressConnector::create(self::$frmTest);

        $blnError = false;
        try {
            $mctAddress->StreetLabel;
        } catch (\QCubed\Exception\UndefinedProperty $e) {
            $blnError = true;
        }
        $this->assertTrue($blnError, 'Street Label was removed by override.');

        $this->assertEquals('100px', $mctAddress->CityControl->Width);

        // Many-to-Many settings
        $mctProject = ProjectConnector::create(self::$frmTest);
        $this->assertEquals(3, $mctProject->PersonAsTeamMemberControl->RepeatColumns);
        $this->assertEquals('Team Members', $mctProject->PersonAsTeamMemberControl->Name);

        // Unique Reverse Reference
        $mctPerson = PersonConnector::create(self::$frmTest);
        $this->assertTrue($mctPerson->LoginControl->Required, 'Reverse reference was marked required by override file.');

        $objItem = $mctPerson->LoginControl->getItem(0);
        $this->assertEquals($objItem->Name, '- Select One -', 'Required value was detected by list control.');
    }
}
