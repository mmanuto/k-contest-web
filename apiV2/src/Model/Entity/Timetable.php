<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Timetable Entity
 *
 * @property int $id
 * @property string $label
 * @property string $time
 * @property string $tatami
 * @property string $color
 * @property int $competition_id
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
        'id' => true,
        'label' => true,
        'time' => true,
        'tatami' => true,
        'color' => true,
        'competition_id' => true,
        'competition' => true,
    ];
}
