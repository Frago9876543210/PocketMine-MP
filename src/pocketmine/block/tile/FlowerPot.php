<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\block\tile;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;

/**
 * @deprecated
 * @see \pocketmine\block\FlowerPot
 */
class FlowerPot extends Spawnable{
	private const TAG_ITEM = "item";
	private const TAG_ITEM_DATA = "mData";
	private const TAG_PLANT_BLOCK = "PlantBlock";
	private const TAG_PLANT_BLOCK_NAME = "name";
	private const TAG_PLANT_BLOCK_VAL = "val";

	/** @var Block|null */
	private $plant = null;

	public function readSaveData(CompoundTag $nbt) : void{
		try{
			if($nbt->hasTag(self::TAG_ITEM, ShortTag::class) and $nbt->hasTag(self::TAG_ITEM_DATA, IntTag::class)){
				$this->setPlant(BlockFactory::get($nbt->getShort(self::TAG_ITEM), $nbt->getInt(self::TAG_ITEM_DATA)));
			}elseif($nbt->hasTag(self::TAG_PLANT_BLOCK, CompoundTag::class)){
				$blockPlant = $nbt->getCompoundTag(self::TAG_PLANT_BLOCK);

				$name = $blockPlant->getString(self::TAG_PLANT_BLOCK_NAME);
				$val = $blockPlant->getShort(self::TAG_PLANT_BLOCK_VAL);

				$this->setPlant(ItemFactory::fromString("$name:$val")->getBlock());
			}
		}catch(\InvalidArgumentException $e){
			//noop
		}
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		if($this->plant !== null){
			$nbt->setShort(self::TAG_ITEM, $this->plant->getId());
			$nbt->setInt(self::TAG_ITEM_DATA, $this->plant->getMeta());
		}
	}

	public function getPlant() : ?Block{
		return $this->plant !== null ? clone $this->plant : null;
	}

	public function setPlant(?Block $plant) : void{
		if($plant === null or $plant instanceof Air){
			$this->plant = null;
		}else{
			$this->plant = clone $plant;
		}
	}

	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		if($this->plant !== null){
			$nbt->setShort(self::TAG_ITEM, $this->plant->getId());
			$nbt->setInt(self::TAG_ITEM_DATA, $this->plant->getMeta());
		}
	}
}
