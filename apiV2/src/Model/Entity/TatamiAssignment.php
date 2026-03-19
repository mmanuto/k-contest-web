<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TatamiAssignment Entity
 *
 * @property int $id
 * @property string $competition_id
 * @property string $tatami
 * @property string $categorycode_id
 * @property string $status
 *
 * @property \App\Model\Entity\Competition $competition
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Categorycode $categorycode
 */
class TatamiAssignment extends Entity
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
        'tatami' => true,
        'categorycode_id' => true,
        'status' => true,
        'competition' => true,
        'user' => true,
        'categorycode' => true,
    ];
}
