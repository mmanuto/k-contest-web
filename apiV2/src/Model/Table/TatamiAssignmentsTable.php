<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TatamiAssignments Model
 *
 * @property \App\Model\Table\CompetitionsTable&\Cake\ORM\Association\BelongsTo $Competitions
 * @property \App\Model\Table\CategorycodesTable&\Cake\ORM\Association\BelongsTo $Categorycodes
 *
 * @method \App\Model\Entity\TatamiAssignment newEmptyEntity()
 * @method \App\Model\Entity\TatamiAssignment newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TatamiAssignment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TatamiAssignment get($primaryKey, $options = [])
 * @method \App\Model\Entity\TatamiAssignment findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TatamiAssignment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TatamiAssignment[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TatamiAssignment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TatamiAssignment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TatamiAssignment[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TatamiAssignment[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TatamiAssignment[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TatamiAssignment[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TatamiAssignmentsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tatami_assignments');
        $this->setDisplayField('competition_id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Competitions', [
            'foreignKey' => 'competition_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Categorycodes', [
            'foreignKey' => 'categorycode_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('competition_id')
            ->maxLength('competition_id', 20)
            ->notEmptyString('competition_id');

        $validator
            ->scalar('tatami')
            ->maxLength('tatami', 10)
            ->requirePresence('tatami', 'create')
            ->notEmptyString('tatami');

        $validator
            ->scalar('categorycode_id')
            ->maxLength('categorycode_id', 10)
            ->notEmptyString('categorycode_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('competition_id', 'Competitions'), ['errorField' => 'competition_id']);
        $rules->add($rules->existsIn('categorycode_id', 'Categorycodes'), ['errorField' => 'categorycode_id']);

        return $rules;
    }
}
