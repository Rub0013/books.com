<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Book;
use App;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use Stripe\Stripe;

class ChartController extends MainController
{
    public function add_to_chart(Request $request){
        $id = $request->id;
        Session::push('prod_id', $id);
        if(session('prod_chart_quantity')){
            $quantity = Session::get('prod_chart_quantity');
            ++$quantity;
            Session::put('prod_chart_quantity',$quantity);

        }else{
            Session::put('prod_chart_quantity',1);
        }

        return 1;
    }

    public function char_list(Request $request){
        if(session('prod_id')){
            $prod_id = session('prod_id');
            $prod = array_count_values($prod_id);
            $string_in = [];
            foreach ($prod as $key => $value){
                $string_in []=   $key.',';
            }
            $products = Book::whereIn('id', $string_in)->get();
            $res = [];
            foreach ($products as $product){
                $res [$product->id]['id'] = $product->id;
                $res [$product->id]['name'] = $product->name;
                $res [$product->id]['price'] = $product->price;
            }
            $big_total = 0;
            foreach ($prod as $key => $quantity){
                $res [$key]['quantity'] = $quantity;
                $res [$key]['small_total'] = $quantity*$res [$key]['price'];
                $big_total += $res [$key]['small_total'];
            }
            return view('chart',['products'=>$res,'big_total'=>$big_total]);
        }
        return view('chart');


    }

    public function del_current_from_chart(Request $request){
        $deleting = $request->id;
        $quantity = Session::get('prod_chart_quantity');
        $all_prods = Session::get('prod_id');
        Session::forget('prod_id');
        foreach ($all_prods as $key => $sess){
            if (!($sess == $deleting)){
                Session::push('prod_id', $sess);
            }else{
                --$quantity;
            }

        }
        Session::put('prod_chart_quantity',$quantity);
        return 1;
    }

    public function delete_all_from_chars(){
        Session::forget('prod_id');
        Session::forget('prod_chart_quantity');
        return redirect(App::getLocale().'/lybrary');
    }

    public function finish_chart(Request $request){
        if(!Session::has('prod_id')){
            return redirect(App::getLocale().'/lybrary');
        }

        $prod_id = session('prod_id');
        $prod = array_count_values($prod_id);
        $string_in = [];
        foreach ($prod as $key => $value){
            $string_in []=   $key.',';
        }
        $products = Book::whereIn('id', $string_in)->get();
        $res = [];
        foreach ($products as $product){
            $res [$product->id]['id'] = $product->id;
            $res [$product->id]['name'] = $product->name;
            $res [$product->id]['price'] = $product->price;
        }
        $big_total = 0;
        foreach ($prod as $key => $quantity){
            $res [$key]['quantity'] = $quantity;
            $res [$key]['small_total'] = $quantity*$res [$key]['price'];
            $big_total += $res [$key]['small_total'];
        }

        Stripe::setApiKey('sk_test_yooDOxjPU3gN3DCHIMWavQKJ');
        $big_big_total = $big_total*100;
        try{
            \Stripe\Charge::create(array(
                "amount" => $big_big_total,
                "currency" => "usd",
                "source" =>$request->input('stripeToken'), // obtained with Stripe.js
                "description" => "Charge"
            ));
        }catch (\Exception $e ){
            return redirect("/en/char_list")->with('errors', $e->getMessage());//
        }
        Session::forget('prod_id');
        Session::forget('prod_chart_quantity');
        return redirect("/en/lybrary")->with('success', "everything OK");
    }

}
