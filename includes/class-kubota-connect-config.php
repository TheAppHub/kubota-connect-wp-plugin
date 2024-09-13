<?php

class Kubota_Connect_Config {
	private static $config = [
		'plugin_name' => 'kubota-connect',
		'version' => '0.9.2',
		'api_url' => 'https://api.kubota.io/dealers/v1',
	];

	public static function getConfig($key = null) {
		if ($key === null) {
			return self::$config;
		}

		return isset(self::$config[$key]) ? self::$config[$key] : null;
	}
}