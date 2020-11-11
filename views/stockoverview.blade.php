@extends('layout.default')

@section('title', $__t('Stock overview'))
@section('activeNav', 'stockoverview')
@section('viewJsName', 'stockoverview')

@push('pageStyles')
<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}"
	rel="stylesheet">
@endpush

@push('pageScripts')
<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title mr-2 order-0">
				@yield('title')
			</h2>
			<h2 class="mb-0 mr-auto order-3 order-md-1 width-xs-sm-100">
				<span id="info-current-stock"
					class="text-muted small"></span>
			</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fas fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stockjournal') }}">
					{{ $__t('Journal') }}
				</a>
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stockentries') }}">
					{{ $__t('Stock entries') }}
				</a>
				@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/locationcontentsheet') }}">
					{{ $__t('Location Content Sheet') }}
				</a>
				@endif
			</div>
		</div>
		<div class="border-top border-bottom my-2 py-1">
			@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			<div id="info-expiring-products"
				data-next-x-days="{{ $nextXDays }}"
				data-status-filter="expiring"
				class="warning-message status-filter-message responsive-button mr-2"></div>
			<div id="info-expired-products"
				data-status-filter="expired"
				class="error-message status-filter-message responsive-button mr-2"></div>
			@endif
			<div id="info-missing-products"
				data-status-filter="belowminstockamount"
				class="normal-message status-filter-message responsive-button"></div>
			<div class="float-right">
				<a class="btn btn-sm btn-outline-info d-md-none mt-1"
					data-toggle="collapse"
					href="#table-filter-row"
					role="button">
					<i class="fas fa-filter"></i>
				</a>
				<a id="clear-filter-button"
					class="btn btn-sm btn-outline-info mt-1"
					href="#">
					{{ $__t('Clear filter') }}
				</a>
			</div>
		</div>
	</div>
</div>
<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"
				id="search"
				class="form-control"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Location') }}</span>
			</div>
			<select class="form-control"
				id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($locations as $location)
				<option value="{{ $location->name }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
			</div>
			<select class="form-control"
				id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->name }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
			</div>
			<select class="form-control"
				id="status-filter">
				<option class="bg-white"
					value="all">{{ $__t('All') }}</option>
				@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				<option value="expiring">{{ $__t('Expiring soon') }}</option>
				<option value="expired">{{ $__t('Already expired') }}</option>
				@endif
				<option value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stock-overview-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Hide/view columns') }}"
							data-table-selector="#stock-overview-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Product group') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Value') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">{{ $__t('Next best before date') }}</th>
					<th class="d-none">Hidden location</th>
					<th class="d-none">Hidden status</th>
					<th class="d-none">Hidden product group</th>
					<th>{{ $__t('Calories') }} ({{ $__t('Per stock quantity unit') }})</th>
					<th>{{ $__t('Calories') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentStock as $currentStockEntry)
				<tr id="product-{{ $currentStockEntry->product_id }}-row"
					class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) table-danger @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('+' . $nextXDays . ' days')) && $currentStockEntry->amount > 0) table-warning @elseif ($currentStockEntry->product_missing) table-info @endif">
					<td class="fit-content border-right">
						<a class="permission-STOCK_CONSUME btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount < 1 || $currentStockEntry->enable_tare_weight_handling == 1) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Consume %1$s of %2$s', '1 ' . $currentStockEntry->qu_unit_name, $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button"
							class="permission-STOCK_CONSUME d-none d-sm-inline-block btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount == 0) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="right"
							title="{{ $__t('Consume all %s which are currently in stock', $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
							data-consume-amount="@if($currentStockEntry->enable_tare_weight_handling == 1){{$currentStockEntry->tare_weight}}@else{{$currentStockEntry->amount}}@endif"
							data-original-total-stock-amount="{{$currentStockEntry->amount}}">
							<i class="fas fa-utensils"></i> {{ $__t('All') }}
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount < 1 || $currentStockEntry->amount == $currentStockEntry->amount_opened || $currentStockEntry->enable_tare_weight_handling == 1) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Mark %1$s of %2$s as open', '1 ' . $currentStockEntry->qu_unit_name, $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}">
							<i class="fas fa-box-open"></i> 1
						</a>
						@endif
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button"
									class="d-inline-block d-sm-none dropdown-item show-as-dialog-link text-danger product-consume-button @if($currentStockEntry->amount == 0) disabled @endif"
									href="#"
									data-toggle="tooltip"
									data-placement="right"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-product-name="{{ $currentStockEntry->product_name }}"
									data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
									data-consume-amount="{{ $currentStockEntry->amount }}">
									<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume all %s which are currently in stock', $currentStockEntry->product_name) }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link permission-SHOPPINGLIST_ITEMS_ADD"
									type="button"
									href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-shopping-cart"></i></span> <span class="dropdown-item-text">{{ $__t('Add to shopping list') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item show-as-dialog-link permission-STOCK_PURCHASE"
									type="button"
									href="{{ $U('/purchase?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-shopping-cart"></i></span> <span class="dropdown-item-text">{{ $__t('Purchase') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link permission-STOCK_CONSUME"
									type="button"
									href="{{ $U('/consume?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume') }}</span>
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item show-as-dialog-link permission-STOCK_TRANSFER @if($currentStockEntry->amount < 1) disabled @endif"
									type="button"
									href="{{ $U('/transfer?embedded&product=' . $currentStockEntry->product_id) }}">
									<span class="dropdown-item-icon"><i class="fas fa-exchange-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Transfer') }}</span>
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link permission-STOCK_INVENTORY"
									type="button"
									href="{{ $U('/inventory?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-list"></i></span> <span class="dropdown-item-text">{{ $__t('Inventory') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell"
									data-product-id="{{ $currentStockEntry->product_id }}"
									type="button"
									href="#">
									<span class="dropdown-item-icon"><i class="fas fa-info"></i></span> <span class="dropdown-item-text">{{ $__t('Show product details') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockentries?embedded&product=') }}{{ $currentStockEntry->product_id }}"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-boxes"></i></span> <span class="dropdown-item-text">{{ $__t('Show stock entries') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal?embedded&product=') }}{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-file-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Stock journal for this product') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal/summary?embedded&product_id=') }}{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-file-archive"></i></span> <span class="dropdown-item-text">{{ $__t('Journal summary for this product') }}</span>
								</a>
								<a class="dropdown-item permission-MASTER_DATA_EDIT"
									type="button"
									href="{{ $U('/product/') }}{{ $currentStockEntry->product_id . '?returnto=%2Fstockoverview' }}">
									<span class="dropdown-item-icon"><i class="fas fa-edit"></i></span> <span class="dropdown-item-text">{{ $__t('Edit product') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-consume-button product-consume-button-spoiled permission-STOCK_CONSUME @if($currentStockEntry->amount < 1) disabled @endif"
									type="button"
									href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-product-name="{{ $currentStockEntry->product_name }}"
									data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
									data-consume-amount="1">
									<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume %1$s of %2$s as spoiled', '1 ' . $currentStockEntry->qu_unit_name, $currentStockEntry->product_name) }}</span>
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/recipes?search=') }}{{ $currentStockEntry->product_name }}">
									<span class="dropdown-item-icon"><i class="fas fa-cocktail"></i></span> <span class="dropdown-item-text">{{ $__t('Search for recipes containing this product') }}</span>
								</a>
								@endif
							</div>
						</div>
					</td>
					<td class="product-name-cell cursor-link"
						data-product-id="{{ $currentStockEntry->product_id }}">
						{{ $currentStockEntry->product_name }}
					</td>
					<td>
						@if($currentStockEntry->product_group_name !== null){{ $currentStockEntry->product_group_name }}@endif
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-amount"
							class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, $currentStockEntry->qu_unit_name, $currentStockEntry->qu_unit_name_plural) }}</span>
						<span id="product-{{ $currentStockEntry->product_id }}-opened-amount"
							class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						@if($currentStockEntry->is_aggregated_amount == 1)
						<span class="pl-1 text-secondary">
							<i class="fas fa-custom-sigma-sign"></i> <span id="product-{{ $currentStockEntry->product_id }}-amount-aggregated"
								class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount_aggregated }}</span> {{ $__n($currentStockEntry->amount_aggregated, $currentStockEntry->qu_unit_name, $currentStockEntry->qu_unit_name_plural) }}
							@if($currentStockEntry->amount_opened_aggregated > 0)<span id="product-{{ $currentStockEntry->product_id }}-opened-amount-aggregated"
								class="small font-italic">{{ $__t('%s opened', $currentStockEntry->amount_opened_aggregated) }}</span>@endif
						</span>
						@endif
						@if(boolval($userSettings['show_icon_on_stock_overview_page_when_product_is_on_shopping_list']))
						@if($currentStockEntry->on_shopping_list)
						<span class="btn btn-link btn-sm text-muted">
							<i class="fas fa-shopping-cart"></i>
						</span>
						@endif
						@endif
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-value"
							class="locale-number locale-number-currency">{{ $currentStockEntry->value }}</span>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">
						<span id="product-{{ $currentStockEntry->product_id }}-next-best-before-date">{{ $currentStockEntry->best_before_date }}</span>
						<time id="product-{{ $currentStockEntry->product_id }}-next-best-before-date-timeago"
							class="timeago timeago-contextual"
							datetime="{{ $currentStockEntry->best_before_date }} 23:59:59"></time>
					</td>
					<td class="d-none">
						@foreach(FindAllObjectsInArrayByPropertyValue($currentStockLocations, 'product_id', $currentStockEntry->product_id) as $locationsForProduct)
						xx{{ FindObjectInArrayByPropertyValue($locations, 'id', $locationsForProduct->location_id)->name }}xx
						@endforeach
					</td>
					<td class="d-none">
						@if($currentStockEntry->best_before_date < date('Y-m-d
							23:59:59',
							strtotime('-'
							. '1'
							. ' days'
							))
							&&
							$currentStockEntry->amount > 0) expired @elseif($currentStockEntry->best_before_date < date('Y-m-d
								23:59:59',
								strtotime('+'
								.
								$nextXDays
								. ' days'
								))
								&&
								$currentStockEntry->amount > 0) expiring @elseif ($currentStockEntry->product_missing) belowminstockamount @endif"
					</td>
					<td class="d-none">
						xx{{ $currentStockEntry->product_group_name }}xx
					</td>
					<td>
						<span class="locale-number locale-number-generic">{{ $currentStockEntry->product_calories }}</span>
					</td>
					<td>
						<span class="locale-number locale-number-generic">{{ $currentStockEntry->calories }}</span>
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $currentStockEntry->product_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade"
	id="stockoverview-productcard-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.productcard')
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
