<?php
 
namespace App\Http\Controllers;
 
use App\Services\Product;
use Illuminate\View\View;
 
class VideoController extends Controller
{
    //Внедряем зависимость где Product является сервисом
    public function __construct(
        protected Product $product,
    ) {}
 
    //Показываем видео товара на основе внедренной зависимости в конструктуре
    public function show(string $id): View
    {
        return view('product.video.show', [
            'video' => $this->product->findVideo($id)
        ]);
    }
}