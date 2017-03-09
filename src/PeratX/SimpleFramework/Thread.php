<?php

/**
 * SimpleFramework
 * The fast, light-weighted, easy-to-extend php framework.
 *
 * Some classes are based on project PocketMine-MP.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PeratX
 */

namespace PeratX\SimpleFramework;

/**
 * This class must be extended by all custom threading classes
 */
abstract class Thread extends \Thread{

	/** @var \ClassLoader */
	protected $classLoader;
	protected $isKilled = false;

	public function getClassLoader(){
		return $this->classLoader;
	}

	public function setClassLoader(\ClassLoader $loader = null){
		if($loader === null){
			$loader = Framework::getInstance()->getLoader();
		}
		$this->classLoader = $loader;
	}

	public function registerClassLoader(){
		if(!interface_exists("ClassLoader", false)){
			require_once(\PeratX\SimpleFramework\PATH . "src/sf/util/ClassLoader.php");
		}
		if($this->classLoader !== null){
			$this->classLoader->register(true);
		}
	}

	public function start(int $options = PTHREADS_INHERIT_ALL){
		ThreadManager::getInstance()->add($this);

		if(!$this->isRunning() and !$this->isJoined() and !$this->isTerminated()){
			if($this->getClassLoader() === null){
				$this->setClassLoader();
			}
			return parent::start($options);
		}

		return false;
	}

	/**
	 * Stops the thread using the best way possible. Try to stop it yourself before calling this.
	 */
	public function quit(){
		$this->isKilled = true;

		$this->notify();

		if(!$this->isJoined()){
			if(!$this->isTerminated()){
				$this->join();
			}
		}

		ThreadManager::getInstance()->remove($this);
	}

	public function getThreadName(){
		return (new \ReflectionClass($this))->getShortName();
	}
}
