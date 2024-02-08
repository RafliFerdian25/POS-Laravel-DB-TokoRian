const tableLoader = (colSpan, dataImage) => {
    return `<tr>
                <td colspan="${colSpan}" class="text-center">
                    <div class="d-flex w-100 align-items-center justify-content-center py-3">
                        <object data="${dataImage}" type="image/svg+xml" style="height: 48px !important"></object>
                    </div>
                </td>
            </tr>`;
};

const inlineLoader = (dataImage) => {
    return `<div style="display: inline !important">
                <object data="${dataImage}" type="image/svg+xml" style="height: 40px; vertical-align: middle; transform: translate(-4px, -1px); display: inline !important"></object>
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
