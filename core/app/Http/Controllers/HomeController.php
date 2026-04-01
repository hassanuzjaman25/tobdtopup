<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Page;
use App\Models\Popup;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Order;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $sliders = Slider::with('media')
            ->where(['status' => Status::ACTIVE])
            ->orderBy('order_column')
            ->get();
        $products = Product::with('media')
            ->where(['status' => Status::ACTIVE])
            ->orderBy('order_column')
            ->get();
        $orders = Order::with(['user', 'variation'])
            ->latest()
            ->take(5)
            ->get();
        $categories = Categorie::where('status', 1)->orderBy('slot', 'asc')->get();
        return view('home', compact('sliders', 'products', 'orders', 'categories'));
    }

    public function page(string $slug)
    {
        $page = Page::where(['slug' => $slug, 'status' => Status::ACTIVE])->first();
        if (!$page) {
            abort(404);
        }
        return view('page', compact('page'));
    }

    public function topup(string $slug)
    {
        $product = Product::with(['variations.vouchers' => function ($query) {
            $query->where('status', Status::AVAILABLE);
        }])->where('status', Status::ACTIVE)->where(['slug' => $slug])->first();

        if (!$product) {
            abort(404);
        }

        return view('topup.checkout', compact('product'));
    }

    public function getPopups(Collection $collection, Request $request)
    {
        $popupQuery = Popup::query();
            $firstVisitPopups = $popupQuery->where('status', Status::ACTIVE)
                ->get();
            $collection = $collection->merge($firstVisitPopups);
            return response()->json(['popups' => $collection]);

        return response()->json(['popups' => $collection]);
    }
}
