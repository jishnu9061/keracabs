@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Thalassery - Kannur</h4>
            <div class="page-title-right">
                <button class="btn btn-primary" id="add-stage-btn">
                    <i class="bx bx-plus"></i> Add Stages
                </button>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- Display Validation Errors -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="stages-form" method="POST" action="{{ route('stage.store') }}">
                    @csrf
                    <input type="hidden" name="route_id" value="{{ $route->id }}">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table class="table table-editable table-bordered table-nowrap align-middle table-edits">
                            <tbody id="stages-table-body">
                                <!-- Existing stages can be displayed here -->
                                @foreach ($existingStages as $index => $stage)
                                    <tr data-id="{{ $index }}">
                                        <td><input type="text" name="stages[{{ $index }}][stage_name]" class="form-control" value="{{ $stage['stage_name'] }}" /></td>
                                        @foreach ($stage['prices'] as $priceIndex => $price)
                                            <td><input type="number" name="stages[{{ $index }}][prices][]" class="form-control" value="{{ $price }}" /></td>
                                        @endforeach
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="bx bx-x"></i> Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Submit Button -->
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save"></i> Save Stages
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>
@endsection

<!-- JAVASCRIPT -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metismenu/8.0.0/metisMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/5.3.6/simplebar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/2.0.5/waves.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pace-js/1.2.4/pace.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/table-edits/1.2.4/table-edits.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>

<script>
    $(document).ready(function() {
        let stageCount = {{ count($existingStages) }}; // Initialize stage count based on existing stages
        let existingStages = @json($existingStages); // Get existing stages data

        // Function to create price input columns based on the last stage's prices
        function createPriceInputs(lastPrices, currentStageCount) {
            let priceColumns = '';

            // For each stage, create the appropriate number of price inputs
            for (let i = 0; i < currentStageCount; i++) {
                // The first price input is always 0; subsequent prices inherit from the last stage
                let priceValue = (i === 0) ? 0 : (lastPrices[i - 1] || 0);
                priceColumns += `
                    <td>
                        <input type="number" name="stages[${stageCount}][prices][]" class="form-control" value="${priceValue}" />
                    </td>
                `;
            }

            return priceColumns;
        }

        // Add stage functionality
        $('#add-stage-btn').on('click', function() {
            stageCount++; // Increment the stage count

            // Get the last stage's prices
            let lastPrices = existingStages.length > 0 ? existingStages[existingStages.length - 1].prices : [];

            // Generate new row with price columns
            let newRow = `
                <tr data-id="${stageCount}">
                    <td>
                        <input type="text" name="stages[${stageCount}][stage_name]" class="form-control" placeholder="Stage Name" />
                    </td>
                    ${createPriceInputs(lastPrices, stageCount)} <!-- Call function to create price inputs -->
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="bx bx-x"></i> Remove
                        </button>
                    </td>
                </tr>
            `;

            $('#stages-table-body').append(newRow);
            existingStages.push({
                prices: [0, ...lastPrices]
            });
        });

        // Remove row functionality
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            stageCount = $('#stages-table-body tr').length;
        });
    });
</script>








