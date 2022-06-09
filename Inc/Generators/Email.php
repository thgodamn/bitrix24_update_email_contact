<?php

namespace sd\Generators;

use Faker\Factory;
use Faker\Generator;

class Email
{
    protected Generator $faker;
    protected array $emails = [];
    protected string $postfix = '_temp_';
    protected int $numberAfterPostfixMin = 1000;
    protected int $numberAfterPostfixMax = 9999;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function get(int $emailsCount = 10): array
    {
        $this->generateEmails($emailsCount);

        return $this->emails;
    }

    protected function generateEmails($emailsCount)
    {
        for ($i = 0; $i < $emailsCount; $i++) {
            $countOfEmailsWithPostfix = rand(1, 5);
            $email                    = $this->faker->unique()->freeEmail;
            $this->emails[]           = $email;
            $this->generateEmailsWithPostfix($email, $countOfEmailsWithPostfix);
        }
    }

    protected function generateEmailsWithPostfix($email, $countOfEmailsWithPostfix)
    {
        $randomNumbersForPostfix = $this->generateRandomNumbersForPostfix($countOfEmailsWithPostfix);

        for ($i = 0; $i < $countOfEmailsWithPostfix; $i++) {
            $this->emails[] = str_replace('@', "{$this->postfix}{$randomNumbersForPostfix[$i]}@", $email);
        }
    }

    protected function generateRandomNumbersForPostfix($countOfEmailsWithPostfix): array
    {
        $randomNumbersForPostfix = [];
        while (count($randomNumbersForPostfix) < $countOfEmailsWithPostfix) {
            $randomNumbersForPostfix[] = $this->faker->numberBetween(
                $this->numberAfterPostfixMin,
                $this->numberAfterPostfixMax
            );
            /* Remove duplications */
            $randomNumbersForPostfix = array_unique($randomNumbersForPostfix);
        }

        return $randomNumbersForPostfix;
    }

}
