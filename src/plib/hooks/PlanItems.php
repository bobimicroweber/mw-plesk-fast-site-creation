<?php

class Modules_Skeleton_PlanItems extends pm_Hook_PlanItems
{
    public function getPlanItems()
    {
        return [
            'mw_fast_site_creation' => 'Install mw fast'
        ];
    }
}