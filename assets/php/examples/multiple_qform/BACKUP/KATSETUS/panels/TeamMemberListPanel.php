<?php

use QCubed\Control\Panel;
use QCubed\Project\Control\DataGrid;



class TeamMemberListPanel extends Panel
{
    protected DataGrid $dtgMembers;

    public function __construct($parent, string $projectId, ?string $strControlId = null)
    {
        parent::__construct($parent, $strControlId);;

//        $this->dtgMembers = new DataGrid($this);
//        // Simulaator: lisa read (reaalne variant – lae andmebaasist)
//        $team = [
//            ['nimi' => 'Jaan', 'roll' => 'Arendaja'],
//            ['nimi' => 'Mari', 'roll' => 'Testija'],
//            ['nimi' => 'Kati', 'roll' => 'Disainer'],
//        ];
//        foreach ($team as $liige) {
//            $this->dtgMembers->addItem($liige['nimi'] . ' (' . $liige['roll'] . ')');
//        }
        // Siin saab vajadusel lisada: lisa/muuda/kustuta nupud
    }
}
