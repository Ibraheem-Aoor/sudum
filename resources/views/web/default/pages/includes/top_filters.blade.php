<div id="topFilters" class="shadow-lg border border-gray300 rounded-sm p-10 p-md-20">
    <div class="row align-items-center">
        <div class="col-lg-9 d-flex align-items-center">

            <div class="checkbox-button primary-selected">
                <input type="radio" name="card" id="gridView" value="grid"
                    @if (empty(request()->get('card')) or request()->get('card') == 'grid') checked="checked" @endif>
                <label for="gridView" class="bg-white border-0 mb-0">
                    <i data-feather="grid" width="25" height="25"
                        class="@if (empty(request()->get('card')) or request()->get('card') == 'grid') text-primary @endif"></i>
                </label>
            </div>

            <div class="checkbox-button primary-selected ml-10">
                <input type="radio" name="card" id="listView" value="list"
                    @if (!empty(request()->get('card')) and request()->get('card') == 'list') checked="checked" @endif>
                <label for="listView" class="bg-white border-0 mb-0">
                    <i data-feather="list" width="25" height="25"
                        class="{{ (!empty(request()->get('card')) and request()->get('card') == 'list') ? 'text-primary' : '' }}"></i>
                </label>
            </div>

            @if (!isset($is_type_bundle) || (isset($is_type_bundle) && !$is_type_bundle))
                <!-- First Link -->
                <div class="d-flex align-items-center justify-content-between justify-content-md-center mb-2"
                    style="margin-right: 20px;">
                    <label class="mb-0 mr-10 cursor-pointer">
                        <a href="/classes" class="position-relative font-14 text-secondary">جميع الدورات</a>
                    </label>
                </div>
                <!-- Second Link -->
                <div class="d-flex align-items-center justify-content-between justify-content-md-center mb-2"
                    style="margin-right: 20px;">
                    <label class="mb-0 mr-10 cursor-pointer">
                        <a href="/categories/qualifying-courses"
                            class="position-relative font-14 text-secondary">الدورات
                            التأهيلية</a>
                    </label>
                </div>

                <!-- Third Link -->
                <div class="d-flex align-items-center justify-content-between justify-content-md-center mb-2"
                    style="margin-right: 20px;">
                    <label class="mb-0 mr-10 cursor-pointer">
                        <a href="/categories/development-courses"
                            class="position-relative font-14 text-secondary">الدورات
                            التطويرية</a>
                    </label>
                </div>


                <div class="d-flex align-items-center justify-content-between justify-content-md-center mb-2"
                    style="margin-right: 20px;">
                    <label class="mb-0 mr-10 cursor-pointer">
                        <a href="/categories/courses-selected-for-you"
                            class="position-relative font-14 text-secondary">دورات مختارة لك</a>
                    </label>
                </div>
            @endif


        </div>

        <div class="col-lg-3 d-flex align-items-center">
            <select name="sort" class="form-control font-14">
                <option disabled selected>{{ trans('public.sort_by') }}</option>
                <option value="">{{ trans('public.all') }}</option>
                <option value="newest" @if (request()->get('sort', null) == 'newest') selected="selected" @endif>
                    {{ trans('public.newest') }}</option>
                <option value="expensive" @if (request()->get('sort', null) == 'expensive') selected="selected" @endif>
                    {{ trans('public.expensive') }}</option>
                <option value="inexpensive" @if (request()->get('sort', null) == 'inexpensive') selected="selected" @endif>
                    {{ trans('public.inexpensive') }}</option>
                <option value="bestsellers" @if (request()->get('sort', null) == 'bestsellers') selected="selected" @endif>
                    {{ trans('public.bestsellers') }}</option>
                <option value="best_rates" @if (request()->get('sort', null) == 'best_rates') selected="selected" @endif>
                    {{ trans('public.best_rates') }}</option>
            </select>
        </div>

    </div>
</div>
