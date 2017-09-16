<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CreditNotes Model
 *
 * @property \App\Model\Table\CompaniesTable|\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\LedgersTable|\Cake\ORM\Association\BelongsTo $PartyLedgers
 * @property \App\Model\Table\LedgersTable|\Cake\ORM\Association\BelongsTo $SalesLedgers
 * @property \App\Model\Table\CreditNoteRowsTable|\Cake\ORM\Association\HasMany $CreditNoteRows
 *
 * @method \App\Model\Entity\CreditNote get($primaryKey, $options = [])
 * @method \App\Model\Entity\CreditNote newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CreditNote[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CreditNote|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CreditNote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CreditNote[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CreditNote findOrCreate($search, callable $callback = null, $options = [])
 */
class CreditNotesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('credit_notes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('PartyLedgers', [
			'className' => 'Ledgers',
            'foreignKey' => 'party_ledger_id',
            'joinType' => 'INNER'
        ]);
		
		$this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER'
        ]);
		$this->belongsTo('Ledgers', [
            'foreignKey' => 'ledger_id',
            'joinType' => 'INNER'
        ]);
		
		$this->hasMany('ItemLedgers', [
			'foreignKey' => 'sales_invoice_id',
			'saveStrategy'=>'replace'
        ]);
		
		$this->hasMany('AccountingEntries', [
            'foreignKey' => 'credit_note_id',
            'joinType' => 'INNER',
			'saveStrategy'=>'replace'
        ]);
		
		$this->belongsTo('SalesLedgers', [
			'className' => 'Ledgers',
            'foreignKey' => 'sales_ledger_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('CreditNoteRows', [
            'foreignKey' => 'credit_note_id',
			'saveStrategy'=>'replace'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('voucher_no')
            ->requirePresence('voucher_no', 'create')
            ->notEmpty('voucher_no');

       /*  $validator
            ->requirePresence('sales_invoice_no', 'create')
            ->notEmpty('sales_invoice_no'); */

        $validator
            ->date('transaction_date')
            ->requirePresence('transaction_date', 'create')
            ->notEmpty('transaction_date');

        $validator
            ->decimal('amount_before_tax')
            ->requirePresence('amount_before_tax', 'create')
            ->notEmpty('amount_before_tax');

        $validator
            ->decimal('total_cgst')
            ->allowEmpty('total_cgst');

        $validator
            ->decimal('total_sgst')
            ->allowEmpty('total_sgst');

        $validator
            ->decimal('total_igst')
            ->allowEmpty('total_igst');

        $validator
            ->decimal('amount_after_tax')
            ->requirePresence('amount_after_tax', 'create')
            ->notEmpty('amount_after_tax');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['party_ledger_id'], 'PartyLedgers'));
        $rules->add($rules->existsIn(['sales_ledger_id'], 'SalesLedgers'));

        return $rules;
    }
}
