const tableEmpty = (colSpan, target) => {
    return `<tr>
                <td colspan="${colSpan}" class="text-center">
                    <div class="d-flex w-100 align-items-center justify-content-center py-3">
                        <p class="mb-0 py-1">Tidak ada data ${target} yang tersedia di tabel</p>
                    </div>
                </td>
            </tr>`;
};

const tableEmptyPlus = (colSpan, target, request, onClick) => {
    return `<tr>
                <td colspan="${colSpan}" class="text-center">
                    <div class="d-flex w-100 align-items-center justify-content-center py-3">
                        <p class="mb-0 py-1">Tidak ada data ${target} yang tersedia di tabel. <span class="btn-anchor" onclick="${onClick}">${request}</span></p>
                    </div>
                </td>
            </tr>`;
};
