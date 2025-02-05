<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Tag;
use Illuminate\Http\Request;

//В данном случае контроллер погает фильтровать продукты по категориям, тегам и цене, а также сортирует их по цене, популярности и рейтингу.
class FilterListController extends Controller
{
    public function getSort($data)
    {
        $searchProd = Product::where('title', 'LIKE', "%{$data['search']}%");

        switch ($data['sort'])
        {
            case 1:
                $sortProduct = $searchProd->orderBy('price', 'ASC');
                break;
            case 2:
                $sortProduct = $searchProd->orderBy('price', 'DESC');
                break;
            case 3:
                $sortProduct = $searchProd->orderBy('price');
                break;
            case 4:
                $best = OrderItem::query()->groupBy('prod_id')->selectRaw('sum(qty) as total_qty, prod_id')
                    ->orderBy('total_qty', 'DESC')->get();
                $best_prod = [];
                foreach ($best as $k){array_push($best_prod, $k->prod_id);}
                $placeholders = implode(',',array_fill(0, count($best_prod), '?'));
                $sortProduct = $searchProd->whereIn('id', $best_prod)->orderByRaw("field(id,{$placeholders})", $best_prod);
                break;
            case 5:
                $top = Rating::query()->groupBy('product_id')->selectRaw('avg(stars_rating) as total_stars, product_id')
                    ->orderBy('total_stars', 'DESC')->get();
                $top_prod = [];
                foreach ($top as $k){array_push($top_prod, $k->product_id);}
                $placeholders = implode(',',array_fill(0, count($top_prod), '?'));
                $sortProduct = $searchProd->whereIn('id', $top_prod)->orderByRaw("field(id,{$placeholders})", $top_prod);
                break;
        }
        return $sortProduct;

    }
    public function getFilters()
    {
        $categories = Category::all();
        $tags = Tag::all();

        $maxPrice = Product::orderBy('price', 'DESC')->first()->price;
        $minPrice = Product::orderBy('price', 'ASC')->first()->price;

        $res = [
            'categories' => $categories,
            'tags' => $tags,
            'priceMin' => $minPrice,
            'priceMax' => $maxPrice,
        ];
        return $res;
    }
}
