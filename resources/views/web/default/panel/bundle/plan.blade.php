@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <section class="mt-35">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ __('public.study_plan_for_bundle', ['bundle' => $bundle->title]) }}</h2>
        </div>
        {{-- first Semester --}}
        @isset($first_semester_webinars->first()?->semester)
            <div class="mt-3">
                <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
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
                                            <th class="text-center">{{ trans('public.subject') }}</th>
                                            <th class="text-center">{{ trans('public.number_of_hours') }}</th>
                                            <th class="text-center">{{ trans('public.add/remove') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($first_semester_webinars as $bundle_webinar)
                                            <tr>
                                                <td>{{ $bundle_webinar->webinar->title }}</td>
                                                <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                <td>
                                                    @if ($auth_user->doesStudentHaveThisBundleWebinar($bundle_webinar))
                                                        @if ($is_removal_avilable)
                                                            <button type="button" class="btn btn-md btn-danger add-remove-btn"
                                                                data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                                data-table="first-semester">{{ __('public.rmv') }}</button>
                                                        @endif
                                                    @else
                                                        <button type="button" class="btn btn-md btn-primary add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="first-semester">{{ __('public.add') }}</button>
                                                    @endif
                                                </td>
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
                <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
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
                                            <th class="text-center">{{ trans('public.subject') }}</th>
                                            <th class="text-center">{{ trans('public.number_of_hours') }}</th>
                                            <th class="text-center">{{ trans('public.add/remove') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($second_semester_webinars as $bundle_webinar)
                                            <tr>
                                                <td>{{ $bundle_webinar->webinar->title }}</td>
                                                <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                <td>
                                                    @if ($auth_user->doesStudentHaveThisBundleWebinar($bundle_webinar))
                                                        <button type="button" class="btn btn-md btn-danger add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="second-semester">{{ __('public.rmv') }}</button>
                                                    @else
                                                        <button type="button" class="btn btn-md btn-primary add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="second-semester">{{ __('public.add') }}</button>
                                                    @endif
                                                </td>
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
                <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
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
                                            <th class="text-center">{{ trans('public.subject') }}</th>
                                            <th class="text-center">{{ trans('public.number_of_hours') }}</th>
                                            <th class="text-center">{{ trans('public.add/remove') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($third_semester_webinars as $bundle_webinar)
                                            <tr>
                                                <td>{{ $bundle_webinar->webinar->title }}</td>
                                                <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                <td>
                                                    @if ($auth_user->doesStudentHaveThisBundleWebinar($bundle_webinar))
                                                        <button type="button" class="btn btn-md btn-danger add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="third-semester">{{ __('public.rmv') }}</button>
                                                    @else
                                                        <button type="button" class="btn btn-md btn-primary add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="third-semester">{{ __('public.add') }}</button>
                                                    @endif
                                                </td>
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
                <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
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
                                            <th class="text-center">{{ trans('public.subject') }}</th>
                                            <th class="text-center">{{ trans('public.number_of_hours') }}</th>
                                            <th class="text-center">{{ trans('public.add/remove') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fourth_semester_webinars as $bundle_webinar)
                                            <tr>
                                                <td>{{ $bundle_webinar->webinar->title }}</td>
                                                <td>{{ $bundle_webinar->webinar->number_of_hours }}</td>
                                                <td>
                                                    @if ($auth_user->doesStudentHaveThisBundleWebinar($bundle_webinar))
                                                        <button type="button" class="btn btn-md btn-danger add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="third-semester">{{ __('public.rmv') }}</button>
                                                    @else
                                                        <button type="button" class="btn btn-md btn-primary add-remove-btn"
                                                            data-bundle_webinar_id="{{ encrypt($bundle_webinar->id) }}"
                                                            data-table="third-semester">{{ __('public.add') }}</button>
                                                    @endif
                                                </td>
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
@endsection

@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        $(document).on('click', '.add-remove-btn', function(e) {
            var src = $(e.target);;
            var bundle_webinar_id = src.attr('data-bundle_webinar_id');
            var table = src.attr('data-table');
            $.post("{{ route('student.add_remove_bundle_webinar') }}", {
                bundle_webinar_id: bundle_webinar_id,
                table: table
            }, function(response) {
                if (response.status) {
                    src.html(response.btn_text);
                    src.removeClass();
                    src.addClass(response.btn_class);
                    $(`#${response.table}-total-registered-hours`).html(response.total_registered_hours);
                } else {
                    alert(response.message);
                }
            });
        });
    </script>

    <script src="/assets/default/js/panel/make_next_session.min.js"></script>
@endpush
