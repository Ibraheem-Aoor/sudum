<!-- Modal -->
<div class="d-none" id="StudyScheduleModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('update.add_new_event_to_diploma') }}</h3>

    <form action="{{ route('studyschedule.store') }}" method="POST">
        @csrf
        <div>
            <input type="hidden" name="bundle_id" value="{{ !empty($bundle) ? $bundle->id :'' }}">


            @if(!empty(getGeneralSettings('content_translate')))
                <div class="form-group">
                    <label class="input-label">{{ trans('auth.language') }}</label>
                    <select name="locale" class="form-control ">
                        @foreach($userLanguages as $lang => $language)
                            <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                        @endforeach
                    </select>
                    @error('locale')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            @else
                <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
            @endif

            {{-- الايام --}}
            <div class="form-group mt-15">
                <label class="input-label d-block">{{ trans('update.select_day') }}</label>
                    <select name="day" class="form-control" data-placeholder="{{ trans('update.select_day') }}">
                        <option value="السبت">السبت</option>
                        <option value="الأحد">الأحد</option>
                        <option value="الأثنين">الأثنين</option>
                        <option value="الثلاثاء">الثلاثاء</option>
                        <option value="الأربعاء">الأربعاء</option>
                        <option value="الخميس">الخميس</option>
                        <option value="الجمعة">الجمعة</option>
                    </select>

                    @error('')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

            </div>


            {{-- المادة --}}
            <div class="form-group mt-15">
                <label class="input-label d-block">{{ trans('panel.select_course') }}</label>
                <select name="webinar_id" class="form-control" data-bundle-id="{{  !empty($bundle) ? $bundle->id : '' }}" data-placeholder="{{ trans('panel.select_course') }}">
                    @forelse($bundleWebinars as $bundleWebinar)
                        @if(!empty($bundleWebinar->webinar->title))
                            <option value="{{ $bundleWebinar->webinar->id }}">{{ $bundleWebinar->webinar->title }}</option>
                        @endif
                    @empty
                        <option value="" disabled>لا توجد مواد متوفر لهذة الدبلومة</option>
                    @endforelse
                </select>

                @error('webinar_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group mt-15">
                <label class="input-label d-block">{{ trans('update.select_date') }}</label>
                <input type="date" name="date" class="form-control" />
                @error('date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            <div class="row">

                {{-- بداية المحاضرة --}}
                <div class="col-6">
                    <div class="form-group mt-15">
                        <label class="input-label d-block">{{ trans('update.start_from') }}</label>
                        <input type="time" name="start_time" class="form-control" />
                        @error('start_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>
                </div>

                {{-- نهاية المحاضرة --}}
                <div class="col-6">
                    <div class="form-group mt-15">
                        <label class="input-label d-block">{{ trans('update.end_time') }}</label>
                        <input type="time" name="end_time" class="form-control"/>
                        @error('end_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>
                </div>

            </div>


            {{-- ملاحظات --}}
            <div class="form-group mt-15">
                <label class="input-label d-block">{{ trans('update.diplomaNots') }}</label>
                <textarea name="note" class="form-control"></textarea>
                @error('note')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            <div class="mt-30 d-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn-primary">{{ trans('public.save') }}</button>
                <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
            </div>


        </div>
    </form>

</div>
