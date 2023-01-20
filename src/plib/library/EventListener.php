<?php
class Modules_Skeleton_EventListener implements EventListener
{
    public function filterActions()
    {
        return [
            'license_expired',
            'license_update',
            'additional_license_expired',

            'domain_delete',
            'domain_alias_delete',
            'site_delete',

            'phys_hosting_create',
            'phys_hosting_update'
        ];
    }

    public function handleEvent($objectType, $objectId, $action, $oldValue, $newValue)
    {
        switch ($action) {

            case "phys_hosting_create":

                $domain = new pm_Domain($objectId);
                if (empty($domain->getName())) {
                    return;
                }

                if (!$domain->hasHosting()) {
                    return false;
                }

                

            break;
        }
    }
}

return new Modules_Skeleton_EventListener();