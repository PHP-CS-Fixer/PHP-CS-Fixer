<?php

require_once __DIR__.'/../vendor/autoload.php';

//class StreamWrapper
//{
//	public static $openResourceCount = 0;
//	private $resource;
//
//	public function stream_open($path, $mode)
//	{
//		$this->resource = $this->original(function () use ($path, $mode) {
//			return fopen($path, $mode);
//		});
//
//		if ($this->resource === false) {
//			return false;
//		}
//
//		++self::$openResourceCount;
//
//		return true;
//	}
//
//	public function stream_close()
//	{
//		$this->original(function () {
//			fclose($this->resource);
//		});
//
//		--self::$openResourceCount;
//		var_dump(self::$openResourceCount);
//	}
//
//	public function stream_read($count)
//	{
//		return $this->original(function () use ($count) {
//			return fread($this->resource, $count);
//		});
//	}
//
//	public function stream_write($data)
//	{
//		return $this->original(function () use ($data) {
//			return fwrite($this->resource, $data);
//		});
//	}
//
//	public function stream_eof()
//	{
//		return $this->original(function () {
//			return feof($this->resource);
//		});
//	}
//
//	public function stream_stat()
//	{
//		return [];
//	}
//
//	public function url_stat($path)
//	{
//		return $this->original(function () use ($path) {
//			return stat($path);
//		});
//	}
//
//	public function dir_opendir($path)
//	{
//		$this->resource = $this->original(function () use ($path) {
//			return opendir($path);
//		});
//
//		if ($this->resource === false) {
//			return false;
//		}
//
//		return true;
//	}
//
//	public function dir_readdir()
//	{
//		return $this->original(function () {
//			return readdir($this->resource);
//		});
//	}
//
//	private function original($cb)
//	{
//		stream_wrapper_restore('file');
//		$result = $cb();
//		stream_wrapper_unregister('file');
//		stream_wrapper_register('file', self::class);
//		return $result;
//	}
//}

//stream_wrapper_unregister('file');
//stream_wrapper_register('file', StreamWrapper::class);
