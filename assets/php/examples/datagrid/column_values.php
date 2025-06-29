<?php

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Table;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    /** @var Table */
    protected Table $tblProjects;

    /**
     * @throws InvalidCast
     * @throws Caller
     */
    protected function formCreate(): void
    {
        // Define the DataGrid
        $this->tblProjects = new Table($this);

        // This CSS class is used to style alternate rows and the header, all in CSS
        $this->tblProjects->CssClass = 'simple_table';

        // Define Columns

        // Show the name of the project
        $this->tblProjects->createNodeColumn('Project', QQN::project()->Name);

        // Date column formatting. Uses the Format string to format the date object that is in the column.
        $col = $this->tblProjects->createNodeColumn('Start Date', QQN::project()->StartDate);
        $col->Format = 'MM/DD/YY';
        $col = $this->tblProjects->createNodeColumn('End Date', QQN::project()->EndDate);
        $col->Format = 'DDD, MMM D, YYYY';

        // PersonAsTeamMemberArray is an array of names. Use a callback to format the array into a string.
        $col = $this->tblProjects->createPropertyColumn('Members', 'PersonAsTeamMemberArray');
        $col->PostCallback = 'ExampleForm::RenderTeamMemberArray';

        //
        $col = $this->tblProjects->createCallableColumn('Balance', [$this, 'dtgPerson_BalanceRender']);
        $col->CellParamsCallback = [$this, 'dtgPerson_BalanceAttributes'];

        $this->tblProjects->setDataBinder('tblProjects_Bind');

    }

    /**
     * Bind the Projects table to the HTML table.
     *
     * @throws Caller
     */
    protected function tblProjects_Bind(): void
    {
        // Expand the PersonAsTeamMember node as an array so that it will be included in each item sent to the columns.
        $clauses = QQ::expandAsArray(QQN::project()->PersonAsTeamMember);

        // We load the data source and set it to the datagrid's DataSource parameter
        $this->tblProjects->DataSource = Project::loadAll($clauses);
    }

    /**
     * Render the team member array as a string.
     *
     * @param array $a
     * @return string
     */
    public static function renderTeamMemberArray(?array $a): string
    {
        if ($a) {
            return implode(', ',
                array_map(function ($val) {
                    return $val->FirstName . ' ' . $val->LastName;
                }, $a));
        } else {
            return '';
        }
    }

    /**
     * Render the number in the column. If the number is negative, uses parentheses to show its negative.
     *
     * @param $item
     * @return string
     */
    public function dtgPerson_BalanceRender($item): string
    {
        $val = $item->Budget - $item->Spent;
        if ($val < 0) {
            return '(' . number_format(-$val) . ')';
        } else {
            return number_format($val);
        }
    }

    /**
     * Calculates and applies style attributes related to the balance of a person's budget and expenses.
     *
     * @param object $item An object representing a person with `Budget` and `Spent` properties.
     * @return array An associative array containing the CSS class and optional style attributes based on the balance.
     */
    public function dtgPerson_BalanceAttributes(object $item): array
    {
        $ret['class'] = 'amount';
        $val = $item->Budget - $item->Spent;

        if ($val < 0) {
            $ret['style'] = 'color:red';
        }
        return $ret;
    }
}

ExampleForm::run('ExampleForm');
