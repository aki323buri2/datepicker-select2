<?php

namespace Main;

use Illuminate\Database\Eloquent\Model;
use SplFIleObject;

class Bunrui extends Model
{
    public static function route()
    {
    	\Route::group([
    		'prefix' => 'bunrui', 
    	], function ($router)
    	{
    		$router->get('/', self::class.'@index');
    	});
    }
    public function index()
    {
    	$file = base_path('../data/統計用分類-大分類中分類（20170220現在）.csv');
    	$csv = new SplFileObject($file);
    	$csv->setFlags(SplFileObject::READ_CSV);
    	$parse = collect();
    	foreach ($csv as $row)
    	{
    		$parse->push($row);
    	}
    	mb_convert_variables('UTF-8', 'SJIS-WIN', $parse); 
    	$parse->splice(0, 1);
    	$columns = collect([
    		'large_code', 
    		'large_name', 
    		'middle_pk_no', 
    		'middle_code', 
    		'middle_name', 
    		'large_ane_middle_name', 
    		'middle_code_repeat', 
    		'large_code_and_name', 
    		'middle_code_and_name', 
    	]);
    	$parse = $parse->map(function ($row) use ($columns)
    	{
    		$row = collect($row);
    		$row = $row->pad($columns->count(), null);

    		return (object)$columns->combine($row)->all();
    	})->filter(function ($row)
    	{
    		return !is_null($row->large_code);
    	});
    	return $parse;
    }
}
