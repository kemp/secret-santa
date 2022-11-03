<?php

namespace Tests\Unit;

use App\Exceptions\InvalidSecretSantaException;
use App\Services\SecretSantaRandomizer;
use PHPUnit\Framework\TestCase;

class SecretSantaRandomizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Reset the random seed
        mt_srand(72);
    }

    public function test_groups_can_be_randomized()
    {
        $participantIds = [
            2,
            3,
            4,
        ];

        $results = SecretSantaRandomizer::randomize($participantIds);

        $this->assertEquals([
            2 => 4,
            3 => 2,
            4 => 3,
        ], $results);
    }

    public function test_participants_can_be_placed_with_an_exclusion()
    {
        $participantIds = [
            2,
            3,
            4,
            5,
        ];

        $exclusions = [
            2 => 4,
        ];

        $this->assertAllRandomResultsMissing([
            2 => 4
        ], $participantIds, $exclusions);

        $this->assertAllRandomResultsMissing([
            4 => 2
        ], $participantIds, $exclusions);
    }

    public function test_participants_can_be_placed_with_exclusions()
    {
        $participantIds = [
            2,
            3,
            4,
            5,
            6,
        ];

        $exclusions = [
            3 => 4,
            5 => 4,
        ];

        $this->assertAllRandomResultsMissing([
            3 => 4,
            4 => 5,
        ], $participantIds, $exclusions);

        $this->assertAllRandomResultsMissing([
            4 => 3,
            5 => 4,
        ], $participantIds, $exclusions);
    }

    public function test_an_exception_is_thrown_if_theres_no_suitable_matches()
    {
        $participantIds = [
            2,
            3,
            4,
            5,
        ];

        $exclusions = [
            2 => 3,
            3 => 4,
        ];

        $this->expectException(InvalidSecretSantaException::class);

        SecretSantaRandomizer::randomize($participantIds, $exclusions);
    }

    public function test_can_be_created_with_a_large_number_of_exclusions()
    {
        $participantIds = range(1, 1000);

        $exclusions = array_combine(range(1, 999), range(2, 1000));

        $result = SecretSantaRandomizer::randomize($participantIds, $exclusions);

        $this->assertNotEmpty($result);

        $this->assertCount(1000, $result);
    }

    private function allRandomResults(array $participants, ?array $exclusions = null): array
    {
        $results = [];

        // Perhaps there is a better way here than looping through a high number of times.
        // Can we programmatically determine how my combinations there are?
        for ($i = 0; $i < 1000; $i++) {
            $results[] = SecretSantaRandomizer::randomize($participants, $exclusions);
        }

        return $results;
    }

    private function assertAllRandomResultsMissing(
        array $missing,
        array $participants,
        ?array $exclusions = null,
    ): void
    {
        $results = $this->allRandomResults($participants, $exclusions);

        foreach ($results as $result) {
            foreach ($missing as $missingKey => $missingValue) {
                if ($result[$missingKey] === $missingValue) {
                    $this->fail(sprintf('Match result [%s => %s] should be excluded', $missingKey, $missingValue));
                }
            }
        }

        $this->expectNotToPerformAssertions();
    }
}
