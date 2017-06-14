<?php

namespace App\Http\Controllers;

use Hash;
use JWTAuth;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index() {

        $news = News::all();
        return response()->json(compact('news', 'id'));
    }
    public function store(Request $request) {

        $objUser = JWTAuth::parseToken()->authenticate();

        $objNews          = new News();
        $objNews->user_id = $objUser->id;
        $objNews->name    = $request->name;
        $objNews->preview = $request->preview;
        $objNews->detail  = $request->detail;

        //Kiểm tra upload hình
        $picture = '';
        if($request->hasFile('picture')) {
          $path     = $request->file('picture')->store('files');
          $tmp      = explode('/', $path);
          $picture  = end($tmp);
        }
        $objNews->picture = $picture;

        $result = $objNews->save();
        return response()->json([
              'msg'    => 'Add success!',
              'result' => $result
        ]);
    }

    public function edit($id) {

        $objNew = News::find($id);
        $objUser = JWTAuth::parseToken()->authenticate();

        if($objUser->username != 'admin' && $objUser->id != $objNew->user_id) {
            return response()->json(['msg' => 'You do not have permission to edit this news!']);
        }else {
            return response()->json(compact('objNew'));
        }
    }

    public function update(Request $request, $id) {

        $objNew   = News::find($id);
        $objUser = JWTAuth::parseToken()->authenticate();

        //Kiểm tra bài đăng của người dùng
        if($objUser->username != 'admin' && $objUser->id != $objNew->user_id) {
            return response()->json(['msg' => 'You do not have permission to edit this news!']);
        }else {
            $picture  = $objNew->picture;
            $objNew->user_id  = $objUser->id;
            $objNew->name     = trim($request->name);
            $objNew->preview  = trim($request->preview);
            $objNew->detail   = trim($request->detail);

            //xử lý ảnh
            if($request->hasFile('picture')){
                $oldPic = $objNew->picture;
                //upload ảnh mới
                $path   = $request->picture->store('files');
                $tmp    = explode('/', $path);
                $newPic = end($tmp);

                //xóa ảnh cũ
                $pathOldPic = storage_path('app/files/'.$oldPic);
                if(is_file($pathOldPic) && ($oldPic != '')) {
                    Storage::delete('files/'.$oldPic);
                }
                $objNew->picture = $newPic;
            }else {
                $objNew->picture = $picture;
            }

            $objNew = $objNew->update();
            return response()->json([
                    'msg'    => 'Edit success!',
                    'result' => $objNew
            ]);
        }
    }

    public function destroy($id) {

        $objNew   = News::find($id);
        $objUser = JWTAuth::parseToken()->authenticate();

        //Kiểm tra bài đăng của người dùng
        if($objUser->username != 'admin' && $objUser->id != $objNew->user_id) {
            return response()->json(['msg' => 'You do not have permission to edit this news!']);
        }else {
            //xóa hình
            $picture  = $objNew->picture;
            $pathOldPic = storage_path('app/files/'.$picture);

            if(is_file($pathOldPic) && ($picture != '')) {
                Storage::delete('files/'.$picture);
            }
            //xóa tin
            if($objNew->delete()) {
                return response()->json('Delete success!');
            }
        }
    }
}
