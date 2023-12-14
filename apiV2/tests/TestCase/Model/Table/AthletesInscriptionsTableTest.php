<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AthletesInscriptionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AthletesInscriptionsTable Test Case
 */
class AthletesInscriptionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AthletesInscriptionsTable
     */
    protected $AthletesInscriptions;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.AthletesInscriptions',
        'app.Athletes',
        'app.CategoryCodes',
        'app.Competitions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('AthletesInscriptions') ? [] : ['className' => AthletesInscriptionsTable::class];
        $this->AthletesInscriptions = $this->getTableLocator()->get('AthletesInscriptions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->AthletesInscriptions);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\AthletesInscriptionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\AthletesInscriptionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
