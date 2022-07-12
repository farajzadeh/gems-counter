<?php

namespace Farajzadeh\GemsCounter\Test;

class HasGemsTest extends TestCase
{
    /** @test */
    public function gems_counter_test()
    {
        $this->assertEquals(0, $this->testUser->fresh()->gems_count);

        $this->testUser->fresh()->createTransaction(10, 'test');
        $this->assertEquals(1, $this->testUser->fresh()->transactions()->count());

        $this->testUser->fresh()->createTransaction(20, 'test');
        $this->assertEquals(30, $this->testUser->fresh()->gems_count);

        $sum = $this->testUser->fresh()->gems_count;
        for ($i = 0; $i < 1000; $i++) {
            $randomAmount = rand(-1000, 1000);
            $sum += $randomAmount;
            $this->testUser->fresh()->createTransaction($randomAmount, 'test');
            $this->assertEquals($sum, $this->testUser->fresh()->gems_count);
        }

        $this->assertEquals($i + 2, $this->testUser->fresh()->transactions()->count());

        $this->assertDatabaseCount('gems', 1);

        $this->testUser->delete();
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('gems', 0);
    }
}