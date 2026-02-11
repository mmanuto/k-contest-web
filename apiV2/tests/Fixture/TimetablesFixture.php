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
                'competition_id' => 'Lorem ipsum dolor ',
                'category' => 'Lorem ipsum dolor sit amet',
                'specialita' => 'Lorem ipsum dolor sit amet',
                'time' => 'Lor',
            ],
        ];
        parent::init();
    }
}
