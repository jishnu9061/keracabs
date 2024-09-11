@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Fare Fee</h4>
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
                <form id="stages-form" method="POST" action="{{ route('fare.store') }}">
                    @csrf
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table class="table table-editable table-bordered table-nowrap align-middle table-edits">
                            <tbody id="stages-table-body">
                                @if($fareFee && $fareFee->price_data)
                                    @php
                                        $stages = json_decode($fareFee->price_data, true);
                                    @endphp
                                    @foreach($stages as $index => $stage)
                                        <tr data-id="{{ $index }}">
                                            <td>
                                                <input type="hidden" name="stages[{{ $index }}][stage_name]" value="{{ $stage['stage_name'] }}" />
                                                {{ $stage['stage_name'] }}
                                            </td>
                                            @foreach($stage['prices'] as $price)
                                                <td>
                                                    <input type="number" name="stages[{{ $index }}][prices][]" class="form-control" value="{{ $price }}" />
                                                </td>
                                            @endforeach
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="bx bx-x"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
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
        let stageCount = $('#stages-table-body tr').length; // Initialize stageCount from existing rows

        // Add stage functionality
        $('#add-stage-btn').on('click', function() {
            stageCount++; // Increment the stage count

            // Create price columns based on the current stage number
            let priceColumns = Array.from({length: stageCount}, (_, i) => `
                <td><input type="number" name="stages[${stageCount}][prices][]" class="form-control" value="0" /></td>
            `).join('');

            // Create a new row with sequential numbering for the stage
            let newRow = `
                <tr data-id="${stageCount}">
                    <td><input type="hidden" name="stages[${stageCount}][stage_name]" value="${stageCount}" />${stageCount}</td>
                    ${priceColumns}
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="bx bx-x"></i> Remove
                        </button>
                    </td>
                </tr>
            `;

            // Append the new row to the table body
            $('#stages-table-body').append(newRow);
        });

        // Remove row functionality
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateStageNumbers(); // Update stage numbers after removing a row
        });

        // Function to update stage numbers
        function updateStageNumbers() {
            $('#stages-table-body tr').each(function(index) {
                let stageNumber = index + 1;
                $(this).find('td:first').html(`<input type="hidden" name="stages[${stageNumber}][stage_name]" value="${stageNumber}" />${stageNumber}`);
            });
        }
    });
</script>
