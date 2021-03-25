<?php
namespace App\Http\Enums; 

abstract class Enum {
    public static function getAll() {
        $oClass = new \ReflectionClass(get_called_class());
        return $oClass->getConstants();
    }
    public static function getDisplay($value) {
        if (isset($value)){
            $oClass = new \ReflectionClass(get_called_class());
            $constants = $oClass->getConstants();
            foreach ($constants as $item) {
                if ($item['value'] == $value) return $item['display'];
            }
        }
        return false;
    }
	
	public static function getAllValue(){
		$all = self::getAll();
		
		return array_map ( function ($a) {
			return $a['value'];
		}, $all);
	}
}