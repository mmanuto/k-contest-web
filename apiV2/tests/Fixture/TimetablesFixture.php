<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TimetablesFixture
 */
class TimetablesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'label' => 'Lorem ipsum dolor sit amet',
                'time' => 'Lor',
                'tatami' => 'Lorem ipsum d',
                'color' => 'Lorem ip',
                'competition_id' => 1,
            ],
        ];
        parent::init();
    }
}
