<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\IntraLocationStockTransferVouchersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\IntraLocationStockTransferVouchersTable Test Case
 */
class IntraLocationStockTransferVouchersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\IntraLocationStockTransferVouchersTable
     */
    public $IntraLocationStockTransferVouchers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.intra_location_stock_transfer_vouchers',
        'app.companies',
        'app.states',
        'app.company_users',
        'app.users',
        'app.locations',
        'app.grns',
        'app.grn_rows',
        'app.items',
        'app.units',
        'app.stock_groups',
        'app.item_ledgers',
        'app.stock_journals',
        'app.inwards',
        'app.outwards',
        'app.sizes',
        'app.shades',
        'app.gst_figures',
        'app.ledgers',
        'app.accounting_groups',
        'app.nature_of_groups',
        'app.suppliers',
        'app.customers',
        'app.accounting_entries',
        'app.purchase_vouchers',
        'app.purchase_voucher_rows',
        'app.first_gst_figures',
        'app.second_gst_figures',
        'app.second_tamp_grn_records',
        'app.financial_years',
        'app.first_tamp_grn_records',
        'app.intra_location_stock_transfer_voucher_rows'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('IntraLocationStockTransferVouchers') ? [] : ['className' => IntraLocationStockTransferVouchersTable::class];
        $this->IntraLocationStockTransferVouchers = TableRegistry::get('IntraLocationStockTransferVouchers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->IntraLocationStockTransferVouchers);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
