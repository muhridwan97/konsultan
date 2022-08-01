$(function () {
    const tableOverSpaces = $('#table-over-space');
    const btnCapacityLeft = tableOverSpaces.find('.btn-capacity-left');

    /**
     * Click open modal detail over space
     */
    btnCapacityLeft.on('click', function (e) {
        e.preventDefault();
        const capacity = $(this).data('capacity');
        const capacityTeus = $(this).data('capacity-teus');
        const status = $(this).data('status');
        const left = $(this).data('left');
        const fclData = JSON.parse(decodeURIComponent($(this).data('fcl')));
        const lclData = JSON.parse(decodeURIComponent($(this).data('lcl')));
        const totalRows = fclData.length > lclData.length ? fclData.length : lclData.length;

        const modal = $('#modal-data-over-space');
        modal.find('#capacity').text(capacity);
        modal.find('#capacity-teus').text(setNumeric(capacityTeus));
        modal.find('#status').text(status);
        modal.find('#capacity-left').text(setNumeric(left.toFixed(2)));

        let totalTeusFcl = 0;
        let totalTeusLcl = 0;
        let tableDetail = modal.find('#table-detail-over-space');
        tableDetail.find('tbody').detach();
        tableDetail.append($('<tbody>'));

        let rowItem = '';
        if (totalRows === 0) {
            rowItem = `<tr><td class="text-center" colspan="16">No data available</td></tr>`;
        } else {
            for (let i = 0; i < totalRows; i++) {
                rowItem += `<tr>`;
                if (fclData[i]) {
                    rowItem += `
                        <td data-title="${fclData[i]['no_reference']}">
                            ${truncate(fclData[i]['no_reference'], 12)}<br>
                            <small class="text-muted">${fclData[i]['customer_name']}</small>
                        </td>
                        <td>${fclData[i]['no_container']}</td>
                        <td>${fclData[i]['size']}</td>
                        <td class="text-center">${setNumeric(fclData[i]['inbound'])}</td>
                        <td class="text-center">${setNumeric(fclData[i]['outbound'])}</td>
                        <td class="text-center">${setNumeric(fclData[i]['stock'])}</td>
                        <td class="success text-center">${setNumeric(Number(fclData[i]['teus_used']).toFixed(2))}</td>
                    `;
                    totalTeusFcl += Number(fclData[i]['teus_used']);
                } else {
                    rowItem += `<td class="text-center" colspan="7"></td>`;
                }

                if (lclData[i]) {
                    rowItem += `
                        <td>${lclData[i]['id_goods']}</td>
                        <td data-title="${lclData[i]['no_reference']}">
                            ${truncate(lclData[i]['no_reference'], 12)}<br>
                            <small class="text-muted">${lclData[i]['customer_name']}</small>
                        </td>
                        <td>${lclData[i]['goods_name']}</td>
                        <td>${setNumeric(lclData[i]['unit_length'])}</td>
                        <td>${setNumeric(lclData[i]['unit_width'])}</td>
                        <td class="text-center">${setNumeric(lclData[i]['inbound'])}</td>
                        <td class="text-center">${setNumeric(lclData[i]['outbound'])}</td>
                        <td class="text-center">${setNumeric(lclData[i]['stock'])}</td>
                        <td class="success text-center">${setNumeric(Number(lclData[i]['teus_used']).toFixed(2))}</td>
                    `;
                    totalTeusLcl += Number(lclData[i]['teus_used']);
                } else {
                    rowItem += `<td class="text-center" colspan="9"></td>`
                }
                rowItem += `</tr>`;
            }
        }
        rowItem += `
            </tr>
                <td class="info text-center" colspan="6">TOTAL TEUS FCL</td>
                <td class="danger text-center">${setNumeric(totalTeusFcl.toFixed(2))}</td>
                <td class="info text-center" colspan="8">TOTAL TEUS LCL</td>
                <td class="danger text-center">${setNumeric(totalTeusLcl.toFixed(2))}</td>
            </tr>
        `;
        tableDetail.find('tbody').first().append(rowItem);

        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
