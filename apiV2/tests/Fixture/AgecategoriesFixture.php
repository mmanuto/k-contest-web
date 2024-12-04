<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AgecategoriesFixture
 */
class AgecategoriesFixture extends TestFixture
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
                'id' => '9e3d23cc-950c-4d93-ae4b-3c4b91858e4e',
                'description' => 'Lorem ipsum dolor sit amet',
                'age_min' => 1,
                'age_max' => 1,
                'agonist' => 1,
                'out_category' => 1,
            ],
        ];
        parent::init();
    }
}
