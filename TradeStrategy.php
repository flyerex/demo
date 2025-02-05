<?php
namespace App\Classes;
use App\Classes\AdaptingMethods;
use App\Classes\OpeningMethods;
use App\Classes\ComponentMethods;
use App\Classes\Strategy;

//Класс стратегии торговли. Содержит методы для анализа данных, установки правил, загрузки данных и т.д.
class TradeStrategy extends Strategy
{
    use AdaptingMethods;
    use OpeningMethods;
    use ComponentMethods;

    public function addOrder($order)
    {
        $this->open_orders[$order->id]=$order;
    }

    public function updateOrder($id, $key, $value)
    {
        $this->open_orders[$id][$key]=$value;
    }

    public function removeOrder($id)
    {
        unset($this->open_orders[$id]);
    }

    public function setPrimarySource($primary_source_id)
    {
        $this->primary_source_id = $primary_source_id;
    }
    
    public function getPrimarySource()
    {
        return $this->primary_source_id;
    }

    public function setRules($rules)
    {
        $this->rules=$rules;
    }

    public function setRuleParams($rule_id, $key, $value)
    {
        if(isset($this->rules[$rule_id]))$this->rules[$rule_id]->params[$key]=$value;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function loadSourcesData($data)
    {
        $this->data=$data;
    }

    public function getLastPrice()
    {
        $last_elem=end($this->data[$this->primary_source_id]);
        return $last_elem["close"];
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function getLeverage()
    {
        return $this->leverage;
    }

    public function getJsonData()
    {
        return json_encode($this->rules);
    }

    public function loadFromJson($data)
    {
        $this->rules=json_decode($data, true);
    }

    public function getHash()
    {
        return md5(json_encode($this->rules));
    }

    public function getSources()
    {
        $sources=array();
        foreach($this->rules as $rule)
        {
            if(!isset($sources[$rule->source]))$sources[$rule->source]=$rule->period;
            else $sources[$rule->source]=max($rule->period,$sources[$rule->source]);
        }
        return $sources;
    }

    public function exponentialMovingAverage($numbers, $n)
    {
        $m   = count($numbers);
        $α   = 2 / ($n + 1);
        $EMA = [];
        $EMA[] = $numbers[0];
        for ($i = 1; $i < $m; $i++) {
            $EMA[] = ($α * $numbers[$i]) + ((1 - $α) * $EMA[$i - 1]);
        }

        return $EMA;
    }

    public function analyze($type)
    {
        $results=array();
        foreach($this->rules as $rule)
        {
            if($rule->type != $type)continue;
            $result=$this->{$rule->method}($rule->params, $rule->source);

            if(isset($result->price) && isset($result->stopPrice))
            {
                if($result->price >= $this->data[$this->primary_source_id][count($this->data[$this->primary_source_id])-1]["low"] && $result->price<=$this->data[$this->primary_source_id][count($this->data[$this->primary_source_id])-1]["high"])continue;
                if($result->stopPrice >= $this->data[$this->primary_source_id][count($this->data[$this->primary_source_id])-1]["low"] && $result->stopPrice<=$this->data[$this->primary_source_id][count($this->data[$this->primary_source_id])-1]["high"])continue;
            }
            $results[]=$result;
        }
                
        return $results;
    }

    public function preload()
    {
        $this->components=array();
        $results=array();
        foreach($this->rules as $rule)
        {
            if($rule->type!=4)continue;
            $this->components[$rule->component]=$this->{$rule->method}($rule->params,$rule->source);
        }

        return $results;
    }
}
?>