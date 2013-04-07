<?php

namespace PhptedaExamples;

use Phpteda\Generator\AbstractGenerator;
use JobExternQuery;
use JobExtern;
use Phpteda\Util\Choice;

/**
 * Generates external jobs
 *
 * @author jwiese
 * @since 2013-03-07
 *
 * @fakerLocale de_DE
 * @fakerProvider \Inhouse\DataGenerator\Faker\Provider\JobOffer
 *
 * <select name="Job status">
 * @method JobExternGenerator activeJobs() Generate only active jobs?
 * @method JobExternGenerator rejectedJobs() Generate only rejected jobs?
 * </select>
 *
 */
class JobExternGenerator extends AbstractGenerator
{
    /**
     * Implements custom way to delete existing data
     *
     * @return void
     */
    protected function removeExistingData()
    {
        JobExternQuery::create()->deleteAll();
    }

    /**
     * Implements custom generator behaviour
     *
     * @return void
     */
    protected function generateData()
    {
        $job = new JobExtern();
        $job->fromArray($this->getDefaultData(), \BasePeer::TYPE_FIELDNAME);

        $status = Choice::when($this->activeJobs)->then(JobExtern::STATUS_APPROVED)->otherwise(null);
        $this->applyStatus($job, $status);

        $job->save();
    }

    /**
     * Returns default values of job
     *
     * @return array
     */
    private function getDefaultData()
    {
        return array(
            'profile_id' => 10000,
            'unique_id' => $this->faker->md5,
            'prizes_salary' => $this->faker->randomElement(
                array(
                    '40.000 - 60.000',
                    '60.000 - 80.000',
                    '80.000 - 100.000'
                )
            ),
            'branch_id' => $this->faker->randomNumber(1, 50),
            'country' => 9,
            'job_name' => $this->faker->jobName,
            'position_level' => 'ma',
            'company' => $this->faker->company,
            'city' => $this->faker->city,
            'url' => $this->faker->url,
            'description' => $this->faker->text(100),
            'shortdescription' => $this->faker->text(50),
            'searchengine_id' => $this->faker->randomNumber(1, 4),
            'searchengine_job_id' => $this->faker->md5,
            'inserted_date' => $this->faker->dateTimeBetween('-2 year', '-1 month'),
            'status_deleted_permanent' => $this->faker->boolean(10),
            'comment' => 'Inserted by ' . get_called_class(),
            'timestamp' => time(),
            'deleted_by_provider' => null,
            'is_modified' => false
        );
    }

    /**
     * @param JobExtern $job
     * @param int $status
     */
    private function applyStatus(JobExtern $job, $status = null)
    {
        $status = is_null($status) ? $this->faker->randomNumber(0, 2) : $status;

        switch ($status) {
            case JobExtern::STATUS_APPROVED:
                $approvedDate = $this->faker->dateTimeBetween($job->getInsertedDate(), '-1 day');
                $rejectedDate = null;
                break;
            case JobExtern::STATUS_NOT_PROCESSED:
                $approvedDate = null;
                $rejectedDate = null;
                break;
            case JobExtern::STATUS_REJECTED:
                $approvedDate = null;
                $rejectedDate = $this->faker->dateTimeBetween($job->getInsertedDate(), '-1 day');
                break;
        }

        $job->setStatus($status);
        $job->setApprovedDate($approvedDate);
        $job->setRejectedDate($rejectedDate);
    }
}
