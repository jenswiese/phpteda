<?php

namespace PhptedaExamples;

use Faker\Factory;
use Phpteda\Util\Choice;
use PhptedaExamples\User;

/**
 * Class for generating CVS file with Users
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-09
 *
 *
 * @method static UserGenerator generate()
 *
 * <select name="User status">
 * @method UserGenerator activeUser() Generate active users?
 * @method UserGenerator deletedUser() Generate deleted users?
 * @method UserGenerator blockedUser() Generate blocked users?
 * </select>
 *
 * @method UserGenerator createdAtToday() Users should be created today?
 * @method UserGenerator noEmail() Users should contain no email?
 * @method UserGenerator withUserCategory($userCategory) Which user category should be taken?
 *
 */
class UserGenerator extends \Phpteda\Generator\AbstractGenerator
{

    /**
     * @return string
     */
    public static function getConfig()
    {
        return '
            <config>
                <group title="Common">
                    <property name="userStatus" title="Provide user status to generate">
                        <option value="active">Only active users</option>
                        <option value="deleted">Only deleted users</option>
                        <option value="blocked">Only blocked users</option>
                    </property>
                    <property name="createdAtToday" type="boolean">Users should be created today?</property>
                    <property name="noEmail" type="boolean">Users should contain no email?</property>
                    <property name="withUserCategory">Which user category should be taken?</property>
                </group>
            </config>
        ';
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
