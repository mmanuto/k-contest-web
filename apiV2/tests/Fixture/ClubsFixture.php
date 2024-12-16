<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ClubsFixture
 */
class ClubsFixture extends TestFixture
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
                'id' => '1fb651b1-6f2e-4970-8aab-e99775350503',
                'club_code' => 'Lorem ipsum dolor sit amet',
                'club_name' => 'Lorem ipsum dolor sit amet',
                'fiscal_code' => 'Lorem ipsum dolor ',
                'short_name' => 'Lorem ipsum dolor ',
                'club_manager' => 'Lorem ipsum dolor sit amet',
                'telephone_n' => 'Lorem ipsum dolor ',
                'mail' => 'Lorem ipsum dolor sit amet',
                'coach' => 'Lorem ipsum dolor sit amet',
                'last_login' => '2024-12-16 14:57:42',
                'created_date' => 1734361062,
                'modified_date' => 1734361062,
            ],
        ];
        parent::init();
    }
}
