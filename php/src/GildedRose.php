<?php

declare(strict_types=1);

namespace GildedRose;

final class GildedRose
{
    /**
     * @param Item[] $items
     */
    public function __construct(
        private array $items
    )
    {
    }

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $this->applyQualityIncreases($item);
            $this->updateItemSellIn($item);
            $this->applyQualityReductions($item);
        }
    }

    private function applyQualityIncreases(Item $item): void
    {
        if ($item->quality >= 50) {
            return;
        }

        if ($this->itemIsAgedBrie($item)) {
            $item->quality++;
            return;
        }

        if ($this->itemIsBackstagePass($item)) {
            $item->quality = $item->quality + 1;
            if ($item->sellIn < 11) {
                $item->quality++;
            }
            if ($item->sellIn < 6) {
                $item->quality++;
            }
        }
    }

    private function itemIsAgedBrie(Item $item): bool
    {
        return $item->name === 'Aged Brie';
    }

    private function itemIsBackstagePass(Item $item): bool
    {
        return $item->name === 'Backstage passes to a TAFKAL80ETC concert';
    }

    private function updateItemSellIn(Item $item): void
    {
        $item->sellIn = $this->itemIsSulfuras($item) ? $item->sellIn : $item->sellIn - 1;
    }

    private function itemIsSulfuras(Item $item): bool
    {
        return $item->name === 'Sulfuras, Hand of Ragnaros';
    }

    private function applyQualityReductions(Item $item): void
    {
        $itemPastSellByDate = $this->itemIsPastSellDate($item);

        if ($this->itemIsBackstagePass($item)) {
            $item->quality = $itemPastSellByDate ? 0 : $item->quality;
            return;
        }

        if ($this->itemIsAgedBrie($item) || $this->itemIsSulfuras($item) || $this->itemIsBackstagePass($item)) {
            return;
        }

        $qualityDecrease = $this->itemIsConjured($item) ? 2 : 1;

        if ($itemPastSellByDate) {
            $qualityDecrease = $qualityDecrease * 2;
        }

        $newQuality = $item->quality - $qualityDecrease;
        $item->quality = max($newQuality, 0);
    }

    private function itemIsPastSellDate(Item $item): bool
    {
        return $item->sellIn < 0;
    }

    private function itemIsConjured(Item $item): bool
    {
        return str_contains($item->name, 'Conjured');
    }
}
