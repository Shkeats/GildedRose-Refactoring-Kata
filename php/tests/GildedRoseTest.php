<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function test_it_lowers_quality_and_quantity_for_a_normal_item()
    {
        $items = [new Item('foo', 5, 5)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame('foo', $items[0]->name);
        $this->assertSame(4, $items[0]->sellIn);
        $this->assertSame(4, $items[0]->quality);
    }

    public function test_that_when_sell_by_date_has_passed_quality_degrades_twice_as_fast()
    {
        $items = [new Item('foo', 0, 5)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(3, $items[0]->quality);
    }

    public function test_that_the_quality_of_an_item_is_never_negative()
    {
        $items = [new Item('foo', 5, -4)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
    }

    public function test_that_the_quality_of_an_item_cannot_become_negative()
    {
        $items = [new Item('foo', 5, 2)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(1, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
    }

    public function test_that_the_quality_of_an_item_cannot_become_negative_when_sell_by_date_has_passed()
    {
        $items = [new Item('foo', 0, 5)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(3, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(1, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
    }

    public function test_that_the_quality_of_aged_brie_item_increases_as_it_ages()
    {
        $items = [new Item('Aged Brie', 5, 5)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(6, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(7, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->quality);
    }

    public function test_that_the_quality_of_an_item_is_never_more_than_50()
    {
        $items = [new Item('foo', 5, 51)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        //Bad input corrected during updateQuality.
        $this->assertSame(50, $items[0]->quality);
    }

    public function test_that_the_sulfuras_item_is_exempt_from_50_quality_rule_and_remains_at_80_quality()
    {
        $items = [new Item('Sulfuras, Hand of Ragnaros', 1, 80)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(80, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(80, $items[0]->quality);
    }

    public function test_that_the_quality_of_an_item_cannot_be_increased_past_50()
    {
        $items = [new Item('Aged Brie', 5, 49)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
    }

    public function test_that_the_sulfuras_item_never_decreases_in_quality()
    {
        $items = [new Item('Sulfuras, Hand of Ragnaros', 2, 50)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
    }

    public function test_quality_of_backstage_passes_item_increases_by_1_daily_as_it_ages_with_11_days_or_more_left()
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 50, 5)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(6, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(7, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->quality);
    }

    public function test_quality_of_backstage_passes_item_increases_by_2_daily_when_10_to_6_days_remain()
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 11, 5)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(10, $items[0]->sellIn);
        $this->assertSame(6, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(9, $items[0]->sellIn);
        $this->assertSame(8, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->sellIn);
        $this->assertSame(10, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(7, $items[0]->sellIn);
        $this->assertSame(12, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(6, $items[0]->sellIn);
        $this->assertSame(14, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(5, $items[0]->sellIn);
        $this->assertSame(16, $items[0]->quality);
    }

    public function test_quality_of_backstage_passes_item_increases_by_3_daily_when_5_to_0_days_remain()
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 6, 1)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(5, $items[0]->sellIn);
        $this->assertSame(3, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(4, $items[0]->sellIn);
        $this->assertSame(6, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(3, $items[0]->sellIn);
        $this->assertSame(9, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(2, $items[0]->sellIn);
        $this->assertSame(12, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(1, $items[0]->sellIn);
        $this->assertSame(15, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->sellIn);
        $this->assertSame(18, $items[0]->quality);
    }

    public function test_quality_of_backstage_passes_item_drops_to_zero_after_concert()
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 2, 1)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(1, $items[0]->sellIn);
        $this->assertSame(4, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->sellIn);
        $this->assertSame(7, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(-1, $items[0]->sellIn);
        $this->assertSame(0, $items[0]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(-2, $items[0]->sellIn);
        $this->assertSame(0, $items[0]->quality);
    }

    public function test_that_conjured_items_degrade_in_quality_at_2_per_day()
    {
        $items = [
            new Item('Conjured Potato', 5, 10),
            new Item('Gold Ring (Conjured)', 5, 50),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->quality);
        $this->assertSame(48, $items[1]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(6, $items[0]->quality);
        $this->assertSame(46, $items[1]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(4, $items[0]->quality);
        $this->assertSame(44, $items[1]->quality);
    }

    public function test_that_conjured_items_degrade_in_quality_at_2_per_day_before_sell_by_date()
    {
        $items = [
            new Item('Conjured Potato', 5, 10),
            new Item('Gold Ring (Conjured)', 5, 50),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->quality);
        $this->assertSame(48, $items[1]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(6, $items[0]->quality);
        $this->assertSame(46, $items[1]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(4, $items[0]->quality);
        $this->assertSame(44, $items[1]->quality);
    }

    public function test_that_conjured_items_degrade_in_quality_at_4_per_day_before_after_sell_by_date()
    {
        $items = [
            new Item('Conjured Potato', 1, 10),
            new Item('Gold Ring (conjured)', 1, 50),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->quality);
        $this->assertSame(48, $items[1]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(4, $items[0]->quality);
        $this->assertSame(44, $items[1]->quality);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
        $this->assertSame(40, $items[1]->quality);
    }
}
