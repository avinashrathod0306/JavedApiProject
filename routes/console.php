<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('store:bg', function () {

    $client = new \GuzzleHttp\Client();
    $res = $client->request('GET', 'https://threemartians.com/bg/backgrounderaser.php');

    $content = json_decode($res->getBody(), true);

    $imageUrl = 'https://threemartians.com/bg/';

    foreach ($content['data'] as $data) {
        //main Image
        $path = $imageUrl.'bg/'.$data['image'];
        $filename = basename($path);
        Image::make($path)->save(public_path('images/' . $filename));

        //Thumb Image
        $path = $imageUrl.'bg/th/'.$data['thumbImg'];
        $filename = basename($path);
        Image::make($path)->save(public_path('images/th/' . $filename));

        $background = new \App\Models\Background();
        $background->name = $data['name'];
        $background->thumbImg = $data['thumbImg'];
        $background->image = $data['image'];
        $background->save();
    }

})->purpose('Scrap background images and data');


Artisan::command('store:sticker', function () {

    $client = new \GuzzleHttp\Client();
    $res = $client->request('GET', 'https://threemartians.com/bg/sticker.php');

    $content = json_decode($res->getBody(), true);

    $imageUrl = 'https://threemartians.com/bg/Sticker/';


    $category_list = array();

    foreach ($content['thumbnail_bg'] as $data) {
        //main Image

        foreach ($data['category_list'] as $image) {
            $path = $imageUrl.$image['image_url'];
            $urlData = parse_url($path);
            $filename = basename($urlData['path']);
            $dir = str_replace($filename,'',$urlData['path']);
            $dir = str_replace('/bg/Sticker/','/images/stickers/',$dir);

            if (!file_exists(public_path($dir))) {
                // path does not exist
                \Illuminate\Support\Facades\File::makeDirectory(public_path($dir), $mode = 0777, true, true);
            }

            Image::make($path)->save(public_path($dir . $filename));

            $path = $imageUrl.$image['thumb_url'];
            $urlData = parse_url($path);
            $filenameTh = basename($urlData['path']);
            $dirTh = str_replace($filenameTh,'',$urlData['path']);
            $dirTh = str_replace('/bg/Sticker/','/images/stickers/',$dirTh);

            if (!file_exists(public_path($dirTh))) {
                // path does not exist
                \Illuminate\Support\Facades\File::makeDirectory(public_path($dirTh), $mode = 0777, true, true);
            }

            Image::make($path)->save(public_path($dirTh . $filenameTh));

            array_push($category_list,[
                'thumb_url' => $image['thumb_url'],
                'image_url' => $image['image_url'],
            ]);

        }




       // $filename = basename($path);
        $category = new \App\Models\Category();
        $category->category_name = $data['category_name'];
        $category->category_list = json_encode($category_list);
        $category->save();

    }

})->purpose('Scrap stickers and data');
