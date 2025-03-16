<?php defined('SYSPATH') or die('No direct script access.');

//Реализация аякс контроллера на одной из фреймворков
class Controller_Ajax extends Controller_Application {
	
	public function action_index()
	{
	
	}


    public function action_close_modal()
    {
        $this->auto_render = false;
        $this->is_ajax = true;
        header('content-type: application/json');

        Cookie::set('close_modal_info' , true , 120*3) ;

        echo json_encode(array("status" => "success" ));
    }

    public  function action_show_modal_buy_one_click(){
	    $priceitem = ORM::factory('Priceitem', $this->request->param('id'));
        $items = [
            [
                'id' => 1,
                'priceitem' => $priceitem,
                'qty' => 1,
                'number' => 0
            ]
        ];
        echo View::factory('common/modal_buy_one_click')
            ->bind('items', $items)
            ->render();
        exit();
    }
	
    public function action_show_modal_cart()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        
        $items = array();

        foreach(Cart::instance()->content as $item) {
            if(is_numeric($item['id'])) {
                $priceitem = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
            }
            $items[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem,
                'qty' => $item['qty'],
                'number' => $item['number']
            );
        }
        echo View::factory('common/modal_cart_new')->set('items', $items)->set('db_lang', $this->db_lang)->render();
        exit();
    }

    public function action_show_modal_cart_admin()
    {
        $this->auto_render = false;
        $this->is_ajax = true;
        $items = array();

        foreach(Cart::instance()->content as $item) {
            if (is_numeric($item['id'])) {
                $priceitem = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
            }


            $items[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem,
                'qty' => $item['qty'],
                'number' => $item['number'],
                'discount_id' => $item['discount_id']
            );
        }
        echo View::factory('common/modal_cart_admin')
        ->set('items', $items)
        ->set('db_lang', $this->db_lang)
        ->set('preorder_id', Cookie::get('cart_preorder_id'))
        ->render();
        exit();
    }

    public function action_modal_cart_remove() {
        $this->auto_render = false;
        $this->template->title = __('Корзина');
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';


        $cart_id = $this->request->post('cart_id');;

        if ($cart_id) {
            Cart::instance()->delete($cart_id);
        }

        $cart_result = Cart::instance()->get_count();
        $items = array();

        foreach (Cart::instance()->content as $item) {

            if(is_numeric($item['id'])) {
                $priceitem = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
            }
            $items[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem,
                'qty' => $item['qty'],
                'number' => $item['number']
            );
        }
        
        echo View::factory('common/modal_cart_new')->set('items', $items)->render();
        exit();
    }

    public function action_modal_cart_remove_admin() {
        $this->auto_render = false;

        $cart_id = $this->request->post('cart_id');
        $preorder_id = $this->request->post('id');

        if ($cart_id) {
            Cart::instance()->delete($cart_id);

            if ($preorder_id) {
                $item = ORM::factory('Preorderitem')
                ->where('order_id', '=', $preorder_id)
                ->and_where('item_id', '=', $cart_id)
                ->find();

                if (!empty($item))
                  $item->delete();
            }
        } else {
            $choices = $this->request->post('choices');

            if ($choices) {
                foreach ($choices as $cart_id) {
                    Cart::instance()->delete($cart_id);

                    if ($preorder_id) {
                        $item = ORM::factory('Preorderitem')
                        ->where('order_id', '=', $preorder_id)
                        ->and_where('item_id', '=', $cart_id)
                        ->find();

                        if (!empty($item))
                          $item->delete();
                    }
                }
            }
        }

        $items = array();

        foreach(Cart::instance()->content as $item) {
            if (is_numeric($item['id'])) {
                $priceitem = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
            }
            $items[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem,
                'qty' => $item['qty'],
                'number' => $item['number'],
                'discount_id' => $item['discount_id']
            );
        }

        if (!count($items))
          Cookie::delete('cart_preorder_id');

        echo View::factory('common/modal_cart_admin')->set('items', $items)->set('db_lang', $this->db_lang)->set('preorder_id', Cookie::get('cart_preorder_id'))->render();
        exit();
    }

    public function action_get_number_cart()
    {
        $cart_result = Cart::instance()->get_count();

        $admin = $this->request->post('admin');

        $cart_count = $cart_result['qty'];

        if ($admin) {
            $cart_count = (Cart::instance()->content) ? count(Cart::instance()->content) : 0;
        }

        echo json_encode(array('status' => 'success', 'cart_count' => $cart_count));
        exit();
    }
	
	public function action_upload_image()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		
		if(!Auth::instance()->logged_in('admin')) return;
		
		$json = array();
		if ($this->request->method() == Request::POST)
        {
            if (isset($_FILES['filename']))
            {
                $json['img'] = 'uploads/'.$this->_save_image($_FILES['filename']);
            }
        }
		
		echo $json['img'];
	}
	
	public function action_check_client_phone()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		header('content-type: application/json');
		
		if(!empty($_POST['id'])) $id = $_POST['id'];
		if(!empty($_POST['phone'])) $phone = $_POST['phone'];
		
		$json = array();
		
		$client = ORM::factory('Client');
		
		$client->where('phone', '=', $phone);
		if(!empty($id)) $client->and_where('id', '!=', $id);

		$json['result'] = ($client->count_all() == 0);

		echo json_encode($json);
	}

	public function action_check_client_email()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		header('content-type: application/json');

		if(!empty($_POST['id'])) $id = $_POST['id'];
		if(!empty($_POST['email'])) $email= $_POST['email'];

		$json = array();

		$client = ORM::factory('Client');

		$client->where('email', '=', $email);
		if(!empty($id)) $client->and_where('id', '!=', $id);

		$json['result'] = ($client->count_all() == 0);

		echo json_encode($json);
	}
	
	protected function _save_image($image)
    {
        if (
            ! Upload::valid($image) OR
            ! Upload::not_empty($image) OR
            ! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
        {
            return FALSE;
        }
 
        $directory = DOCROOT.'uploads/';
 
        if ($file = Upload::save($image, NULL, $directory))
        {
            $filename = date('YmdHis').strtolower(Text::random('alnum', 20)).'.jpg';
 
            Image::factory($file)
                ->save($directory.$filename);
 
            
            unlink($file);
 
            return $filename;
        }
 
        return FALSE;
    }

	public function action_rating()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;

		$this->response->headers('Content-Type', 'application/json');

		$value = Request::current()->post('value');
		$id = Request::current()->post('id');
		$user_id = Request::$client_ip;

        $factory = Request::current()->post('factory');

		$object = ORM::factory($factory)->where('id', '=', $id)->find();

        if ($object->ip) {
			$ip = explode(',', $object->ip);
			if (in_array($user_id, $ip)) {
				echo '0';
				exit();

			} else {
				$ip[] = $user_id;
				$object->rating = ($object->rating * $object->votes + $value) / ($object->votes + 1);
				$object->votes++;

			}
		} else {
			$ip[] = $user_id;
			$object->rating = ($object->rating * $object->votes + $value) / ($object->votes + 1);
			$object->votes++;
		}

		$object->ip = implode(',', $ip);
		$object->save();

		$temp = array(
			'votes' => $object->votes,
			'rating' => $object->rating
		);

		echo json_encode($temp);
		exit();

	}

	public function action_render_menu()
	{
		$this->auto_render = false;
		$this->is_ajax = true;

        echo View::factory('common/categories_menu')->set('db_lang', $this->db_lang)->render();
		exit();
	}

    public function action_render_menu_horizontal()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        echo View::factory('common/categories_menu_horizontal')->set('db_lang', $this->db_lang)->render();
        exit();
    }

	public function action_add_to_cart()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        $this->template->title = __('Корзина покупок');
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        //Cart::instance()->add($priceitem_id, $qty/*, $number*/);
        if (HTTP_Request::POST == $this->request->method()) {
            $priceitem_id = $this->request->post('priceitem_id');
            $qty = $this->request->post('qty');
            $admin = $this->request->post('admin');

            Cart::instance()->add($priceitem_id, $qty/*, $number*/);

            $cart_result = Cart::instance()->get_count();

            $cart_count = $cart_result['qty'];

            if ($admin) {
                $cart_count = (Cart::instance()->content) ? count(Cart::instance()->content) : 0;
            }

            echo json_encode(array(
                'status' => 'success',
                'cart_count' => $cart_count,
                'cart_price' => $cart_result['price'],
            ));
            return;
        }

        //Cart::instance()->add();
    }

    public function action_delete_from_cart()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        $this->template->title = __('Корзина покупок');
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            $priceitem_id = $this->request->post('priceitem_id');
            $qty = $this->request->post('qty');
            $admin = $this->request->post('admin');

            Cart::instance()->unadd($priceitem_id, $qty);

            $cart_result = Cart::instance()->get_count();

            $cart_count = $cart_result['qty'];

            if ($admin) {
                $cart_count = (Cart::instance()->content) ? count(Cart::instance()->content) : 0;
            }

            echo json_encode(array(
                'status' => 'success',
                'cart_count' => $cart_count,
                'cart_price' => $cart_result['price']
            ));
            return;
        }

    }

    public function action_preorder_get_info()
    {
        $this->auto_render = false;
        $this->is_ajax = true;

        $id = $this->request->post('id');

        $order = ORM::factory('Preorder')->where('id', '=',  $id)->find();

        echo json_encode([
            'name' => $order->name,
            'phone' => $order->phone,
        ]);
        die();
    }

    public function action_exit_preorder_admin()
    {
        $this->auto_render = false;
        $this->is_ajax = true;

        Cookie::delete('cart_preorder_id');

        Cart::instance()->delete();

        exit();
    }

    public function action_preorder_save()
    {
        $this->auto_render = false;
        $this->is_ajax = true;
        $items = array();

        $id = $this->request->post('id');
        $phone = $this->request->post('phone');
        $name = $this->request->post('name');
        $checked = $this->request->post('checked');

        if (!$id) {
            $order = ORM::factory('Preorder');
        } else {
            $order = ORM::factory('Preorder')->where('id', '=', $id)->find();
            $orderitem = ORM::factory('Preorderitem')->where('order_id', '=', $id)->find_all();

            if ($orderitem) {
                foreach ($orderitem as $item)
                  $item->delete();
            }
        }


        $order->state = 0;
        $order->manager_id = Auth::instance()->get_user()->id;
        $order->phone = $phone;
        $order->name = $name;

        $order->save();

        $checked_arr = [];

        if ($checked) {
            $checked_arr = explode(',', $checked);
        }

        foreach(Cart::instance()->content as $item) {
            if ($checked_arr) {
                if (in_array($item['id'], $checked_arr)) {
                    $this->add_priceitem_to_order($item, $order->id);
                }
            }
        }

        Cart::instance()->delete();
        Cookie::delete('cart_preorder_id');
        exit();
    }

    private function add_priceitem_to_order($item, $order_id)
    {
        if (is_numeric($item['id'])) {
            $priceitem = ORM::factory('Priceitem')->where('id', '=', $item['id'])->find();
        } else {
            $json_array = json_decode(base64_decode(str_replace('_', '=', $item['id'])), true);
            $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
        }

            $json_array['price'] = $priceitem->price;
            $json_array['currency_id'] = $priceitem->currency_id;
            $json_array['discount_id'] = $item['discount_id'];
            $json_array['amount'] = $priceitem->amount;
            $json_array['delivery'] = $priceitem->delivery;
            $json_array['supplier_id'] = $priceitem->supplier_id;

                $json_array['part_id'] = $priceitem->part_id;
                $json_array['article'] = $priceitem->part->article_long;
                $json_array['brand'] = $priceitem->part->brand_long;
                $json_array['name'] = $priceitem->part->name;

            try {
                $id = str_replace('=', '_', base64_encode(json_encode($json_array)));
            } catch (Exception $e) {
                $json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
                $id = str_replace('=', '_', base64_encode(json_encode($json_array)));
            }

        $orderitem = ORM::factory('Preorderitem');
        $orderitem->set('item_id', $id);
        $orderitem->set('part_id', $priceitem->part_id);
        $orderitem->set('supplier_id', $priceitem->supplier_id);
        $orderitem->set('article', $priceitem->part->article_long);
        $orderitem->set('brand', $priceitem->part->brand_long);
        $orderitem->set('name', $priceitem->part->name);
        $orderitem->set('suplier_code_tehnomir', $priceitem->suplier_code_tehnomir);
        $orderitem->set('purchase_per_unit', $priceitem->get_price());
        $orderitem->set('purchase_per_unit_in_currency', $priceitem->price);
        $orderitem->set('currency_id', $priceitem->currency_id);
        $orderitem->set('order_id', $order_id);
        $orderitem->set('qty', $item['qty']);
        $orderitem->set('discount_id', $item['discount_id']);
        $orderitem->save();
    }


    public function action_admin_add_to_order()
    {
        $this->auto_render = false;
        $this->is_ajax = true;
        $this->template->title = __('Корзина покупок');
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method()) {
            $choices = $this->request->post('choices');

            if ($choices) {
                Cart::instance()->remove_of_order();

                foreach ($choices as $cart_id)
                  Cart::instance()->add_to_order($cart_id);
            }

            $cart_result = Cart::instance()->get_count();
            $cart_count = (Cart::instance()->content) ? count(Cart::instance()->content) : 0;

            echo json_encode(array('status' => 'success', 'cart_count' => $cart_count, 'cart_price' => $cart_result['price']));
            return;
        }
    }

    public function action_preorder_select()
    {
        $this->auto_render = false;
        $this->is_ajax = true;

        $id = $this->request->post('id');

        if (!$id) {
            die("0");
        }

        $order = ORM::factory('Preorder')->where('id', '=', $id)->find();

        if (empty($order)) {
            die("0");
        }

        $items = ORM::factory('Preorderitem')
        ->where('order_id', '=', $order->id)
        ->find_all();


        if (empty($items)) {
            die("0");
        }

        Cart::instance()->delete();

        foreach ($items as $item) {
			if (is_numeric($item->item_id)) {
				$priceitem = ORM::factory('Priceitem')->where('id', '=', $item->item_id)->find();
			} else {
				$json_array = json_decode(base64_decode(str_replace('_', '=', $item->item_id)), true);
				$priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
			}
            
            $_GET['discount_id'] = $item->discount_id;

            Cart::instance()->add($item->item_id, $item->qty, $priceitem->amount);
        }

        $cart_result = Cart::instance()->get_count();

        if ($cart_result['qty'])
          Cookie::set('cart_preorder_id', $id);
        else
          Cookie::delete('cart_preorder_id');

        echo $cart_result['qty'];
    }

}
