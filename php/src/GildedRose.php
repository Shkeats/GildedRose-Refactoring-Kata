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
            if (!$this->itemIsAgedBrie($item) and !$this->itemIsBackstagePass($item)) {
                if ($item->quality > 0) {
                    if (!$this->itemIsSulfuras($item)) {
                        $qualityDecrease = $this->itemIsConjured($item) ? 2 : 1;
                        $item->quality = $item->quality - $qualityDecrease;
                    }
                }
            } else {
                if ($item->quality < 50) {
                    $item->quality = $item->quality + 1;
                    if ($this->itemIsBackstagePass($item)) {
                        if ($item->sellIn < 11) {
                            if ($item->quality < 50) {
                                $item->quality = $item->quality + 1;
                            }
                        }
                        if ($item->sellIn < 6) {
                            if ($item->quality < 50) {
                                $item->quality = $item->quality + 1;
                            }
                        }
                    }
                }
            }

            if (!$this->itemIsSulfuras($item)) {
                $item->sellIn = $item->sellIn - 1;
            }

            if ($item->sellIn < 0) {
                if (!$this->itemIsAgedBrie($item)) {
                    if (!$this->itemIsBackstagePass($item)) {
                        if ($item->quality > 0) {
                            if (!$this->itemIsSulfuras($item)) {
                                $qualityDecrease = $this->itemIsConjured($item) ? 2 : 1;
                                $item->quality = $item->quality - $qualityDecrease;
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
    }

    private function itemIsAgedBrie(Item $item): bool
    {
        return $item->name === 'Aged Brie';
    }

    private function itemIsBackstagePass(Item $item): bool
    {
        return $item->name === 'Backstage passes to a TAFKAL80ETC concert';
    }

    private function itemIsSulfuras(Item $item): bool
    {
        return $item->name === 'Sulfuras, Hand of Ragnaros';
    }

    private function itemIsConjured(Item $item): bool
    {
        return str_contains($item->name, 'Conjured');
    }
}
