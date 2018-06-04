<?php

namespace Main;

use Illuminate\Database\Eloquent\Model;

use Main\Http\Middleware\CorsMiddleware;

class Hinsyu extends Model
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
    		$router->get('/reset', self::class.'@reset');
    	});
    }
    public function __construct()
    {
    	$this->connection = 'ebix';
    	$this->table = 'hinsyu';
    	$this->file = base_path('../data/統計用分類-品種CD（20170220現在）.csv');
    }
    
    public function index()
    {
        $input = \Request::input();
        $query = $this;
        foreach ($input as $name => $value)
        {
            $query->orWhere($name, 'like', '%'.'$value'.'%');
        }
    	return $query->limit(100)->get();
    	// $this->reset();
    	// $this->loadCSV([ $this, 'insertValues' ]);
    }

    public function reset()
    {
        $schema = \Illuminate\Support\Facades\Schema::connection($this->connection);
        $schema->dropIfExists($this->table);
        $schema->create($this->table, function (\Illuminate\Database\Schema\Blueprint $table)
        {
              $table->string('hinsyu_code' , 50)->comment('品種ＣＤ');
              $table->string('hinmei'      )->nullable()->comment('品名');
              $table->string('size'        )->nullable()->comment('サイズ');
              $table->string('yoryo'       )->nullable()->comment('容量');
              $table->string('yoryo_tani'  )->nullable()->comment('重量');
              $table->string('irisu'       )->nullable()->comment('入数');
              $table->string('middle_code' )->nullable()->comment('');
              $table->string('large_name'  )->nullable()->comment('大分類');
              $table->string('middle_name' )->nullable()->comment('中分類');
              $table->string('small_name'  )->nullable()->comment('小分類');
              $table->string('syu'         )->nullable()->comment('種');
              $table->string('keijo_bui'   )->nullable()->comment('形状部位');
              $table->string('kako_hoho'   )->nullable()->comment('加工方法');
              // $table->string('aki_1'       )->nullable()->comment('');
              // $table->string('aki_2'       )->nullable()->comment('');
              // $table->string('aki_3'       )->nullable()->comment('');
              // $table->string('aki_4'       )->nullable()->comment('');
              $table->timestamps();

              $table->primary('hinsyu_code');
        });

        $connection = $this->getConnection();
        $connection->statement('alter table '.$this->table.' comment \'品種マスタ\'');
    }
    public function loadCSV($process)
    {
    	$file = $this->file;
    	if (!file_exists($file))
        {
            throw new \Exception($file.' not exists!');
        }
        $total = (int)shell_exec('wc -l '.$file.' | awk \'{print $1}\'');
        $csv = new \SplFileObject($file);
        $csv->setFlags(\SplFileObject::READ_CSV);

        $columns = [
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
            'syu', 
            'keijo_bui', 
            'kako_hoho', 
            'aki_1', 
            'aki_2', 
            'aki_3', 
            'aki_4', 
        ]; 

        $offset = 0;
        $limit = 1000;
        $block = collect();
        $done = 0;
        foreach ($csv as $values)
        {
            if ($done++ === 0) continue;
            if (is_null($values) || is_null(@$values[0])) continue;
            if ($values[0] === '') continue;
            $block->push($values);
            if ($block->count() === $limit)
            {
                call_user_func($process, $columns, $block);
                $block = collect();
            }
        }
        if ($block->count() > 0) call_user_func($process, $columns, $block);

        dump($done);
    }
    public function insertValues($columns, $values)
    {
    	$columns->push('created_at');
    	$columns->push('updated_at');
    	$now = \Carbon\Carbon::now();
    	mb_convert_variables('UTF-8', 'SJIS-WIN', $values);
    	$combine = collect($values)->map(function ($values) use ($columns, $now)
    	{
    		$values = collect($values);
    		$values->push($now);
    		$values->push($now);
    		return $columns->combine($values)->all();
    	});
    	$combine = $combine->map(function ($columns)
    	{
    		return collect($columns)->forget(['aki_1', 'aki_2', 'aki_3', 'aki_4'])->all();
    	});
    	$this->getConnection()->table($this->table)->insert($combine->all());
    	// dump($combine->count());
    }

    
}
