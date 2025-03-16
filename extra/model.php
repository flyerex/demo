<?php defined('SYSPATH') or die('No direct script access.');
 
//Реализация модели в одном из фреймворков
class Model_Brand extends ORM {
    protected $_table_name = 'brands';
	
	protected $_has_many = array(
		'brandrules'  => array(
			'model'       => 'Brandrule',
			'foreign_key' => 'brand_id',
		),
    );

	public function apply_rules($article) {
		$trim_charset = " \t\n\r\0.'\"(),";
		$article = trim($article, $trim_charset);
		foreach($this->brandrules->find_all()->as_array() as $brandrule) {
			if($brandrule->type == "delete_start") {
				$article = preg_replace('/^'.preg_quote($brandrule->value, '/').'/i', '', $article);
				$article = trim($article, $trim_charset);
			}
			else if($brandrule->type == "delete_end") {
				$article = preg_replace('/'.preg_quote($brandrule->value, '/').'$/i', '', $article);
				$article = trim($article, $trim_charset);
			}
		}
		
		return $article;
	}
	//Пример рекурсии в даталеере
	public function get_brand($brand_long, $operation_id = false, $recursion_level = 0, $create_brand = false, $tecdoc = NULL) {
		$trim_charset = " \t\n\r\0.'\"(),";
		
		$brand_long = trim($brand_long, $trim_charset);
		$brand = Article::get_short_article($brand_long);
		
		$brand_instance = ORM::factory('Brand')->where('brand', '=', $brand)->find();
		if(empty($brand_instance->id)) {
            return 'bad_brand';
		} else {
			if(!empty($brand_instance->change_to)) {
				$brand_long = trim($brand_instance->change_to, $trim_charset);
				if($recursion_level < 10)				
					$brand_instance = $this->get_brand($brand_long, $operation_id, ($recursion_level + 1));
			}
		}
		return $brand_instance;
	}
	
	public function get_short_change_to($brand_long) {
		$trim_charset = " \t\n\r\0.'\"(),";
		$brand_long = trim($brand_long, $trim_charset);
		$brand = Article::get_short_article($brand_long);
		$brand_instance = ORM::factory('Brand')->where('brand', '=', $brand)->find();
		if(!empty($brand_instance->id)) {
			$brand_long = trim($brand_instance->change_to, $trim_charset);
			$brand = Article::get_short_article($brand_long);
		}
		return $brand;
	}
	
	public function get_short_change_to_back($brand) {
		$query = DB::select('brand')->from('brands_old');
		$query = $query->where('change_to_short', '=', $brand);
		
		$result = $query->execute()->as_array(NULL, 'brand');
		return $result;
	}
}
