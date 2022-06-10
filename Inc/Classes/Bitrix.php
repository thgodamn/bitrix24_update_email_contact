<?php

namespace sd;

use App\Bitrix24\Bitrix24API;
use App\Bitrix24\Bitrix24APIException;

class Bitrix
{
    protected string $webhookURL = 'https://bitrix24.loc/rest/1/2334xo23yislz1g4/';
    protected Bitrix24API $bitrixInstance;

    public function __construct()
    {
        $this->bitrixInstance = new Bitrix24API($this->webhookURL);
    }

    public function addContacts(array $bitrixUsers)
    {
        try {
            $this->bitrixInstance->addContacts($bitrixUsers);
        } catch (Bitrix24APIException|\Exception $e) {
            echo "Error: {$e->getMessage()}";
            die();
        }
    }

    public function getUsers()
    {

        try {
            $users = $this->bitrixInstance->getContactList([], ['ID', 'EMAIL']);
            return $this->convertUsers($users);
        } catch (\Exception $e) {
            echo "Error: {$e->getMessage()}";
            die();
        }

    }

    protected function convertUsers(\Generator $generatorUsers): array
    {
        $convertedUsers = [];
        /* Generator return users in array by 50 items */
        foreach ($generatorUsers as $users) {
            foreach ($users as $user) {
                $emailsString     = serialize($user['EMAIL']);
                $convertedUsers[] = [
                    'id'     => $user['ID'],
                    'emails' => $emailsString,
                ];

            }
        }
        return $convertedUsers;
    }

    public function updateContacts() {

        $contacts = [];
        $fp = @fopen("clearUsers.txt", "r");
        if ($fp) {

            while (($buffer = fgets($fp, 4096)) !== false) {
                $contact = unserialize($buffer);
                $contact['EMAIL'] = unserialize($contact['EMAIL']);
                $contacts[] = $contact;

            }

            $this->bitrixInstance->updateContacts($contacts);

            if (!feof($fp)) {
                echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
            }
            fclose($fp);
        }

    }
}
