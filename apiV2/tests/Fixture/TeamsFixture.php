<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TeamsFixture
 */
class TeamsFixture extends TestFixture
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
                'club_id' => 'Lorem ipsum dolor ',
                'competition_id' => 'Lorem ipsum dolor ',
                'componenti' => 'Lorem ipsum dolor sit amet',
                'categoria' => 'Lorem ipsum dolor sit amet',
                'grado' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
