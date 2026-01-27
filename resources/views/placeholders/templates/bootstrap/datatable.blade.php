<div>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm" role="status" aria-live="polite" aria-label="Loading">
            <!-- Header Skeleton -->
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="row g-3 align-items-center">
                    <!-- Search Skeleton -->
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0">
                                <div class="placeholder placeholder-wave" style="width: 20px; height: 20px;"></div>
                            </span>
                            <div class="form-control bg-light placeholder placeholder-wave"></div>
                        </div>
                    </div>

                    <!-- Controls Skeleton -->
                    <div class="col-md-6 col-lg-7">
                        <div class="d-flex gap-2 justify-content-end flex-wrap align-items-center">
                            <!-- Per Page Skeleton -->
                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                <div class="form-select form-select-sm bg-light placeholder placeholder-wave"
                                    style="height: 32px;"></div>
                            </div>

                            <!-- Filter Button Skeleton -->
                            <button class="btn btn-sm btn-outline-primary placeholder" disabled
                                style="width: 90px;"></button>

                            <!-- Export Button Skeleton -->
                            <button class="btn btn-sm btn-outline-info placeholder" disabled
                                style="width: 90px;"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Skeleton -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                    <thead class="table-light">
                        <tr class="fw-600">
                            <th scope="col" class="border-bottom-2 text-secondary fw-600 py-3 px-4">
                                <div class="placeholder placeholder-wave" style="width: 60px; height: 16px;"></div>
                            </th>
                            <th scope="col" class="border-bottom-2 text-secondary fw-600 py-3 px-4">
                                <div class="placeholder placeholder-wave" style="width: 80px; height: 16px;"></div>
                            </th>
                            <th scope="col" class="border-bottom-2 text-secondary fw-600 py-3 px-4">
                                <div class="placeholder placeholder-wave" style="width: 100px; height: 16px;"></div>
                            </th>
                            <th scope="col" class="border-bottom-2 text-secondary fw-600 py-3 px-4">
                                <div class="placeholder placeholder-wave" style="width: 70px; height: 16px;"></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="placeholder placeholder-wave" style="width: 40px; height: 16px;"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="placeholder placeholder-wave" style="width: 120px; height: 16px;"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="placeholder placeholder-wave" style="width: 100px; height: 16px;"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="placeholder placeholder-wave" style="width: 80px; height: 16px;"></div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Pagination Skeleton -->
            <nav class="card-footer bg-white border-top py-3 px-4" aria-label="Page navigation">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <div class="placeholder placeholder-wave" style="width: 200px; height: 16px;"></div>
                    </small>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary placeholder" disabled
                            style="width: 40px; height: 32px;"></button>
                        <button class="btn btn-sm btn-outline-secondary placeholder" disabled
                            style="width: 40px; height: 32px;"></button>
                        <button class="btn btn-sm btn-outline-secondary placeholder" disabled
                            style="width: 40px; height: 32px;"></button>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
