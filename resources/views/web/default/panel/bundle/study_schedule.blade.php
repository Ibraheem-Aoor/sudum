@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <section class="mt-35">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ __('update.studyschedule_for_bundle', ['bundle' => $bundle->title]) }}</h2>
        </div>
        <div class="mt-3">
            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        @if (!empty($bundle->Studyschedule) and !$bundle->Studyschedule->isEmpty())
                            <div class="table-responsive">
                                <table class="table table-striped text-center font-14">

                                    <tr>
                                        <th>{{ trans('update.diplomaSubject') }}</th>
                                        <th>{{ trans('update.select_day') }}</th>
                                        <th>{{ trans('update.select_date') }}</th>
                                        <th>{{ trans('update.start_from') }}</th>
                                        <th>{{ trans('update.end_time') }}</th>
                                        <th>{{ trans('update.diplomaNots') }}</th>

                                    </tr>

                                    @foreach ($bundle->Studyschedule as $Studyschedule)
                                        @if ($auth_user->hasRegisteredWebinarForBundle($Studyschedule->bundle_id, $Studyschedule->webinar_id))
                                            <tr>
                                                <th>{{ $Studyschedule->webinar->title }}</th>
                                                <td>{{ $Studyschedule->day }}</td>
                                                <td>{{ $Studyschedule->date }}</td>
                                                <td>{{ $Studyschedule->start_time }}</td>
                                                <td>{{ $Studyschedule->end_time }}</td>
                                                <td>{{ $Studyschedule->note }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                </table>
                            </div>
                        @else
                            @include('admin.includes.no-result', [
                                'file_name' => 'comment.png',
                                'title' => trans('update.Studyschedule_no_result'),
                                'hint' => trans('update.Studyschedule_no_result_hint'),
                            ])
                        @endif

                    </div>
                </div>
            </div>
        </div>

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
