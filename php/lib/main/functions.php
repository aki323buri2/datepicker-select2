<?php
if (!function_exists('sjis2utf8'))
{
	function sjis2utf8($sjis)
	{
		return mb_convert_encoding($sjis, 'UTF-8', 'SJIS-WIN'); 
	}
}
if (!function_exists('basename_without_extension'))
{
	function basename_without_extension($path)
	{
		return preg_replace('/\.[^.]+$/', '', basename($path));
	}
}