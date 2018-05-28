<?php

namespace Main;

use Illuminate\Database\Eloquent\Model;
use DB;

class Hinsyu extends Model
{
    public static function route()
    {
        \Route::group([
            'prefix' => 'hinsyu', 
        ], function ($router)
        {
            $router->get('/', self::class.'@index');
        });
    }
    public function __construct()
    {
        config([ 'database.connections' => collect(config('database.connections'))->merge([
            'ebix-sqlite' => [
                'driver' => 'sqlite', 
                'database' => base_path('../data/ebix.sqlite'), 
                'prefix' => '', 
            ], 
        ])]);
        // dd(config('database.connections'));
        $this->connection = 'ebix-sqlite';
        $this->table = 'hinsyu';
        $this->id = 'hinsyu_code';
        $this->incrementing = false;

        $this->columns = collect([
            'hinsyu_code', 
            'hinmei', 
            'size', 
            'yoryo', 
            'yoryo_tani', 
            'irisu', 
            'middle_code', 
            'large_name', 
            'middle_name', 
            'small_name', 
            'syurui', 
            'keijo_bui', 
            'kako_ho', 
            'aki_1', 
            'aki_2', 
            'aki_3', 
            'aki_4', 
        ]);
    }
    public function create_table()
    {
        $cn = DB::connection($this->connection);
        $sql = [];
        $sql[] = 'create table '.$this->table.' (';
        foreach ($this->columns as $name)
        {
            $null =(
                $name === $this->id 
                ? 'not null' 
                : 'null default null'
            );
            $sql[] = $name.' varchar(100) '.$null.',';
        }
        $sql[] = 'craeted_at datetime null default null,';
        $sql[] = 'updated_at datetime null default null,';
        $sql[] = 'primary key ('.$this->id.')';
        $sql[] = ')';
        $sql = implode("\n", $sql);
        $cn->statement($sql);
        
    }
    public function index()
    {
        dd($this);
        $file = base_path('../data/統計用分類-品種CD（20170220現在）.csv');
        if (!file_exists($file))
        {
            throw new \Exception($file.' not found!');
        }
        // $size = filesize($file);

        $total = (int)exec('wc -l '.$file.' | awk "{print $1}"');
        
        $csv = new \SplFileObject($file);
        $csv->setFlags(\SplFileObject::READ_CSV);
        $parse = collect();
        $limit = 500;
        foreach ($csv as $line)
        {
            $parse->push($line);
            if (--$limit === 0) break;
        }
        mb_convert_variables('UTF-8', 'SJIS-WIN', $parse);

        $columns = $this->columns;
        $parse = $parse->map(function ($row) use ($columns)
        {
            $values = collect($row)->pad($columns->count(), null);
            return (object)$columns->combine($values)->all();
        });

        return $parse;
    }
}
