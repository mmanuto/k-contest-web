<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Belts Model
 *
 * @method \App\Model\Entity\Belt newEmptyEntity()
 * @method \App\Model\Entity\Belt newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Belt[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Belt get($primaryKey, $options = [])
 * @method \App\Model\Entity\Belt findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Belt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Belt[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Belt|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Belt saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Belt[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Belt[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Belt[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Belt[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class BeltsTable extends Table
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

        $this->setTable('belts');
        $this->setDisplayField('Nome');
        $this->setPrimaryKey('id');
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
            ->scalar('Nome')
            ->maxLength('Nome', 50)
            ->requirePresence('Nome', 'create')
            ->notEmptyString('Nome');

        $validator
            ->scalar('category')
            ->maxLength('category', 50)
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

        $validator
            ->integer('group_increment')
            ->requirePresence('group_increment', 'create')
            ->notEmptyString('group_increment');

        return $validator;
    }
}
