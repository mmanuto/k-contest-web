<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AthletesInscriptions Model
 *
 * @property \App\Model\Table\AthletesTable&\Cake\ORM\Association\BelongsTo $Athletes
 * @property \App\Model\Table\CategoryCodesTable&\Cake\ORM\Association\BelongsTo $CategoryCodes
 * @property \App\Model\Table\CompetitionsTable&\Cake\ORM\Association\BelongsTo $Competitions
 *
 * @method \App\Model\Entity\AthletesInscription newEmptyEntity()
 * @method \App\Model\Entity\AthletesInscription newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\AthletesInscription[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AthletesInscription get($primaryKey, $options = [])
 * @method \App\Model\Entity\AthletesInscription findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\AthletesInscription patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AthletesInscription[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AthletesInscription|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AthletesInscription saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AthletesInscription[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AthletesInscription[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\AthletesInscription[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\AthletesInscription[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AthletesInscriptionsTable extends Table
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

        $this->setTable('athletes_inscriptions');
        $this->setDisplayField('cintura');
        $this->setPrimaryKey('id');

        $this->belongsTo('Athletes', [
            'foreignKey' => 'athlete_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CategoryCodes', [
            'foreignKey' => 'category_code_id',
            'joinType' => 'INNER',
        ]);
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
            ->integer('athlete_id')
            ->notEmptyString('athlete_id');

        $validator
            ->notEmptyString('category_code_id');

        $validator
            ->notEmptyString('competition_id');

        $validator
            ->scalar('cintura')
            ->maxLength('cintura', 50)
            ->requirePresence('cintura', 'create')
            ->notEmptyString('cintura');

        $validator
            ->allowEmptyString('n_iscrizione');

        $validator
            ->notEmptyString('inviato');

        $validator
            ->notEmptyString('modificato');

        $validator
            ->requirePresence('accorpato', 'create')
            ->notEmptyString('accorpato');

        $validator
            ->scalar('old_category')
            ->maxLength('old_category', 15)
            ->notEmptyString('old_category');

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
        $rules->add($rules->existsIn('athlete_id', 'Athletes'), ['errorField' => 'athlete_id']);
        $rules->add($rules->existsIn('category_code_id', 'CategoryCodes'), ['errorField' => 'category_code_id']);
        $rules->add($rules->existsIn('competition_id', 'Competitions'), ['errorField' => 'competition_id']);

        return $rules;
    }
}
