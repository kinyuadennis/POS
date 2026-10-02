@php
    $theme_color = session('business.theme_color', 'blue');
    $color_map = [
        'blue' => '#3c8dbc',
        'purple' => '#605ca8',
        'green' => '#00a65a',
        'red' => '#dd4b39',
        'yellow' => '#f39c12',
        'orange' => '#ff851b',
        'sky' => '#00c0ef',
        'primary' => '#3c8dbc'
    ];
    $primary_hex = isset($color_map[$theme_color]) ? $color_map[$theme_color] : '#3c8dbc';
@endphp

<style>
    /* Search section container */
    .docs-search-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    /* Add button dynamic theme styling */
    .docs-add-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .docs-add-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    /* Hide default Datatables show entries length select and export buttons */
    #documents_and_notes_table_wrapper > .row:first-child,
    #documents_and_notes_table_wrapper .dataTables_length,
    #documents_and_notes_table_wrapper .dt-buttons {
        display: none !important;
    }

    /* Enforce single-line text and uniform row heights */
    #documents_and_notes_table th,
    #documents_and_notes_table th *,
    #documents_and_notes_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Hide native Datatables processing alert */
    .dataTables_processing,
    #documents_and_notes_table_processing {
        display: none !important;
    }

    /* DataTable wrapper styling */
    .docs-datatable-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
</style>

<!-- Custom Search & Action Section -->
<div class="docs-search-section">
    <!-- Top Row: Search Input + Add Button -->
    <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
        <!-- Search Input -->
        <div style="flex-grow: 1;">
            <div class="tw-relative" style="position: relative; width: 100%;">
                <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="custom_docs_search" 
                       placeholder="Search documents and notes..." 
                       class="form-control"
                       style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
            </div>
        </div>
        
        <!-- Add Button -->
        @if(in_array('create', $permissions))
            <button type="button" class="tw-dw-btn docs-add-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0 docs_and_notes_btn" 
                    data-href="{{action([\App\Http\Controllers\DocumentAndNoteController::class, 'create'], ['notable_id' => $notable_id, 'notable_type' => $notable_type])}}"
                    style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700; cursor: pointer; border: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                @lang('messages.add')
            </button> 
        @endif
    </div>

    <!-- Bottom Row: Show Entries and Export dropdown below search on the left -->
    <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
        <!-- Static length selector -->
        <div style="display: inline-block;">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                @lang('lang_v1.show') 
                <select id="custom_docs_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                    <option value="-1">@lang('lang_v1.all')</option>
                </select>
                @lang('lang_v1.entries')
            </label>
        </div>

        <!-- Static Export button dropdown -->
        <div style="display: inline-block;">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 8px 16px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px;">
                    <i class="fa fa-share-square"></i> @lang('lang_v1.export') <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-left" style="border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 8px; min-width: 120px;">
                    <li><a href="#" class="docs-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                    <li><a href="#" class="docs-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Custom Datatable Container -->
<div class="docs-datatable-section">
    <div class="table-responsive">
        <table class="table table-bordered table-striped dataTable" style="width: 100%;" id="documents_and_notes_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th class="sorting">@lang('lang_v1.heading')</th>
                    <th class="sorting">@lang('lang_v1.added_by')</th>
                    <th class="sorting">@lang('lang_v1.created_at')</th>
                    <th class="sorting">@lang('lang_v1.updated_at')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade docus_note_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>