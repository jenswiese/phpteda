<?php

use Faker\Factory;
use Phpteda\Util\Choice;

/**
 * Class for generating CVS file with Users
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-09
 *
 *
 * @method static UserGenerator generate()
 * @method UserGenerator activeUser()
 * @method UserGenerator deletedUser()
 * @method UserGenerator blockedUser()
 * @method UserGenerator createdAtToday()
 * @method UserGenerator noEmail()
 * @method UserGenerator withUserCategory($userCategory)
 *
 */
class UserGenerator extends \Phpteda\Generator\AbstractGenerator
{
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
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = Choice::when($this->noEmail)->then(null)->otherwise($this->faker->safeEmail);
        $userCategory =
            Choice::when($this->withUserCategory)->otherwise($this->faker->randomNumber(1, 3));
        $createdAt =
            Choice::when($this->createdAtToday)
            ->then($this->faker->dateTimeBetween('today'))
            ->otherwise($this->faker->dateTimeBetween('-1 year', '-6 months'));

        $isActive = Choice::when($this->activeUser)->then(true)->otherwise($this->faker->boolean);
        $isDeleted = Choice::when($this->deletedUser)->then(true)->otherwise($this->faker->boolean);
        $isBlocked = Choice::when($this->blockedUser)->then(true)->otherwise($this->faker->boolean);

        $user = new User();
        $user->setFirstname($firstName);
        $user->setLastname($lastName);
        $user->setEmail($email);
        $user->setCreatedAt($createdAt);
        $user->setUserCategory($userCategory);
        $user->setIsActive($isActive);
        $user->setIsDeleted($isDeleted);
        $user->setIsBlocked($isBlocked);

        file_put_contents('/tmp/phpteda_user.csv', $user, FILE_APPEND);
    }
}
