<?php

namespace PhptedaExamples;

use Phpteda\Util\Choice;
use PhptedaExamples\User;

/**
 * Class for generating CVS file with Users
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-09
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
                <group>
                    <property name="userStatus" title="Provide user status to generate">
                        <option value="active">Only active users</option>
                        <option value="deleted">Only deleted users</option>
                        <option value="blocked">Only blocked users</option>
                    </property>
                    <property name="createdAtToday" type="boolean">Users should be created today?</property>
                    <property name="noEmail" type="boolean">Users should contain no email?</property>
                    <property name="withUserCategory">Which user category should be taken?</property>
                </group>
                <group>
                    <property name="testMulti" type="multiple">Provide Urls for testing</property>
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
        $firstName = $this->testDataGenerator->firstName;
        $lastName = $this->testDataGenerator->lastName;
        $email = Choice::when($this->noEmail)->then(null)->otherwise($this->testDataGenerator->safeEmail);
        $userCategory =
            Choice::when($this->withUserCategory)->otherwise($this->testDataGenerator->randomNumber(1, 3));
        $createdAt =
            Choice::when($this->createdAtToday)
            ->then($this->testDataGenerator->dateTimeBetween('today'))
            ->otherwise($this->testDataGenerator->dateTimeBetween('-1 year', '-6 months'));

        $isActive = ('active' == $this->userStatus);
        $isDeleted = ('deleted' == $this->userStatus);
        $isBlocked = ('blocked' == $this->userStatus);

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
