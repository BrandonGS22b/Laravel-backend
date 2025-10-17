import $ from 'jquery';
import 'datatables.net';

$(document).ready(function () {
    const userRoleId = window.Laravel?.role_id || 0;
    const canEdit = (userRoleId === 2);
    let contribuyenteIdToDelete = null;

    const tabla = $('#tablaContribuyentes').DataTable({
        ajax: '/contribuyentes/data',
        dataSrc: 'data',
        columns: [
            { data: 'tipo_documento' },
            { data: 'documento' },
            { data: 'nombres' },
            { data: 'apellidos' },
            { data: 'telefono' },
            {
                data: 'id',
                render: function (data) {
                    let buttons = `<button class="btnView" data-id="${data}">👁 Ver</button>`;
                    if (canEdit) {
                        buttons += ` <button class="btnEdit" data-id="${data}">✏️</button>`;
                        buttons += ` <button class="btnDelete" data-id="${data}">🗑</button>`;
                    }
                    return buttons;
                },
            },
        ],
    });

    $('#btnCrear').on('click', function () {
        $('#formContribuyente')[0].reset();
        $('#modalContribuyente').removeClass('hidden');
    });

    $('#btnCerrar').on('click', function () {
        $('#modalContribuyente').addClass('hidden');
    });

    $('#tablaContribuyentes').on('click', '.btnDelete', function () {
        contribuyenteIdToDelete = $(this).data('id');
        $('#modalConfirmacion').removeClass('hidden');
    });

    $('#btnCancelarEliminar').on('click', function () {
        $('#modalConfirmacion').addClass('hidden');
    });

    $('#btnConfirmarEliminar').on('click', function () {
        if (!contribuyenteIdToDelete) return;
        $.ajax({
            url: `/contribuyentes/${contribuyenteIdToDelete}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: () => {
                tabla.ajax.reload();
                $('#modalConfirmacion').addClass('hidden');
            },
        });
    });
});
