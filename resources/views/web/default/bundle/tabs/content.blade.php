{{-- course FAQ --}}
@if (!empty($bundle->bundleWebinars) and $bundle->bundleWebinars->count() > 0)
    <div class="mt-20">
        <h2 class="section-title after-line">{{ trans('product.courses') }}</h2>

        @foreach ($bundle->bundleWebinars as $bundleWebinar)
            @if (!empty($bundleWebinar->webinar) && isset($user) && $user->doesStudentHaveThisBundleWebinar($bundleWebinar))
                @include('web.default.includes.webinar.list-card-diploma', [
                    'webinar' => $bundleWebinar->webinar,
                    'bundle' => $bundle,
                ])
            @endif
        @endforeach
    </div>
@endif

@if (auth()->check())
    @php
        $userId = auth()->id();
        $hasPurchases =
            deepClone($bundle)
                ->whereHas('sales', function ($query) use ($userId) {
                    $query->where('buyer_id', $userId);
                })
                ->count() > 0;
    @endphp

    @if ($hasPurchases)
        <section class="mt-30 d-none">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title after-line">{{ trans('update.studyschedule') }}</h2>
            </div>

            <div class="row mt-10">
                <div class="col-12">
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
                                    <tr>
                                        <th>{{ $Studyschedule->webinar_id }}</th>
                                        <td>{{ $Studyschedule->day }}</td>
                                        <td>{{ $Studyschedule->date }}</td>
                                        <td>{{ $Studyschedule->start_time }}</td>
                                        <td>{{ $Studyschedule->end_time }}</td>
                                        <td>{{ $Studyschedule->note }}</td>
                                    </tr>
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

        </section>
    @else
        <!-- لا تعرض شئ اترك الجزء فاضي للطالب الذي لم يشتري الكورس -->
    @endif
@else
    <!-- لا تعرض شئ اترك الجزء فاضي للزوار -->
@endif
