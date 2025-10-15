<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AthletesFixture
 */
class AthletesFixture extends TestFixture
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
                'id' => 'b9c9b6e3-7ffa-4941-80ee-00c2dad562d4',
                'club_id' => 'Lorem ipsum dolor s',
                'nome' => 'Lorem ipsum dolor sit amet',
                'cognome' => 'Lorem ipsum dolor sit amet',
                'data_nascita' => 'Lorem ipsum dolor ',
                'sesso' => 'L',
                'cod_fiscale' => 'Lorem ipsum dolor ',
                'federation_id' => 1,
                'n_tessera' => 'Lorem ipsum dolor sit amet',
                'peso' => 1,
                'grado' => 'Lorem ipsum d',
                'tesserino' => 'Lorem ipsum dolor sit amet',
                'created_date' => 1759436861,
                'modified_date' => 1759436861,
            ],
        ];
        parent::init();
    }
}
