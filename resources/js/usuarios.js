$(document).ready(function () {
    const tabla = $('#tablaUsuarios').DataTable({
        ajax: $('#tablaUsuarios').data('ajax') || '/usuarios/data', // ruta de tu backend
        scrollX: true,
        autoWidth: false,
        columns: [
            { data: 'id', width: '5%' },
            { data: 'name', width: '25%' },
            { data: 'email', width: '30%' },
            { data: 'role_name', width: '15%' },
            {
                data: null,
                className: 'text-center',
                width: '25%',
                render: data => `
                    <button class="btnEditar bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md mx-1" data-id="${data.id}">Editar</button>
                    <button class="btnEliminar bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md mx-1" data-id="${data.id}">Eliminar</button>`
            }
        ]
    });

    // Abrir modal nuevo
    $('#btnNuevoUsuario').click(() => {
        $('#formUsuario')[0].reset();
        $('#id_usuario').val('');
        $('#tituloModal').text('Nuevo Usuario');
        $('#modalUsuario').removeClass('hidden');
    });

    // Cerrar modal
    $('#btnCerrarModalHeader, #btnCancelarModal').click(() => $('#modalUsuario').addClass('hidden'));

    // Guardar usuario
    $('#formUsuario').submit(function (e) {
        e.preventDefault();
        const id = $('#id_usuario').val();
        const url = id ? `/usuarios/${id}` : '/usuarios';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url,
            type: method,
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: () => {
                $('#modalUsuario').addClass('hidden');
                tabla.ajax.reload();
            },
            error: xhr => {
                const errors = xhr.responseJSON?.errors;
                $('span[id^="error_"]').text('');
                if (errors) for (let field in errors) $(`#error_${field}`).text(errors[field][0]);
            }
        });
    });

    // Editar
    $(document).on('click', '.btnEditar', function () {
        const id = $(this).data('id');
        $.get(`/usuarios/${id}/edit`, function (data) {
            $('#tituloModal').text('Editar Usuario');
            $('#id_usuario').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#role_id').val(data.role_id);
            $('#password, #password_confirmation').val('');
            $('#modalUsuario').removeClass('hidden');
        });
    });

    // Eliminar
    $(document).on('click', '.btnEliminar', function () {
        const id = $(this).data('id');
        if (confirm('¿Seguro que deseas eliminar este usuario?')) {
            $.ajax({
                url: `/usuarios/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: () => tabla.ajax.reload()
            });
        }
    });
});
