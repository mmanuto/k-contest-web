<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Timetables Model
 *
 * @property \App\Model\Table\CompetitionsTable&\Cake\ORM\Association\BelongsTo $Competitions
 *
 * @method \App\Model\Entity\Timetable newEmptyEntity()
 * @method \App\Model\Entity\Timetable newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Timetable[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Timetable get($primaryKey, $options = [])
 * @method \App\Model\Entity\Timetable findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Timetable patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Timetable[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Timetable|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Timetable saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Timetable[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Timetable[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Timetable[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Timetable[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TimetablesTable extends Table
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

        $this->setTable('timetables');
        $this->setDisplayField('label');

        $this->belongsTo('Competitions', [
            'foreignKey' => 'competition_id',
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
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->scalar('label')
            ->maxLength('label', 500)
            ->requirePresence('label', 'create')
            ->notEmptyString('label');

        $validator
            ->scalar('time')
            ->maxLength('time', 5)
            ->requirePresence('time', 'create')
            ->notEmptyString('time');

        $validator
            ->scalar('tatami')
            ->maxLength('tatami', 15)
            ->requirePresence('tatami', 'create')
            ->notEmptyString('tatami');

        $validator
            ->scalar('color')
            ->maxLength('color', 10)
            ->requirePresence('color', 'create')
            ->notEmptyString('color');

        $validator
            ->integer('competition_id')
            ->notEmptyString('competition_id');

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

        return $rules;
    }
}
