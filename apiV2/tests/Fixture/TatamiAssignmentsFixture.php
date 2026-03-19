<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TatamiAssignmentsFixture
 */
class TatamiAssignmentsFixture extends TestFixture
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
                'competition_id' => 'Lorem ipsum dolor ',
                'tatami' => 'Lorem ip',
                'categorycode_id' => 'Lorem ip',
                'status' => 'Lorem ipsum dolor ',
            ],
        ];
        parent::init();
    }
}
