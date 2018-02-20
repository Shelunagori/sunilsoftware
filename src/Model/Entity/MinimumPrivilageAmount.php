<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MinimumPrivilageAmount Entity
 *
 * @property int $id
 * @property int $company_id
 * @property float $amount
 * @property \Cake\I18n\FrozenTime $created_on
 *
 * @property \App\Model\Entity\Company $company
 */
class MinimumPrivilageAmount extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
