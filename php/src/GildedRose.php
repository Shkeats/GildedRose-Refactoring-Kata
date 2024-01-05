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
            $this->applyQualityReductions($item);
            $this->applyQualityIncreases($item);
            $this->updateItemSellIn($item);

            if ($this->itemIsPastSellDate($item)) {
                $this->handleItemPastSellDate($item);
            }
        }
    }

    private function applyQualityReductions(Item $item): void
    {
        if ($item->quality < 0) {
            return;
        }

        if ($this->itemIsAgedBrie($item) || $this->itemIsSulfuras($item) || $this->itemIsBackstagePass($item)) {
            return;
        }

        $qualityDecrease = $this->itemIsConjured($item) ? 2 : 1;
        $newQuality = $item->quality - $qualityDecrease;
        $item->quality = max($newQuality, 0);
    }

    private function itemIsAgedBrie(Item $item): bool
    {
        return $item->name === 'Aged Brie';
    }

    private function itemIsSulfuras(Item $item): bool
    {
        return $item->name === 'Sulfuras, Hand of Ragnaros';
    }

    private function itemIsBackstagePass(Item $item): bool
    {
        return $item->name === 'Backstage passes to a TAFKAL80ETC concert';
    }

    private function itemIsConjured(Item $item): bool
    {
        return str_contains($item->name, 'Conjured');
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

    private function updateItemSellIn(Item $item): void
    {
        $item->sellIn = $this->itemIsSulfuras($item) ? $item->sellIn : $item->sellIn - 1;
    }

    private function itemIsPastSellDate(Item $item): bool
    {
        return $item->sellIn < 0;
    }

    private function handleItemPastSellDate(Item $item): void
    {
        if (!$this->itemIsAgedBrie($item)) {
            if (!$this->itemIsBackstagePass($item)) {
                if ($item->quality > 0) {
                    if (!$this->itemIsSulfuras($item)) {
                        $this->applyQualityReductions($item);
                    }
                }
            } else {
                $item->quality = $item->quality - $item->quality;
            }
        } else {
            if ($item->quality < 50) {
                $item->quality = $item->quality + 1;
            }
        }
    }
}
