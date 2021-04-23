<?php
	// Load the QCubed Development Framework
use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Paginator;
use QCubed\Query\QQ;

require('../qcubed.inc.php');
	
	// our child Panel
	require ('./records.summary.php');
		
	class ProjectListForm extends FormBase {
		// Local instance of the DataGrid to list Projects
		protected $dtgProjects;
					
		protected function formCreate() {
			// Instantiate the DataGrid
			$this->dtgProjects = new DataGrid($this);

			// Style the DataGrid
			//$this->dtgProjects->CssClass = 'datagrid';
			$this->dtgProjects->AlternateRowCssClass = 'alternate';

			// Add Pagination
			$this->dtgProjects->Paginator = new Paginator($this->dtgProjects);
			$this->dtgProjects->ItemsPerPage = 3;

			// Add columns
			
			// Create a column that will hold a toggle button. We will need to manually draw the content of the cell.
			$col = $this->dtgProjects->createCallableColumn('', [$this, 'render_btnToggleRecordsSummary']);
			$col->HtmlEntities = false;
			$col->CellStyler->Width = "1%";

			$this->dtgProjects->createNodeColumn('Id', QQN::project()->Id);
			$this->dtgProjects->createNodeColumn('Name', QQN::project()->Name);
			$this->dtgProjects->createNodeColumn('Status', QQN::project()->ProjectStatusType);
			$this->dtgProjects->createNodeColumn('Description', QQN::project()->Description);
			$this->dtgProjects->createNodeColumn('Start Date', QQN::project()->StartDate);
			$this->dtgProjects->createNodeColumn('End Date', QQN::project()->EndDate);
			$this->dtgProjects->createNodeColumn('Budget', QQN::project()->Budget);
			$this->dtgProjects->createNodeColumn('Spent', QQN::project()->Spent);

			// Create a column that will hold a child datagrid

			$col = $this->dtgProjects->createCallableColumn('', [$this, 'render_ucRecordsSummary']);
			$col->HtmlEntities = false;
			$col->CellStyler->Width = 0;

			// Specify the Datagrid's Data Binder method
			$this->dtgProjects->setDataBinder('dtgProjects_Bind');

			// For purposes of this example, add a css file that styles the table.
			// Normally you would include your global style sheets in your tpl file or header.inc.php file.
			$this->dtgProjects->addCssFile(QCUBED_EXAMPLES_URL . '/master_detail/styles.css');
		}

		protected function dtgProjects_Bind() {
			$this->dtgProjects->TotalItemCount = Project::queryCount(QQ::all());

			// If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
			// the OrderByClause to the $objClauses array
			if ($objClause = $this->dtgProjects->OrderByClause) {
				$objClauses[] = $objClause;
			}

			// Add the LimitClause information, as well
			if ($objClause = $this->dtgProjects->LimitClause) {
				$objClauses[] = $objClause;
			}

			$this->dtgProjects->DataSource = Project::loadAll($objClauses);
		}
		
		// Function to render our toggle button column
		// As you can see we pass as a parameter the item binded in the
		// row of DataGrid
		public function render_btnToggleRecordsSummary(Project $objProject) {
			// Create their unique id...
			$objControlId = 'btnToggleRecordsSummary' . $objProject->Id;
			
			if (!$objControl = $this->getControl($objControlId)) {			
				$intTeamMemberCount = Person::countByProjectAsTeamMember($objProject->Id);
				if ($intTeamMemberCount > 0) {

					// If not exists create our toggle button who his parent
					// is our master DataGrid...
					$objControl = new Button($this->dtgProjects, $objControlId);
					$objControl->Width = 25;
					$objControl->Text = '+' . $intTeamMemberCount;
					$objControl->CssClass = 'inputbutton';
				
					// Pass the id of the bounded item just for other process
					// on click event
				
					$objControl->ActionParameter = $objProject->Id;
				
					// Add event on click the toogle button
					$objControl->addAction(new Click(), new Ajax( 'btnToggleRecordsSummary_Click'));
				}
			}
			// We pass the parameter of "false" to make sure the control doesn't render
			// itself RIGHT HERE - that it instead returns its string rendering result.
			return $objControl->render(false);
		}
			
			
		// Clicking the toogle button...
		public function btnToggleRecordsSummary_Click(ActionParams $params) {
			// First get the button himself for change '+' to '-'
			$srcControl = $params->Control;
			
			$intProjectId = intval($params->ActionParameter);
			
			// Look for our child datagrid if is render...
			$objControlId = 'ucRecordsSummary' . $intProjectId;
			$objControl = $this->getControl($objControlId);

			$intTeamMemberCount = Person::countByProjectAsTeamMember($intProjectId);
			if ($intTeamMemberCount > 0) {
				if ($objControl) {
				// Ask if our child datagrid is visible...
					if ($objControl->Visible) {
						// Make it desapear ...
						$objControl->Visible = false;
						$srcControl->Text = '+';
					} else {
						// Or make it appear...
						$objControl->Visible = true;
						$srcControl->Text = '-';
					}

					// Important! Refresh the parent DataGrid...
					$this->dtgProjects->refresh();
				}
			}
		}
			
		// Draw the child datagrid inside of a cell of the parent datagrid
		public function render_ucRecordsSummary(Project $objProject) {
			$objControlId = 'ucRecordsSummary' . $objProject->Id;
			
			if (!$objControl = $this->getControl($objControlId)) {
				// Create the User Control Child DataGrid passing the
				// parent, in this case Master DataGrid and the unique id.
				$objControl = new RecordsSummary($this->dtgProjects, $objProject, $objControlId);
				
				// Put invisible at the begging, the toogle button is gonna do the job
				// test - $objControl->Visible = true;
				$objControl->Visible = false;
			}
			
			return $objControl->render(false);
		}	
	}

	// Go ahead and run this form object to generate the page and event handlers, 
	// implicitly using project_list.tpl.php as the included HTML template file
	ProjectListForm::run('ProjectListForm');
