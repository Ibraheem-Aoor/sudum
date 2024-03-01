@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <style>
        .select2-container {
            z-index: 1212 !important;
        }
        table tr th:first-child, table tr td:first-child,table tr th:last-child, table tr td:last-child{
            text-align: initial !important;
        }
        table tfoot tr td:first-child p {
            text-align: left !important;

        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $bundle->title }} - {{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item"><a>{{ $pageTitle }}</a></div>
            </div>
        </div>
    </section>


    <section class="card">
        <div class="card-body">
            <section class="mt-35">
                <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                    <h2 class="section-title">{{ __('public.study_plan_for_bundle', ['bundle' => $bundle->title]) }}</h2>
                    <h2 class="section-title">{{ __('public.student') }} : {{ $student->full_name }}</h2>
                </div>
                {{-- first Semester --}}
                @isset($first_semester_webinars->first()?->semester)
                    <div class="mt-3">
                        <div
                            class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                            <h2 class="section-title">{{ __('public.semester_trans.First Semester') }}
                                <small>({{ __('public.max_number_of_registered_hours', ['hours' => $second_semester_webinars->first()?->semester?->max_number_of_hours]) }})</small>
                            </h2>
                        </div>
                        <div class="panel-section-card py-20 px-25 mt-20">
                            <div class="row">
                                <div class="col-12 ">
                                    <div class="table-responsive">
                                        <table class="table custom-table" id="first-semester-table">
                                            <thead>
                                                <tr>
                                                    <th class="">{{ trans('public.subject') }}</th>
                                                    <th class="">{{ trans('public.number_of_hours') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($first_semester_webinars as $bundle_webinar)
                                                    <tr>
                                                        <td>{{ $bundle_webinar->webinar->title }}</td>
                                                        <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        <p>{{ __('public.total_registered_hours') }}: <span
                                                                id="first-semester-total-registered-hours">{{ $total_registerd_hours_for_first_semester }}</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset

                {{-- Second Semester. --}}
                @isset($second_semester_webinars->first()?->semester)
                    <div class="mt-3">
                        <div
                            class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                            <h2 class="section-title">{{ __('public.semester_trans.second Semester') }}
                                <small>({{ __('public.max_number_of_registered_hours', ['hours' => $second_semester_webinars->first()?->semester?->max_number_of_hours]) }})</small>
                            </h2>
                        </div>
                        <div class="panel-section-card py-20 px-25 mt-20">
                            <div class="row">
                                <div class="col-12 ">
                                    <div class="table-responsive">
                                        <table class="table custom-table" id="second-semester-table">
                                            <thead>
                                                <tr>
                                                    <th class="">{{ trans('public.subject') }}</th>
                                                    <th class="">{{ trans('public.number_of_hours') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($second_semester_webinars as $bundle_webinar)
                                                    <tr>
                                                        <td>{{ $bundle_webinar->webinar->title }}</td>
                                                        <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        <p>{{ __('public.total_registered_hours') }}: <span
                                                                id="second-semester-total-registered-hours">{{ $total_registerd_hours_for_second_semester }}</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset

                {{-- Third Semester. --}}
                @isset($third_semester_webinars->first()?->semester)
                    <div class="mt-3">
                        <div
                            class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                            <h2 class="section-title">{{ __('public.semester_trans.third Semester') }}
                                <small>({{ __('public.max_number_of_registered_hours', ['hours' => $third_semester_webinars->first()?->semester?->max_number_of_hours]) }})</small>
                            </h2>
                        </div>
                        <div class="panel-section-card py-20 px-25 mt-20">
                            <div class="row">
                                <div class="col-12 ">
                                    <div class="table-responsive">
                                        <table class="table custom-table" id="third-semester-table">
                                            <thead>
                                                <tr>
                                                    <th class="">{{ trans('public.subject') }}</th>
                                                    <th class="">{{ trans('public.number_of_hours') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($third_semester_webinars as $bundle_webinar)
                                                    <tr>
                                                        <td>{{ $bundle_webinar->webinar->title }}</td>
                                                        <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        <p>{{ __('public.total_registered_hours') }}: <span
                                                                id="third-semester-total-registered-hours">{{ $total_registerd_hours_for_third_semester }}</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset

                {{-- Fourth Semester. --}}
                @isset($fourth_semester_webinars->first()?->semester)
                    <div class="mt-3">
                        <div
                            class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                            <h2 class="section-title">{{ __('public.semester_trans.fourth Semester') }}
                                <small>({{ __('public.max_number_of_registered_hours', ['hours' => $fourth_semester_webinars->first()?->semester?->max_number_of_hours]) }})</small>
                            </h2>
                        </div>
                        <div class="panel-section-card py-20 px-25 mt-20">
                            <div class="row">
                                <div class="col-12 ">
                                    <div class="table-responsive">
                                        <table class="table custom-table" id="third-semester-table">
                                            <thead>
                                                <tr>
                                                    <th class="">{{ trans('public.subject') }}</th>
                                                    <th class="">{{ trans('public.number_of_hours') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($fourth_semester_webinars as $bundle_webinar)
                                                    <tr>
                                                        <td>{{ $bundle_webinar->webinar->title }}</td>
                                                        <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        <p>{{ __('public.total_registered_hours') }}: <span
                                                                id="third-semester-total-registered-hours">{{ $total_registerd_hours_for_fourth_semester }}</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset
            </section>
        </div>
    </section>


@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>

    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
    </script>

    <script src="/assets/default/js/admin/webinar_students.min.js"></script>
@endpush
