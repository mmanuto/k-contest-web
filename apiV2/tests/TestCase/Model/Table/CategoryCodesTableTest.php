<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CategoryCodesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CategoryCodesTable Test Case
 */
class CategoryCodesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CategoryCodesTable
     */
    protected $CategoryCodes;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.CategoryCodes',
        'app.AthletesInscriptions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CategoryCodes') ? [] : ['className' => CategoryCodesTable::class];
        $this->CategoryCodes = $this->getTableLocator()->get('CategoryCodes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CategoryCodes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CategoryCodesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
