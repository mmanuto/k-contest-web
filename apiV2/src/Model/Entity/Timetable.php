<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Timetable Entity
 *
 * @property int $id
 * @property string $competition_id
 * @property string $category
 * @property string $specialita
 * @property string $time
 *
 * @property \App\Model\Entity\Competition $competition
 */
class Timetable extends Entity
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
        'competition_id' => true,
        'category' => true,
        'specialita' => true,
        'time' => true,
        'competition' => true,
    ];
}
