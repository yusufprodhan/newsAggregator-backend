<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NewsController extends BaseController
{
    /**
     * @get user news list
     * @param Request $request [pageSize, pageNumber, user_id, filters]
     * @return object
     * @author yusuf
     */
    public function newsList(Request $request): object
    {
        try {
            #set page size
            $pageSize = $request->input('pageSize');
            #set user variable initialize
            $user = null;
            #get user data
            if($request->input('user_id')){
                $user = User::find($request->input('user_id'))->first();
            }

            #initial news model query
            $query = News::query();

            #keyword search
            if($request->input('keyword') !== null){
                $query->when($request->input('keyword'), function ($q) use ($request) {
                    return $q->orwhere('title', 'LIKE', '%'.$request->input('keyword').'%')
                        ->orwhere('content', 'LIKE', '%'.$request->input('keyword').'%')
                        ->orwhere('description', 'LIKE', '%'.$request->input('keyword').'%');
                });
            }
            #category condition
            if($request->input('category') !== null){
                #user filtered category condition
                $query->when($request->input('category'), function ($q) use ($request) {
                    return $q->orWhereIn('category',explode(',',$request->input('category')));
                });
            }else{
                #user favourite category condition
                $query->when($user && $user->fav_category, function ($q) use ($user) {
                    return $q->orWhereIn('category',json_decode($user->fav_category));
                });
            }
            #source condition
            if($request->input('source_name') !== null){
                #user filtered source condition
                $query->when($request->input('source_name'), function ($q) use ($request) {
                    return $q->orWhereIn('source_name',explode(',',$request->input('source_name')));
                });
            }else{
                #user favourite source condition
                $query->when($user && $user->fav_source, function ($q) use ($user) {
                    return $q->orWhereIn('source_name',json_decode($user->fav_source));
                });
            }

            #author condition
            if($request->input('author') !== null){
                #user filtered author condition
                $query->when($request->input('author'), function ($q) use ($request) {
                    return $q->orWhereIn('author',explode(',',$request->input('author')));
                });
            }else{
                #user favourite author condition
                $query->when($user && $user->fav_author, function ($q) use ($user) {
                    return $q->orWhereIn('author',json_decode($user->fav_author));
                });
            }
            #published condition
            if($request->input('publishedAt') !== null){
                $query->when($request->input('publishedAt'), function ($q) use ($request) {
                    $to = date('Y-m-d', strtotime($request->input('publishedAt'))).' 00:00:00';
                    $from = date('Y-m-d', strtotime($request->input('publishedAt'))).' 23:00:00';
                    return $q->orwhereBetween('publishedAt',[$to, $from]);
                });
            }
            #execute condition
            $news =$query
                    ->orderBy('publishedAt','DESC')
                    ->paginate($pageSize);
            #all categories
            $data['news'] = $news;
            $data['categories'] = Helper::$categories;
            $data['author'] = Helper::getAuthor();
            $data['source'] = Helper::getSource();

          return  $this->sendResponse($data,__('locale.exceptions.news_list'));
        }catch (\Throwable $th){
            return $this->sendError($th->getMessage(),['reason'=>$th->getMessage()],500);
        }
    }
}
