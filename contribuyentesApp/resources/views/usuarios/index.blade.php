{{-- Gestión de Usuarios --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

        <!-- Botón de crear -->
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">Usuarios registrados</h3>
            <button id="btnNuevoUsuario"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl shadow transition-all duration-200">
                + Nuevo Usuario
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 overflow-x-auto">
            <table id="tablaUsuarios" class="min-w-[950px] text-sm text-gray-800 dark:text-gray-100 w-full">
                <thead class="border-b border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">ID</th>
                        <th class="px-6 py-3 text-left font-semibold">Nombre</th>
                        <th class="px-6 py-3 text-left font-semibold">Correo</th>
                        <th class="px-6 py-3 text-left font-semibold">Rol</th>
                        <th class="px-6 py-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalUsuario" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate__animated animate__fadeInUp">

            <div class="flex justify-between items-center mb-5">
                <h3 id="tituloModal" class="text-2xl font-bold text-gray-800 dark:text-gray-100"></h3>
                <button id="btnCerrarModalHeader" class="text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            </div>

            <form id="formUsuario" class="space-y-4">
                @csrf
                <input type="hidden" id="id_usuario" name="id">

                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nombre completo</label>
                    <input type="text" id="name" name="name" placeholder="Ej: Juan Pérez"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                    <span class="text-red-500 text-xs" id="error_name"></span>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="usuario@correo.com"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                    <span class="text-red-500 text-xs" id="error_email"></span>
                </div>

                <!-- Rol -->
                <div>
                    <label for="role_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Rol del usuario</label>
                    <select id="role_id" name="role_id"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                        <option value="">Selecciona un rol</option>
                        <option value="1">Superusuario</option>
                        <option value="2">Administrador</option>
                    </select>
                    <span class="text-red-500 text-xs" id="error_role_id"></span>
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                    <span class="text-red-500 text-xs" id="error_password"></span>
                </div>

                <!-- Confirmar contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="btnCancelarModal"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-all">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        $(document).ready(function () {
            const tabla = $('#tablaUsuarios').DataTable({
                ajax: '{{ route("usuarios.data") }}',
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
                            <button class="btnEditar bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md mx-1">Editar</button>
                            <button class="btnEliminar bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md mx-1">Eliminar</button>
                        `
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
                const url = id ? `/usuarios/${id}` : `{{ route("usuarios.store") }}`;
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url, type: method, data: $(this).serialize(),
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
    </script>
</x-app-layout>
