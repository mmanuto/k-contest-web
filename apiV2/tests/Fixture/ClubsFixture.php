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
                'id' => 'eaac657f-6858-4bff-a310-10637435ab76',
                'club_code' => 'Lorem ipsum dolor sit amet',
                'club_name' => 'Lorem ipsum dolor sit amet',
                'fiscal_code' => 'Lorem ipsum dolor ',
                'short_name' => 'Lorem ipsum dolor ',
                'club_manager' => 'Lorem ipsum dolor sit amet',
                'telephone_n' => 'Lorem ipsum dolor ',
                'mail' => 'Lorem ipsum dolor sit amet',
                'coach' => 'Lorem ipsum dolor sit amet',
                'last_login' => '2024-11-01 15:22:04',
            ],
        ];
        parent::init();
    }
}
