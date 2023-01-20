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
                if (!$this->checkSsl($domain->getName())) {
                    $this->addDomainEncryption($domain);
                } else {
                    // Domain already have a SSL.
                }

            break;
        }
    }

    private function checkSsl($domainName)
    {
        $g = @stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
        $r = @stream_socket_client("ssl://www.".$domainName.":443", $errno, $errstr, 30,
            STREAM_CLIENT_CONNECT, $g);
        $cont = @stream_context_get_params($r);
        if (isset($cont["options"]["ssl"]["peer_certificate"])) {
            return true;
        }

        return false;
    }

    private function addDomainEncryption($domain)
    {
        $artisan = false;

        $sslEmail = 'admin@microweber.com';

        $encryptOptions = [];
        $encryptOptions[] = '--domain';
        $encryptOptions[] = $domain->getName();
        $encryptOptions[] = '--email';
        $encryptOptions[] = $sslEmail;

        // Add SSL
        try {
            //Modules_Microweber_Log::debug('Start installing SSL for domain: ' . $domain->getName() . '; SSL Email: ' . $sslEmail);

            $artisan = \pm_ApiCli::call('extension', array_merge(['--exec', 'letsencrypt', 'cli.php'], $encryptOptions), \pm_ApiCli::RESULT_FULL);

            //Modules_Microweber_Log::debug('Encrypt domain log for: ' . $domain->getName() . '<br />' . $artisan['stdout']. '<br /><br />');
            //Modules_Microweber_Log::debug('Success instalation SSL for domain: ' . $domain->getName());

        } catch(\Exception $e) {

            //Modules_Microweber_Log::debug('Can\'t install SSL for domain: ' . $domain->getName());
            //Modules_Microweber_Log::debug('Error: ' . $e->getMessage());

        }

        return $artisan;
    }
}

return new Modules_Skeleton_EventListener();