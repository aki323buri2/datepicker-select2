<?php

namespace Main;

use Illuminate\Database\Eloquent\Model;

use Main\Http\Middleware\CorsMiddleware;

class Bunrui extends Model
{
    public static function route()
    {
    	\Route::group([
            'prefix' => strtolower(class_basename(self::class)), 
            'middleware' => [
                CorsMiddleware::class, 
            ], 
    	], function ($router)
    	{
    		$router->get('/', self::class.'@index');
    	});
    }
    public function index()
    {
        $input = \Request::input();
        $input = collect($input);
        $query = $this;
        foreach ($input as $name => $value)
        {
            $query->orWhere($name, 'like', '%'.$value.'%');
        }
        return $query->limit(100)->get();
        // $this->reset();
        // $this->loadCSV([ $this, 'insertValues' ]);
    }
    public function __construct()
    {
        $this->connection = 'ebix';
        $this->table = 'bunui';
    }
    function reset()
    {
        $schema = \Illuminate\Support\Facades\Schema::connection($this->connection);
        $schema->dropIfExists($this->table);
        $schema->create($this->table, function (\Illuminate\Database\Schema\Blueprint $table)
        {
            $table->string('large_code'                , 30)->comment('大分類CD');  
            $table->string('large_name'                    )->nullable()->comment('大分類名称');  
            $table->string('middle_subcode'            , 30)->comment('中分類CDサブ');  
            $table->string('middle_code'               , 30)->comment('中分類CD');  
            $table->string('middle_subname'                )->nullable()->comment('中分類名称サブ');  
            $table->string('middle_name'                   )->nullable()->comment('中分類名称');  
            $table->string('middle_code_display'           )->nullable()->comment('中分類CD表示');  
            $table->string('large_code_name_display'       )->nullable()->comment('大分類CD名称表示');  
            $table->string('middle_subcode_subname_display')->nullable()->comment('中分類CDサブ名称サブ表示');  
            $table->timestamps();
            $table->primary('middle_code');
            $table->index('large_code');
            $table->index([ 'large_code', 'middle_subcode' ]);
        });
        $connection = $this->getConnection();
        $connection->statement('alter table '.$this->table.' comment \'分類マスタ\'');
    }
    function loadCSV($process)
    {
    	$file = base_path('../data/統計用分類-大分類中分類（20170220現在）.csv');
        $csv = new \SplFileObject($file);
        $csv->setFlags(\SplFileObject::READ_CSV);

        $columns = [
            'large_code', 
            'large_name', 
            'middle_subcode', 
            'middle_code', 
            'middle_subname', 
            'middle_name', 
            'middle_code_display', 
            'large_code_name_display', 
            'middle_subcode_subname_display', 
        ]; 
        
        $offset = 0;
        $limit = 1000;
        $chunk = collect();
        $done = 0;
        foreach ($csv as $line)
        {
            if (is_null($line)) continue;
            if (is_null(@$line[0])) continue;
            if ($line[0] === '') continue;
            $done++;
            $chunk->push($line);
            if ($chunk->count() === $limit)
            {
                call_user_func($process, $columns, $chunk);
                $chunk = collect();
            }
        }
        if ($chunk->count() > 0) call_user_func($process, $columns, $chunk);
        dd($done);
    }
    function insertValues($columns, $values)
    {
        $columns = collect($columns);
        $columns->push('created_at');
        $columns->push('updated_at');
        $now = \Carbon\Carbon::now();
        mb_convert_variables('UTF-8', 'SJIS-WIN', $values);
        $combine = $values->map(function ($values) use ($columns, $now)
        {
            $values = collect($values);
            $values->push($now);
            $values->push($now);
            return collect($columns)->combine($values)->all();
        });
        $this->getConnection()->table($this->table)->insert($combine->all());
    }
}
