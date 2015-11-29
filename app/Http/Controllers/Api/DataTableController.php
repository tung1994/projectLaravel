<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Language;
use App\User;
use Carbon\Carbon;
use Datatable;
use Illuminate\Http\Request;

class DataTableController extends Controller
{

    /**
     * Abort if request is not ajax
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if(! $request->ajax() || ! Datatable::shouldHandle()) abort(403, 'Forbidden');
        parent::__construct();
    }

    /**
     * JSON data for seeding Articles
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getArticles()
    {
        return Datatable::collection($this->language->articles)
            ->showColumns('title', 'read_count')
            ->addColumn('category_id', function($model)
            {
                return $model->category->title;
            })
            ->addColumn('published_at', function($model)
            {
                return $model->published_at;
            })
            ->addColumn('updated_at', function($model)
            {
                return $this->setDateTime($model->updated_at);
            })
            ->addColumn('',function($model)
            {
                return get_ops('article', $model->id);
            })
            ->searchColumns('title')
            ->orderColumns('category_id', 'published_at', 'read_count', 'title', 'updated_at')
            ->make();
    }

    /**
     * JSON data for seeding Categories
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getCategories()
    {
        return Datatable::collection($this->language->categories)
            ->showColumns('title')
            ->addColumn('updated_at', function($model)
            {
                return $this->setDateTime($model->updated_at);
            })
            ->addColumn('',function($model)
            {
                return get_ops('category', $model->id);
            })
            ->searchColumns('title')
            ->orderColumns('title', 'updated_at')
            ->make();
    }

    /**
     * JSON data for seeding Languages
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getLanguages()
    {
        return Datatable::collection(Language::all())
            ->showColumns('title', 'code')
            ->addColumn('updated_at', function($model)
            {
                return $this->setDateTime($model->updated_at);
            })
            ->addColumn('',function($model)
            {
                return get_ops('language', $model->id);
            })
            ->searchColumns('title')
            ->orderColumns('code', 'title', 'updated_at')
            ->make();
    }

    /**
     * JSON data for seeding Users
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUsers()
    {
        return Datatable::collection(User::all())
            ->showColumns('name', 'ip_address')
            ->addColumn('logged_in_at', function($model)
            {
                return $this->setDateTime($model->logged_in_at);
            })
            ->addColumn('logged_out_at', function($model)
            {
                return $this->setDateTime($model->logged_out_at);
            })
            ->addColumn('',function($model)
            {
                return get_ops('user', $model->id);
            })
            ->searchColumns('ip_address', 'name')
            ->orderColumns('logged_in_at','logged_out_at', 'name')
            ->make();
    }

    private function setDateTime(Carbon $datetime)
    {
        return $datetime->year > 0 ? $datetime . "<br/><small>(" . $datetime->diffForHumans() . ")</small>" : "-";
    }

}