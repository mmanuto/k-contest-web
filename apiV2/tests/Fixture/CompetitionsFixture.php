<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CompetitionsFixture
 */
class CompetitionsFixture extends TestFixture
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
                'responsabile' => 'Lorem ipsum dolor sit amet',
                'club_name' => 'Lorem ipsum dolor sit amet',
                'logo' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'telephone' => 'Lorem ipsum dolor sit amet',
                'nome_gara' => 'Lorem ipsum dolor sit amet',
                'apertura_iscrizioni' => '2024-02-02',
                'chiusura_iscrizioni' => '2024-02-02',
                'data_gara' => '2024-02-02',
                'locandina' => 'Lorem ipsum dolor sit amet',
                'circolare' => 'Lorem ipsum dolor sit amet',
                'nr_atleti' => 1,
                'richieste' => 'Lorem ipsum dolor sit amet',
                'codiceTipoCategorie' => 1,
                'comp_status' => 'Lorem ipsum dolor sit amet',
                'flag_tabs' => 1,
                'flag_classifications' => 1,
                'flag_timetable' => 1,
            ],
        ];
        parent::init();
    }
}
