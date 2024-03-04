<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AthletesInscription Entity
 *
 * @property int $id
 * @property int $athlete_id
 * @property int $category_code_id
 * @property int $competition_id
 * @property string $cintura
 * @property int|null $n_iscrizione
 * @property int $inviato
 * @property int $modificato
 * @property int $accorpamento
 * @property int $deleted
 * @property string|null $old_category
 *
 * @property \App\Model\Entity\Athlete $athlete
 * @property \App\Model\Entity\CategoryCode $category_code
 * @property \App\Model\Entity\Competition $competition
 */
class AthletesInscription extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'athlete_id' => true,
        'category_code_id' => true,
        'competition_id' => true,
        'cintura' => true,
        'n_iscrizione' => true,
        'inviato' => true,
        'modificato' => true,
        'accorpamento' => true,
        'deleted' => true,
        'old_category' => true,
        'athlete' => true,
        'category_code' => true,
        'competition' => true,
    ];
}
