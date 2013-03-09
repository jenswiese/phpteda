<?php

use Faker\Factory;
use Faker\Generator;

/**
 * Class for ...
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-09
 *
 *
 * @method UserGenerator activeUser()
 * @method UserGenerator deletedUser()
 * @method UserGenerator blockedUser()
 * @method UserGenerator createdAtToday()
 * @method UserGenerator noEmail()
 *
 */
class UserGenerator extends \Phpteda\Generator\AbstractGenerator
{
    public function __construct(Generator $faker = null)
    {
        parent::__construct($faker);
    }

    /**
     * Implements custom way to delete existing data
     *
     * @return AbstractGenerator
     */
    protected function removeExistingData()
    {
        file_put_contents('/tmp/phpteda_user.csv', '');
    }


    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    protected function generateData()
    {
        $user = new User();
        $user->setFirstname($this->faker->firstName);
        $user->setLastname($this->faker->lastName);

        $user->setEmail(
            $this->chooseIf($this->noEmail, null, $this->faker->safeEmail)
        );

        $user->setCreatedAt(
            $this->chooseIf(
                $this->createdAtToday,
                $this->faker->dateTimeBetween('today'),
                $this->faker->dateTimeBetween('-1 year', '-6 months')
            )
        );

        $user->setIsActive(
            $this->chooseIf(
                $this->activeUser, true, $this->faker->boolean
            )
        );

        $user->setIsDeleted(
            $this->chooseIf(
                $this->deletedUser, true, $this->faker->boolean
            )
        );

        $user->setIsBlocked(
            $this->chooseIf(
                $this->blockedUser, true, $this->faker->boolean
            )
        );

        file_put_contents('/tmp/phpteda_user.csv', $user, FILE_APPEND);
    }
}
