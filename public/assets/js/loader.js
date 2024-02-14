const tableLoader = (colSpan) => {
    return `<tr>
                <td colspan="${colSpan}" class="text-center">
                    <div class="d-flex w-100 align-items-center justify-content-center py-3">
                        <svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="25" height="25">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#3498db" stroke-width="5" stroke-dasharray="89 89" stroke-linecap="round">
                                <animateTransform attributeName="transform" dur="1s" type="rotate" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                            </circle>
                        </svg>
                    </div>
                </td>
            </tr>`;
};

const inlineLoader = (dataImage) => {
    return `<div style="display: inline !important">
                <svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="25" height="25">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#3498db" stroke-width="5" stroke-dasharray="89 89" stroke-linecap="round">
                        <animateTransform attributeName="transform" dur="1s" type="rotate" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                    </circle>
                </svg>
            </div>`;
};

const loadingScreenLargeOnMobile = () => {
    return `<div class="loading-screen-large d-block d-md-none">
                <div class="w-100 d-flex justify-content-center align-items-center" style="height: 680px !important">
                    <div class="loading-dots">
                        <p id="loadingText" class="mb-0 tracking-widest text-lg fw-bold">Loading...</p>
                    </div>
                </div>
            </div>`;
};

const loadingCalendar = (dataImage) => {
    return `<div class="loading-calendar">
                <div class="d-flex h-100 w-100 align-items-center justify-content-center py-3">
                    <object data="${dataImage}"
                        type="image/svg+xml" style="height: 64px !important"></object>
                </div>
            </div>`;
};
