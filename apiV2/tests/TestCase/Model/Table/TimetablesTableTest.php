<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TimetablesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TimetablesTable Test Case
 */
class TimetablesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TimetablesTable
     */
    protected $Timetables;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Timetables',
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
        $config = $this->getTableLocator()->exists('Timetables') ? [] : ['className' => TimetablesTable::class];
        $this->Timetables = $this->getTableLocator()->get('Timetables', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Timetables);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TimetablesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\TimetablesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
