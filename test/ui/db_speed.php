<?php
    require_once('../qcubed.inc.php');
    
	class SpeedForm extends \QCubed\Project\Control\FormBase {
		protected $pnlTiny;
		protected $pnlBig;

		protected $btnGo;

		protected function formCreate() {

			$count = 10000;
			Project::ClearCache();
			Person::ClearCache();

			// Create test persons in database.

			// Tiny objects
			if (Person::CountAll() < $count) {
				for ($i = 0; $i < $count; $i++) {
					$objPerson = new Person();
					$objPerson->FirstName = 'FirstName' . $i;
					$objPerson->LastName = 'LastName' . $i;
					$objPerson->Save();
				}
			}

			// Bigger objects with expansion
			if (Project::CountAll() < $count) {
				for ($i = 0; $i < $count; $i++) {
					$objProject = new Project();
					$objProject->Name = 'Project' . $i;
					$objProject->ProjectStatusTypeId = ProjectStatusType::Open;
					$objProject->ManagerPersonId = $i % 1000 + 1000;
					$objProject->Description = 'Description' . $i;
					$objProject->StartDate = \QCubed\QDateTime::Now();
					$objProject->EndDate = \QCubed\QDateTime::Now();
					$objProject->Budget = $i;
					$objProject->Spent = 1;
					$objProject->Save();
				}
			}

			$this->pnlTiny = new \QCubed\Control\Panel($this);
			$this->pnlTiny->Name = '10,000 Person Objects';

			$this->pnlBig = new \QCubed\Control\Panel($this);
			$this->pnlBig->Name = '10,000 Project Objects With Expansion';

			$this->btnGo = new \QCubed\Project\Control\Button($this);
			$this->btnGo->Text = 'Go';
			$this->btnGo->AddAction (new \QCubed\Event\Click(), new \QCubed\Action\Ajax('Go_Click'));

		}

		protected function Go_Click() {
			\QCubed\Project\Application::$blnLocalCache = false;

			$timeNoCache = -microtime(true);
			$a = Person::LoadAll(); // noncached loads
			$timeNoCache += microtime(true);

			\QCubed\Project\Application::$blnLocalCache = true;

			$timeLoad1Cached = -microtime(true);
			$a = Person::LoadAll(); // noncached loads
			$timeLoad1Cached += microtime(true);
			$timeLoad2Cached = -microtime(true);
			$a = Person::LoadAll(); // cached loads
			$timeLoad2Cached += microtime(true);

			\QCubed\Project\Application::$blnLocalCache = new \QCubed\Cache\LocalMemoryCache(array());

			$timeLoad3Cached = -microtime(true);
			$a = Person::LoadAll(); // noncached loads
			$timeLoad3Cached += microtime(true);
			$timeLoad4Cached = -microtime(true);
			$a = Person::LoadAll(); // cached loads
			$timeLoad4Cached += microtime(true);


			$this->pnlTiny->Text = sprintf ("Load No Cache: %2.1f%% \n", 100 * $timeNoCache/ $timeNoCache) .
				sprintf ("Populate Cache: %2.1f%% \n", 100 * $timeLoad1Cached / $timeNoCache) .
				sprintf ("Load With Cache: %2.1f%% \n", 100 * $timeLoad2Cached / $timeNoCache) .
				sprintf ("Populate LocalCacheProvider: %2.1f%% \n", 100 * $timeLoad3Cached / $timeNoCache) .
				sprintf ("Load LocalCacheProvider: %2.1f%% \n", 100 * $timeLoad4Cached / $timeNoCache)
			;

			$cond = \QCubed\Query\QQ::Equal (QQN::Project()->ProjectStatusTypeId, ProjectStatusType::Open);
			$clauses[] = \QCubed\Query\QQ::Expand (QQN::Project()->ManagerPerson);

			Project::ClearCache();
			Person::ClearCache();

			\QCubed\Project\Application::$blnLocalCache = false;

			$timeNoCache = -microtime(true);
			$a = Project::QueryArray($cond, $clauses); // noncached loads
			$timeNoCache += microtime(true);

			\QCubed\Project\Application::$blnLocalCache = true;

			$timeLoad1Cached = -microtime(true);
			$a = Project::QueryArray($cond, $clauses); // noncached loads
			$timeLoad1Cached += microtime(true);
			$timeLoad2Cached = -microtime(true);
			$a = Project::QueryArray($cond, $clauses); // cached loads
			$timeLoad2Cached += microtime(true);

			\QCubed\Project\Application::$blnLocalCache = new \QCubed\Cache\LocalMemoryCache(array());

			$timeLoad3Cached = -microtime(true);
			$a = Project::QueryArray($cond, $clauses); // noncached loads
			$timeLoad3Cached += microtime(true);
			$timeLoad4Cached = -microtime(true);
			$a = Project::QueryArray($cond, $clauses); // cached loads
			$timeLoad4Cached += microtime(true);


			$this->pnlBig->Text = sprintf ("Load No Cache: %2.1f%% \n", 100 * $timeNoCache / $timeNoCache) .
				sprintf ("Populate Cache: %2.1f%% \n", 100 * $timeLoad1Cached / $timeNoCache) .
				sprintf ("Load With Cache: %2.1f%% \n", 100 * $timeLoad2Cached / $timeNoCache) .
				sprintf ("Populate LocalCacheProvider: %2.1f%% \n", 100 * $timeLoad3Cached / $timeNoCache) .
				sprintf ("Load LocalCacheProvider: %2.1f%% \n", 100 * $timeLoad4Cached / $timeNoCache)
			;

		}

	}
	SpeedForm::Run('SpeedForm');
?>