<?php

namespace App\Http\Controllers\Panel;

use App\Enums\BundleSemsterEnum;
use App\Exports\WebinarStudents;
use App\Http\Controllers\Controller;
use App\Mixins\RegistrationPackage\UserPackage;
use App\Models\Bundle;
use App\Models\BundleFilterOption;
use App\Models\BundleWebinar;
use App\Models\Category;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Tag;
use App\Models\Translation\BundleTranslation;
use App\Models\Translation\WebinarTranslation;
use App\Models\Webinar;
use App\Models\WebinarFilterOption;
use App\Models\WebinarPartnerTeacher;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class BundlesController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isUser()) {
            abort(404);
        }

        /* الكود القديم اللذي ينفذ فانكشن لعرض جميع الدبلومات الخاصة بالمدرس في حالة انه تم اضافتها بنفسه */
        /*
            $query = Bundle::where(function ($query) use ($user) {
                $query->where('bundles.teacher_id', $user->id);
                $query->orWhere('bundles.creator_id', $user->id);
            });
        */



        $query = Bundle::where(function ($query) use ($user) {
                $query->join('bundle_webinars', 'bundle_webinars.creator_id', 'bundles.id');
            })
            ->with([
                'bundleWebinars' => function ($query) {
                    $query->with([
                        'webinar'
                    ]);
                    $query->orderBy('order', 'asc');
                }
            ])
            ->first();


        $bundlesHours = deepClone($query)->join('bundle_webinars', 'bundle_webinars.bundle_id', 'bundles.id')
            ->join('webinars', 'webinars.id', 'bundle_webinars.webinar_id')
            ->select('bundles.*', DB::raw('sum(webinars.duration) as duration'))
            ->sum('duration');

        $query->with([
            /*'reviews' => function ($query) {
                $query->where('status', 'active');
            },*/
            'bundleWebinars',
            'category',
            'teacher',
            'sales' => function ($query) {
                $query->where('type', 'bundle')
                    ->whereNull('refund_at');
            }
        ])->orderBy('updated_at', 'desc');

        $bundlesCount = $query->count();

        $bundles = $query->paginate(10);

        $bundleSales = Sale::where('seller_id', $user->id)
            ->where('type', 'bundle')
            ->whereNotNull('bundle_id')
            ->whereNull('refund_at')
            ->get();

        $data = [
            'pageTitle' => trans('update.my_bundles'),
            'bundles' => $bundles,
            'bundlesCount' => $bundlesCount,
            'bundleSalesAmount' => $bundleSales->sum('amount'),
            'bundleSalesCount' => $bundleSales->count(),
            'bundlesHours' => $bundlesHours,
        ];

        return view('web.default.panel.bundle.index', $data);
    }

    public function create()
    {
        return abort(404);
        /*
            $user = auth()->user();

            if (!$user->isTeacher() and !$user->isOrganization()) {
                abort(404);
            }


            $categories = Category::where('parent_id', 612)
                ->with('subCategories')
                ->get();

            $teachers = null;
            $isOrganization = $user->isOrganization();

            if ($isOrganization) {
                $teachers = User::where('role_name', Role::$teacher)
                    ->where('organ_id', $user->id)->get();
            }


            $data = [
                'pageTitle' => trans('update.new_bundle'),
                'teachers' => $teachers,
                'categories' => $categories,
                'isOrganization' => $isOrganization,
                'currentStep' => 1,
                'userLanguages' => getUserLanguagesLists(),
            ];

            return view('web.default.panel.bundle.create', $data);
        */
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $rules = [
            'title' => 'required|max:255',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        if (empty($data['video_demo'])) {
            $data['video_demo_source'] = null;
        }

        if (!empty($data['video_demo_source']) and !in_array($data['video_demo_source'], ['upload', 'youtube', 'vimeo', 'external_link'])) {
            $data['video_demo_source'] = 'upload';
        }

        $bundle = Bundle::create([
            'teacher_id' => $user->isTeacher() ? $user->id : (!empty($data['teacher_id']) ? $data['teacher_id'] : $user->id),
            'creator_id' => $user->id,
            'slug' => Bundle::makeSlug($data['title']),
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'video_demo_source' => $data['video_demo'] ? $data['video_demo_source'] : null,
            'status' => ((!empty($data['draft']) and $data['draft'] == 1) or (!empty($data['get_next']) and $data['get_next'] == 1)) ? Bundle::$isDraft : Bundle::$pending,
            'created_at' => time(),
        ]);

        if ($bundle) {
            BundleTranslation::updateOrCreate([
                'bundle_id' => $bundle->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        $notifyOptions = [
            '[u.name]' => $user->full_name,
            '[item_title]' => $bundle->title,
            '[content_type]' => trans('update.bundle'),
        ];
        sendNotification("bundle_submission", $notifyOptions, $user->id);
        sendNotification("bundle_submission_for_admin", $notifyOptions, 1);
        sendNotification("new_item_created", $notifyOptions, 1);

        $url = '/panel/bundles';
        if ($data['get_next'] == 1) {
            $url = '/panel/bundles/' . $bundle->id . '/step/2';
        }

        return redirect($url);
    }

    public function edit(Request $request, $id, $step = 1)
    {
        $user = auth()->user();
        $isOrganization = $user->isOrganization();

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }
        $locale = $request->get('locale', app()->getLocale());

        $data = [
            'pageTitle' => trans('update.new_bundle_page_title_step', ['step' => $step]),
            'currentStep' => $step,
            'isOrganization' => $isOrganization,
            'userLanguages' => getUserLanguagesLists(),
            'locale' => mb_strtolower($locale),
            'defaultLocale' => getDefaultLocale(),
        ];

        $query = Bundle::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            });

        if ($step == '1') {
            $data['teachers'] = $user->getOrganizationTeachers()->get();
        } elseif ($step == 2) {
            $query->with([
                'category' => function ($query) {
                    $query->with([
                        'filters' => function ($query) {
                            $query->with('options');
                        }
                    ]);
                },
                'filterOptions',
                'tags',
            ]);

            /* Modify the categories variable to display only the diploma-specific categories */
            $categories = Category::where('parent_id', 612)
                ->with('subCategories')
                ->get();

            $data['categories'] = $categories;
        } elseif ($step == 3) {
            $query->with([
                'tickets' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ]);
        } elseif ($step == 4) {
            $query->with([
                'bundleWebinars' => function ($query) {
                    $query->with([
                        'webinar'
                    ]);
                    $query->orderBy('order', 'asc');
                }
            ]);
        } elseif ($step == 5) {
            $query->with([
                'faqs' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ]);
        }

        $bundle = $query->first();

        if (empty($bundle)) {
            abort(404);
        }

        $data['bundle'] = $bundle;

        $data['pageTitle'] = trans('public.edit') . ' ' . $bundle->title;

        $definedLanguage = [];
        if ($bundle->translations) {
            $definedLanguage = $bundle->translations->pluck('locale')->toArray();
        }

        $data['definedLanguage'] = $definedLanguage;

        if ($step == 2) {
            $data['bundleTags'] = $bundle->tags->pluck('title')->toArray();

            $bundleCategoryFilters = !empty($bundle->category) ? $bundle->category->filters : [];

            if (empty($bundle->category) and !empty($request->old('category_id'))) {
                $category = Category::where('id', $request->old('category_id'))->first();

                if (!empty($category)) {
                    $bundleCategoryFilters = $category->filters;
                }
            }

            $data['bundleCategoryFilters'] = $bundleCategoryFilters;
        } elseif ($step == 4) {
            $data['webinars'] = Webinar::select('id', 'creator_id', 'teacher_id')
                ->where('status', Webinar::$active)
                ->where('private', false)
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->get();
        }

        return view('web.default.panel.bundle.create', $data);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $rules = [];
        $data = $request->all();
        $currentStep = $data['current_step'];
        $getStep = $data['get_step'];
        $getNextStep = (!empty($data['get_next']) and $data['get_next'] == 1);
        $isDraft = (!empty($data['draft']) and $data['draft'] == 1);

        $bundle = Bundle::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (empty($bundle)) {
            abort(404);
        }

        if ($currentStep == 1) {
            $rules = [
                'title' => 'required|max:255',
                'thumbnail' => 'required',
                'image_cover' => 'required',
                'description' => 'required',
            ];
        }

        if ($currentStep == 2) {
            $rules = [
                'category_id' => 'required',
            ];
        }

        $bundleRulesRequired = false;
        if (($currentStep == 6 and !$getNextStep and !$isDraft) or (!$getNextStep and !$isDraft)) {
            $bundleRulesRequired = empty($data['rules']);
        }

        $this->validate($request, $rules);


        $data['status'] = ($isDraft or $bundleRulesRequired) ? Bundle::$isDraft : Bundle::$pending;
        $data['updated_at'] = time();

        if ($currentStep == 1) {
            if (empty($data['video_demo'])) {
                $data['video_demo_source'] = null;
            }

            if (!empty($data['video_demo_source']) and !in_array($data['video_demo_source'], ['upload', 'youtube', 'vimeo', 'external_link'])) {
                $data['video_demo_source'] = 'upload';
            }

            BundleTranslation::updateOrCreate([
                'bundle_id' => $bundle->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'seo_description' => $data['seo_description'],
            ]);
        }

        if ($currentStep == 2) {
            BundleFilterOption::where('bundle_id', $bundle->id)->delete();
            Tag::where('bundle_id', $bundle->id)->delete();

            $filters = $request->get('filters', null);
            if (!empty($filters) and is_array($filters)) {
                foreach ($filters as $filter) {
                    BundleFilterOption::create([
                        'bundle_id' => $bundle->id,
                        'filter_option_id' => $filter
                    ]);
                }
            }

            if (!empty($request->get('tags'))) {
                $tags = explode(',', $request->get('tags'));

                foreach ($tags as $tag) {
                    Tag::create([
                        'bundle_id' => $bundle->id,
                        'title' => $tag,
                    ]);
                }
            }
        }

        if ($currentStep == 3) {
            $data['subscribe'] = !empty($data['subscribe']) ? true : false;
            $data['price'] = !empty($data['price']) ? convertPriceToDefaultCurrency($data['price']) : null;
        }

        unset($data['_token'],
            $data['current_step'],
            $data['draft'],
            $data['get_next'],
            $data['partners'],
            $data['tags'],
            $data['filters'],
            $data['ajax'],
            $data['title'],
            $data['description'],
            $data['seo_description'],
        );

        if (empty($data['teacher_id']) and $user->isOrganization() and $bundle->creator_id == $user->id) {
            $data['teacher_id'] = $user->id;
        }

        $bundle->update($data);

        $url = '/panel/bundles';
        if ($getNextStep) {
            $nextStep = (!empty($getStep) and $getStep > 0) ? $getStep : $currentStep + 1;

            $url = '/panel/bundles/' . $bundle->id . '/step/' . (($nextStep <= 6) ? $nextStep : 6);
        }

        if ($bundleRulesRequired) {
            $url = '/panel/bundles/' . $bundle->id . '/step/6';

            return redirect($url)->withErrors(['rules' => trans('validation.required', ['attribute' => 'rules'])]);
        }

        if (!$getNextStep and !$isDraft and !$bundleRulesRequired) {
            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[item_title]' => $bundle->title,
                '[content_type]' => trans('update.bundle'),
            ];
            sendNotification("content_review_request", $notifyOptions, 1);
        }

        return redirect($url);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $bundle = Bundle::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!$bundle) {
            abort(404);
        }

        $bundle->delete();

        return response()->json([
            'code' => 200,
            'redirect_to' => $request->get('redirect_to')
        ], 200);
    }

    public function getContentItemByLocale(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'item_id' => 'required',
            'locale' => 'required',
            'relation' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        $bundle = Bundle::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (!empty($bundle)) {

            $itemId = $data['item_id'];
            $locale = $data['locale'];
            $relation = $data['relation'];

            if (!empty($bundle->$relation)) {
                $item = $bundle->$relation->where('id', $itemId)->first();

                if (!empty($item)) {
                    foreach ($item->translatedAttributes as $attribute) {
                        try {
                            $item->$attribute = $item->translate(mb_strtolower($locale))->$attribute;
                        } catch (\Exception $e) {
                            $item->$attribute = null;
                        }
                    }

                    return response()->json([
                        'item' => $item
                    ], 200);
                }
            }
        }

        abort(403);
    }

    public function exportStudentsList($id)
    {
        $user = auth()->user();

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $bundle = Bundle::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (!empty($bundle)) {
            $sales = Sale::where('type', 'bundle')
                ->where('bundle_id', $bundle->id)
                ->whereNull('refund_at')
                ->whereHas('buyer')
                ->with([
                    'buyer' => function ($query) {
                        $query->select('id', 'full_name', 'email', 'mobile');
                    }
                ])->get();

            if (!empty($sales) and !$sales->isEmpty()) {
                $export = new WebinarStudents($sales);
                return Excel::download($export, trans('panel.users') . '.xlsx');
            }

            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('webinars.export_list_error_not_student'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function courses($id)
    {
        $user = auth()->user();

        if (!$user->isTeacher() and !$user->isOrganization()) {
            abort(404);
        }

        $bundle = Bundle::where('id', $id)
            // ->where(function ($query) use ($user) {
            //     $query->where('creator_id', $user->id)
            //         ->orWhere('teacher_id', $user->id);
            // })
            ->with([
                'bundleWebinars' => function ($query) {
                    $query->with([
                        'webinar'
                    ]);
                    $query->orderBy('order', 'asc');
                }
            ])
            ->first();

        if (!empty($bundle)) {

            $data = [
                'pageTitle' => trans('product.courses'),
                'bundle' => $bundle
            ];

            return view('web.default.panel.bundle.courses', $data);
        }

        abort(404);
    }

    public function showPlan(Request $request , $id)
    {
        $data['auth_user'] = Auth::user();
        $data['bundle'] = Bundle::query()->findOrFail(decrypt($id));
        $data['pageTitle']   =   $data['bundle']->title;
        $data['first_semester_webinars']    =   $data['bundle']->bundleWebinars()->whereHas('semester' , function($semester){
            $semester->whereName(BundleSemsterEnum::FIRST->value);
        })->get();
        $data['second_semester_webinars']    =   $data['bundle']->bundleWebinars()->whereHas('semester' , function($semester){
            $semester->whereName(BundleSemsterEnum::SECOND->value);
        })->get();
        $data['third_semester_webinars']    =   $data['bundle']->bundleWebinars()->whereHas('semester' , function($semester){
            $semester->whereName(BundleSemsterEnum::THIRD->value);
        })->get();
        $data['fourth_semester_webinars']    =   $data['bundle']->bundleWebinars()->whereHas('semester' , function($semester){
            $semester->whereName(BundleSemsterEnum::FOURTH->value);
        })->get();
        $data['total_registerd_hours_for_first_semester'] = $data['auth_user']->getTotalRegisterdHours(semester_id: $data['first_semester_webinars']->first()?->semester?->id);
        $data['total_registerd_hours_for_second_semester'] = $data['auth_user']->getTotalRegisterdHours(semester_id: $data['second_semester_webinars']->first()?->semester?->id);
        $data['total_registerd_hours_for_third_semester'] = $data['auth_user']->getTotalRegisterdHours(semester_id: $data['third_semester_webinars']->first()?->semester?->id);
        $data['total_registerd_hours_for_fourth_semester'] = $data['auth_user']->getTotalRegisterdHours(semester_id: $data['fourth_semester_webinars']->first()?->semester?->id);
        $data['is_removal_avilable'] = $data['bundle']->latest_webinar_remove_date >= Carbon::today()->toDateString();
        return view('web.default.panel.bundle.plan', $data);
    }

    /**
     * toggle add/remove a bundleWebinar to/from a student.
     */
    public function addOrRemoveBundleWebinarToUser(Request $request)
    {
        try{
            $auth_user = Auth::user();
            $bundle_webinar = BundleWebinar::query()->findOrFail(decrypt($request->bundle_webinar_id));
            $is_removal_avilable = $bundle_webinar->bundle->latest_webinar_remove_date >= Carbon::today()->toDateString();
            $semseter = $bundle_webinar->semester;
            $total_registerd_hours_for_semester = $auth_user->getTotalRegisterdHours(semester_id:$semseter->id);
            $total_hours_after_registering_this_webinar = $total_registerd_hours_for_semester + $bundle_webinar->webinar->number_of_hours;

            // toggling
            $response = [
                'status'    =>  true,
                'message'   =>  __('public.request_success'),
                'table'     =>  $request->table,
                'btn_class' =>  'btn btn-md btn-danger add-remove-btn',
                'btn_text'  =>  __('public.rmv'),
            ];
            if($auth_user->doesStudentHaveThisBundleWebinar($bundle_webinar))
            {
                if(!$is_removal_avilable)
                {
                    $response = [
                        'status'    =>  false,
                        'message'   =>  __('public.latest_removal_date_passed'),
                    ];
                }else{
                    $auth_user->bundleWebinars()->where('bundle_webinar_id' , $bundle_webinar->id)->delete();
                    $response['btn_text'] = __('public.add');
                    $response['btn_class'] =  'btn btn-md btn-primary add-remove-btn';
                }
            }elseif($total_hours_after_registering_this_webinar > $semseter->max_number_of_hours){
                $response = [
                    'status'    =>  false,
                    'message'   =>  __('public.max_number_of_registered_hours' , ['hours' => $semseter->max_number_of_hours]),
                ];
            }elseif($total_registerd_hours_for_semester < $semseter->max_number_of_hours ){
                $auth_user->bundleWebinars()->updateOrCreate([
                    'bundle_webinar_id' =>  $bundle_webinar->id
                ] , [
                    'user_id'   =>  $auth_user->id,
                    'bundle_webinar_id' =>  $bundle_webinar->id
                ]);
            }else{
                $response = [
                    'status'    =>  false,
                    'message'   =>  __('public.request_failed'),
                ];
            }
            $response['total_registered_hours'] =   $auth_user->getTotalRegisterdHours(semester_id: $semseter->id,  bundle_id: $bundle_webinar->bundle->id);
        }catch(Throwable $e)
        {
            $response = [
                    'status'    =>  false,
                    'message'   =>  __('public.request_failed'),
                ];
        }
        return response()->json($response);
    }


    /**
     * Dispaly The StudySchedule Of The Student
     */
    public function studySchedule($id)
    {
        $data['auth_user'] = Auth::user();
        $data['bundle'] = Bundle::query()->findOrFail(decrypt($id));
        return view('web.default.panel.bundle.study_schedule', $data);
    }

}
