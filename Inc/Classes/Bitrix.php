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

                $emails      = $user['EMAIL'];
                $emailsArray = [];

                if ( ! empty($emails) && is_array($emails)) {
                    foreach ($emails as $email) {
                        $emailsArray[] = $email['VALUE'];
                    }
                }
                $emailsString     = implode(',', $emailsArray);
                $convertedUsers[] = [
                    'id'     => $user['ID'],
                    'emails' => $emailsString,
                ];

            }
        }
        return $convertedUsers;
    }

    public function updateContacts() {

        $fp = @fopen("clearUsers.txt", "r");
        if ($fp) {
            while (($buffer = fgets($fp, 4096)) !== false) {
                $update_contact = unserialize($buffer);
                $bitrix_contact =  $this->bitrixInstance->getContact($update_contact['ID']);

                $delete_emails = [
                    'EMAIL' => []
                ];

                //удалить прошлые email
                if (isset($bitrix_contact['EMAIL'])) {
                    foreach ($bitrix_contact['EMAIL'] as $key => $field_email) {
                        $delete_emails['EMAIL'][] = [
                            "ID" => $field_email['ID'],
                            "VALUE" => '',
                            "VALUE_TYPE" => "WORK"
                        ];
                    }
                    $this->bitrixInstance->updateContact($update_contact['ID'],$delete_emails);
                }

                //прочитать emails из файла
                $filerow_emails = explode(',',$update_contact['EMAILS']);
                $update_emails = [
                    'EMAIL' => []
                ];
                foreach ($filerow_emails as $key => $email) {
                    $update_emails['EMAIL'][] = [
                        "VALUE" => $email,
                        "VALUE_TYPE" => "WORK"
                    ];
                }
                $this->bitrixInstance->updateContact($update_contact['ID'],$update_emails);
            }
            if (!feof($fp)) {
                echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
            }
            fclose($fp);
        }
    }
}
