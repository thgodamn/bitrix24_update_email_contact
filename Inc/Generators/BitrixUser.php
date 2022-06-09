<?php

namespace sd\Generators;

class BitrixUser
{
    protected array $users = [];

    public function get(array $emails): array
    {
        $this->generateUsers($emails);

        return $this->users;
    }

    protected function generateUsers(array $emails)
    {
        $emailsCount = count($emails);
        for ($i = 0; $i < $emailsCount; $i++) {
            $this->users[] = [
                'EMAIL' => [
                    [
                        'VALUE_TYPE' => 'WORK',
                        'VALUE'      => $emails[$i],
                        'TYPE_ID'    => 'EMAIL',
                    ],
                ],
            ];
        }
    }
}
