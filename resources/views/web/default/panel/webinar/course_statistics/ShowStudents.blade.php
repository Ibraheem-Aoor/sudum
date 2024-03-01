@extends('web.default.panel.layouts.panel_layout')

@section('content')
    <!-- قائمةالطلاب -->
    <section class="mt-35">
        <h2 class="section-title">{{ trans('panel.students_list') }} {{ $webinar->title }}</h2>

        @if(!empty($students) and !$students->isEmpty())
            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <a href="/panel/webinars/{{ $webinar->id }}/export-students-list"  class="d-none d-lg-flex btn btn-sm btn-primary rounded-pill" style="margin-right: 20px; margin-bottom: 30px;">
                        {{ trans('public.export_list') }}
                    </a>



                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table custom-table text-center ">
                                <thead>
                                <tr>
                                    <th class="text-left text-gray">{{ trans('quiz.student') }}</th>
                                    <th class="text-left text-gray">{{ trans('panel.name') }}</th>
                                    <th class="text-left text-gray">{{ trans('public.email') }}</th>
                                    <th class="text-left text-gray">{{ trans('public.mobile') }}</th>
                                    <th class="text-center text-gray">{{ trans('update.progress') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $usersLists = new \Illuminate\Support\Collection($students->items());
                                    $usersLists = $usersLists->merge($unregisteredUsers);
                                @endphp

                                @foreach($usersLists as $user)

                                    <tr>
                                        <td class="text-left">
                                            <div class="user-inline-avatar d-flex align-items-center">
                                                <div class="avatar bg-gray200">
                                                    <img src="{{ $user->getAvatar() }}" class="img-cover" alt="">
                                                </div>
                                                <div class=" ml-5">
                                                    <span class="d-block text-dark-blue font-weight-500">{{ $user->full_name }}</span>
                                                    <span class="mt-5 d-block font-12 text-gray">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-left">
                                            <span class="d-block text-dark-blue font-weight-500">{{ $user->full_name }}</span>
                                        </td>

                                        <td class="text-left">
                                            <span class="mt-5 d-block font-12 text-gray">{{ $user->email }}</span>
                                        </td>

                                        <td class="text-left">
                                            <span class="mt-5 d-block font-12 text-gray">{{ $user->mobile }}</span>
                                        </td>

                                        <td class="align-middle">
                                            <span class="text-dark-blue font-weight-500">{{ $user->course_progress ?? 0 }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-30">
                {{ $students->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>
        @else

            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'studentt.png',
                'title' => trans('update.course_statistic_students_no_result'),
                'hint' =>  nl2br(trans('update.course_statistic_students_no_result_hint')),
            ])
        @endif

    </section>
@endsection
