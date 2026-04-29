<?php
/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 15/01/21
 * Time: 01:05
 */

namespace App\datasources;


class Datasource{

    private static $instance;

    protected $model;
    protected $modelFillable;
    protected $datasourceConfig;

    public static function getInstance(){
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    public function setModel($model){
        //inicializa o model
        $this->getFillable($model);

        $this->model = $model::whereRaw('1=1');
        return $this;
    }

    public function setDatasourceConfig($datasourceConfig){
        $this->datasourceConfig =$datasourceConfig;
        return $this;
    }

    public function getFillable($model){
        $this->modelFillable = (new $model())->getFillable();
    }

    public function get(){

        $page_size = $this->datasourceConfig['page_size'];
        $page = $this->datasourceConfig['page'];
        $filters = $this->datasourceConfig['filters'];

        foreach ($filters as $key => $filter){

            //caso seja search procura por qualquer dado dentro
            if($key == 'search'){
                foreach ($this->modelFillable as $index => $fillable) {

                    if($index == 0) {
                        $this->model->where($fillable, 'LIKE', '%' . $filter . '%');
                    }else{
                        $this->model->orWhere($fillable, 'LIKE', '%' . $filter . '%');
                    }
                }
            }
        }

        return $this->model->paginate($page_size, ['*'], 'page', $page);

    }


}