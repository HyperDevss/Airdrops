<?php

namespace AppGallery\Airdrops\utils;

use pocketmine\item\Item;

final class Randomizer{

    private array $items;
    private int $min_count_per_items;
    private int $max_count_per_items;
    private int $maximum_items;

    /**
     * @param Item[] $items
     * @param int $min_count_per_items
     * @param int $max_count_per_items
     * @param int $maximum_items
     */
    public function __construct(array $items, int $min_count_per_items, int $max_count_per_items, int $maximum_items){
        $this->items = $items;
        $this->min_count_per_items = min($min_count_per_items, $max_count_per_items);
        $this->max_count_per_items = max($min_count_per_items, $max_count_per_items);
        $this->maximum_items = $maximum_items;
        $this->init();
    }

    private function init(): void{
        $result = [];
        foreach ($this->items as $item) {
            $result[] = match($item->getMaxStackSize()){
                1 => $item->setCount(1),
                16 => $item->setCount(rand($this->min_count_per_items, 16)),
                default => $item->setCount(rand($this->min_count_per_items, $this->max_count_per_items))
            };
        }
        $possibleItems = array_rand($result, min(count($this->items), $this->maximum_items));
        foreach ($possibleItems as $possibleItem) {
            $this->items[] = $result[$possibleItem];
        }
    }

    /**
     * @return Item[]
     */
    public function getItems(): array{
        shuffle($this->items);
        return $this->items;
    }

}