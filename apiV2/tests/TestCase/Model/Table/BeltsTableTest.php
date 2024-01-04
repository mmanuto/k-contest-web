<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BeltsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BeltsTable Test Case
 */
class BeltsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BeltsTable
     */
    protected $Belts;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Belts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Belts') ? [] : ['className' => BeltsTable::class];
        $this->Belts = $this->getTableLocator()->get('Belts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Belts);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\BeltsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
