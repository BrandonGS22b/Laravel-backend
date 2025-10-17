{{-- Gestión de Usuarios --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    
    <style>
        /* FIX: Soluciona la superposición de la flecha del select de DataTables con el número */
        .dataTables_length select {
            padding-right: 2rem !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            position: relative;
        }

        /* CLAVE: Si usas la animación CSS, asegúrate de que el modal se muestre como 'flex' cuando está activo */
        #modalUsuario:not(.hidden), #modalConfirmarEliminar:not(.hidden) {
            display: flex;
        }

        .modal-enter-active, .modal-leave-active {
          transition: opacity 0.3s ease;
        }
        .modal-enter-from, .modal-leave-to {
          opacity: 0;
        }
    </style>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Header y botón --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">Usuarios registrados</h3>
            <button id="btnNuevoUsuario" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition-all duration-200">
                + Nuevo Usuario
            </button>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 overflow-x-auto">
            <table id="tablaUsuarios" class="min-w-[950px] text-sm text-gray-800 dark:text-gray-100 w-full">
                <thead class="border-b border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
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

    {{-- Modal de Creación/Edición --}}
    <div id="modalUsuario" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-8 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 id="tituloModal" class="text-2xl font-bold text-gray-800 dark:text-gray-100"></h3>
                <button type="button" id="btnCerrarModalHeader" class="text-gray-500 hover:text-red-600 text-3xl font-bold">&times;</button>
            </div>

            <form id="formUsuario" class="space-y-5">
                @csrf
                <input type="hidden" id="id_usuario" name="id">

                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre completo</label>
                    <input type="text" id="name" name="name" placeholder="Ej: Juan Pérez"
                        class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                    <span class="text-red-500 text-xs" id="error_name"></span>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="usuario@correo.com"
                        class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                    <span class="text-red-500 text-xs" id="error_email"></span>
                </div>

                {{-- Rol --}}
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol del usuario</label>
                    <select id="role_id" name="role_id"
                        class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                        <option value="">Selecciona un rol</option>
                        <option value="1">Superusuario</option>
                        <option value="2">Administrador</option>
                    </select>
                    <span class="text-red-500 text-xs" id="error_role_id"></span>
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres (Solo si deseas cambiarla)"
                        class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                    <span class="text-red-500 text-xs" id="error_password"></span>
                </div>

                {{-- Confirmar contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña"
                        class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="btnCancelarModal"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg transition-all">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition-all">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Custom Modal de Confirmación de Eliminación --}}
    <div id="modalConfirmarEliminar" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-sm p-6 relative text-center">
            <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <h3 class="mt-4 text-xl font-bold text-gray-800 dark:text-gray-100">Confirmar Eliminación</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">¿Estás seguro de que deseas eliminar este usuario? Esta acción es irreversible.</p>
            <div class="mt-5 flex justify-center gap-4">
                <button type="button" id="btnCancelarEliminar"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold px-4 py-2 rounded-lg transition-all duration-200">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" data-id=""
                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg shadow-md transition-all duration-200">Eliminar</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        // Función para mostrar modal. Usa solo 'hidden' para mayor compatibilidad con Tailwind/JS.
        function showModal(selector) {
            // Aseguramos que la clase 'hidden' se remueva y se aplique 'flex' para que se muestre.
            $(selector).removeClass('hidden');
        }

        // Función para ocultar modal.
        function hideModal(selector) {
            // Aplicamos la clase 'hidden' para ocultar.
            $(selector).addClass('hidden');
        }

        $(document).ready(function () {
            let userIdToDelete = null;

            const tabla = $('#tablaUsuarios').DataTable({
                ajax: '{{ route("usuarios.data") }}',
                scrollX: true,
                autoWidth: false,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
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

            // --- Modal de Creación/Edición ---

            $('#btnNuevoUsuario').click(() => {
                $('#formUsuario')[0].reset();
                $('#id_usuario').val('');
                $('#tituloModal').text('Nuevo Usuario');
                $('span[id^="error_"]').text(''); // Limpiar errores
                
                // **CLAVE PARA CREAR**: Asegurarse de que no exista el campo _method=PUT
                $('#formUsuario').find('input[name="_method"]').remove(); 
                
                showModal('#modalUsuario'); // **FUNCIÓN CORREGIDA**
            });

            $('#btnCerrarModalHeader, #btnCancelarModal').click(() => hideModal('#modalUsuario'));
            
            // Cerrar al hacer click fuera del modal principal
            $('#modalUsuario').on('click', function(e) {
                if ($(e.target).is('#modalUsuario')) {
                    hideModal('#modalUsuario');
                }
            });

            $('#formUsuario').submit(function (e) {
                e.preventDefault();
                const id = $('#id_usuario').val();
                
                // En Laravel, las rutas resource para PUT/PATCH/DELETE DEBEN usar POST con un campo _method
                const url = id ? `/usuarios/${id}` : `{{ route("usuarios.store") }}`;
                const method = 'POST'; // Siempre POST para Laravel AJAX con _method

                $('span[id^="error_"]').text(''); // Limpiar errores

                // **CLAVE PARA EDITAR**: Añadir el campo oculto para simular PUT/PATCH
                if (id && !$(this).find('input[name="_method"]').length) {
                    $(this).append('<input type="hidden" name="_method" value="PUT">'); 
                } 
                
                $.ajax({
                    url, type: method, data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: (response) => {
                        hideModal('#modalUsuario');
                        tabla.ajax.reload();
                        console.log(response.message);
                    },
                    error: xhr => {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) for (let field in errors) $(`#error_${field}`).text(errors[field][0]);
                        else console.error('Error en la operación:', xhr.responseText);
                    }
                });
            });

            // Click en el botón Editar
            $(document).on('click', '.btnEditar', function () {
                const id = $(this).data('id');
                // Endpoint edit para obtener datos (mismo que usas en el controller)
                $.get(`/usuarios/${id}/edit`, function (data) {
                    $('#tituloModal').text('Editar Usuario');
                    $('#id_usuario').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#role_id').val(data.role_id);
                    $('#password, #password_confirmation').val('');
                    
                    // **CLAVE PARA EDITAR**: Asegurar que el campo _method=PUT exista
                    if (!$('#formUsuario').find('input[name="_method"]').length) {
                        $('#formUsuario').append('<input type="hidden" name="_method" value="PUT">');
                    }

                    $('span[id^="error_"]').text('');
                    showModal('#modalUsuario'); // **FUNCIÓN CORREGIDA**
                }).fail(xhr => {
                    console.error('Error al obtener datos del usuario:', xhr.responseText);
                });
            });

            // --- Modal de Confirmación de Eliminación ---

            $(document).on('click', '.btnEliminar', function () {
                userIdToDelete = $(this).data('id');
                showModal('#modalConfirmarEliminar'); // **FUNCIÓN CORREGIDA**
            });
            
            $('#btnCancelarEliminar').click(() => hideModal('#modalConfirmarEliminar'));
            
            // Cerrar al hacer click fuera del modal de confirmación
            $('#modalConfirmarEliminar').on('click', function(e) {
                if ($(e.target).is('#modalConfirmarEliminar')) {
                    hideModal('#modalConfirmarEliminar');
                }
            });

            $('#btnConfirmarEliminar').click(function () {
                if (userIdToDelete) {
                    $.ajax({
                        url: `/usuarios/${userIdToDelete}`,
                        // **CLAVE PARA ELIMINAR**: Cambiar 'DELETE' a 'POST' y añadir _method
                        type: 'POST', 
                        data: {
                            _method: 'DELETE', // Simulación del método DELETE
                            _token: $('meta[name="csrf-token"]').attr('content') 
                        },
                        success: () => {
                            hideModal('#modalConfirmarEliminar');
                            tabla.ajax.reload();
                            userIdToDelete = null;
                            console.log('Usuario eliminado correctamente.');
                        },
                        error: xhr => {
                            console.error('Error al intentar eliminar el usuario:', xhr.responseText);
                            hideModal('#modalConfirmarEliminar');
                            userIdToDelete = null;
                            alert('No se pudo eliminar el usuario.');
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>