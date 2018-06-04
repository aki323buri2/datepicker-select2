<?php
if (!function_exists('basename_without_extension'))
{
	function basename_without_extension($file)
	{
		return preg_replace('/\.[^.]+$/', '', basename($file));
	}
}