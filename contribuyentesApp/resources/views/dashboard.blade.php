<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contribuyentes') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Estilos DataTables para modo oscuro y Tailwind */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #E5E7EB; /* Texto claro */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #E5E7EB !important;
            border-radius: 0.5rem;
            margin: 0 0.25rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: #374151 !important; /* Gris oscuro para selección */
            border-color: #374151 !important;
            color: #F9FAFB !important;
        }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper select {
            color: #1F2937; /* Texto oscuro para inputs */
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #D1D5DB;
        }

        /* --- CORRECCIÓN ESPECÍFICA PARA OCULTAR LA FLECHA DEL SELECT --- */
        .dataTables_wrapper select {
            /* Ocultar flecha en Chrome/Safari/Edge */
            -webkit-appearance: none;
            /* Ocultar flecha en Firefox */
            -moz-appearance: none;
            /* Ocultar flecha en IE */
            appearance: none;
            
            /* Ajustar color y fondo del select en modo oscuro para DataTables */
            background-color: #4B5563; /* Fondo oscuro */
            color: #F9FAFB; /* Texto claro */
            border-color: #4B5563;
        }
        /* Color del texto del input de búsqueda en modo oscuro */
        .dataTables_wrapper .dataTables_filter input {
             background-color: #4B5563;
             border-color: #4B5563;
             color: #F9FAFB;
        }
        /* Para que funcione el cursor en el input de búsqueda */
        .dataTables_wrapper .dataTables_filter input:focus {
             outline: none;
             box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5); /* Anillo azul de Tailwind */
        }
        /* Para que el select mantenga el fondo oscuro */
        .dataTables_wrapper .dataTables_length select {
            background-image: none !important; /* Asegura que no haya otra imagen de flecha */
        }
        /* --- FIN CORRECCIÓN SELECT --- */

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.6);
        }
        .animate-modal {
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Botón Crear (Visible solo para rol 2) --}}
            @auth
                @if(Auth::user()->role_id == 2)
                    <div class="flex justify-end">
                        <button id="btnCrear"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow-lg transition transform hover:scale-[1.02]">
                            Crear Contribuyente
                        </button>
                    </div>
                @endif
            @endauth

            {{-- Tabla de Contribuyentes --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden p-6">
                <table id="tablaContribuyentes" class="w-full text-sm divide-y divide-gray-300 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Tipo Documento</th>
                            <th class="px-4 py-3 text-left font-semibold">Documento</th>
                            <th class="px-4 py-3 text-left font-semibold">Nombres</th>
                            <th class="px-4 py-3 text-left font-semibold">Apellidos</th>
                            <th class="px-4 py-3 text-left font-semibold">Teléfono</th>
                            <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Contribuyente (Crear/Ver/Editar) --}}
    <div id="modalContribuyente" class="hidden fixed inset-0 flex justify-center items-center z-50 p-4 transition-opacity duration-300 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 w-full max-w-4xl p-6 sm:p-10 rounded-3xl shadow-2xl overflow-y-auto max-h-[95vh] animate-modal border border-gray-100 dark:border-gray-700/50">
                
                <h3 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white border-b pb-4 border-gray-200 dark:border-gray-700/70">
                    <span id="modalTitle">Detalle Contribuyente</span>
                </h3>

                <form id="formContribuyente" class="space-y-8">
                    @csrf
                    <input type="hidden" id="contribuyenteId" name="contribuyenteId">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Tipo Documento</label>
                            <select id="tipo_documento" name="tipo_documento"
                                class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm appearance-none">
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula</option>
                                <option value="NIT">NIT</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_tipo_documento"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Documento</label>
                            <input id="documento" name="documento" type="text"
                                class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="ID o RUC">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_documento"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Nombres</label>
                            <input id="nombres" name="nombres" type="text"
                                class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Nombre(s) completo(s)">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_nombres"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Apellidos</label>
                            <input id="apellidos" name="apellidos" type="text"
                                class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Apellido(s) completo(s)">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_apellidos"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Celular</label>
                            <input id="celular" name="celular" type="text" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Ej: 300 123 4567">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_celular"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Email</label>
                            <input id="email" name="email" type="email" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="contacto@ejemplo.com">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_email"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Usuario</label>
                            <input id="usuario" name="usuario" type="text" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Nombre de usuario">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_usuario"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Dirección</label>
                            <input id="direccion" name="direccion" type="text" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Calle/Carrera/Piso/Oficina">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_direccion"></span>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Teléfono (Fijo)</label>
                            <input id="telefono" name="telefono" type="text"
                                class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Ej: 601 555-1234">
                            <span class="text-red-500 text-xs mt-1 error-text block" id="error_telefono"></span>
                        </div>
                        
                        {{-- Se eliminó el campo 'email_2' duplicado del formulario HTML para usar solo 'email' --}}

                    </div>

                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                        
                        {{-- Auditoría --}}
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold">Fecha de Creación</label>
                            <input type="text" id="created_at" name="created_at" class="mt-1 block w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-gray-100 dark:bg-gray-700/80 dark:text-gray-400 cursor-not-allowed shadow-inner text-sm" disabled>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold">Fecha de Actualización</label>
                            <input type="text" id="updated_at" name="updated_at" class="mt-1 block w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-gray-100 dark:bg-gray-700/80 dark:text-gray-400 cursor-not-allowed shadow-inner text-sm" disabled>
                        </div>
                        
                        {{-- Frecuencia de Letras (Propiedad personalizada del backend) --}}
                        <div class="md:col-span-2">
                            <h4 class="font-bold text-sm text-gray-700 dark:text-gray-300 mb-2">Frecuencia de Letras en Nombres/Apellidos (Análisis)</h4>
                            <pre id="frecuenciaLetras" class="text-sm bg-gray-50 dark:bg-gray-900 dark:text-green-400 text-gray-800 p-4 rounded-xl overflow-auto max-h-48 shadow-inner border border-gray-200 dark:border-gray-700/80 transition duration-150"></pre>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" id="btnCerrar" class="flex items-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2.5 rounded-xl transition transform hover:scale-[1.02] shadow-md font-semibold">
                            Cerrar
                        </button>
                        @if(Auth::check() && Auth::user()->role_id == 2)
                            <button type="submit" id="btnGuardar" class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl transition transform hover:scale-[1.02] shadow-lg shadow-indigo-500/30 font-semibold">
                                Guardar
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

    {{-- Modal Confirmación de Eliminación --}}
    <div id="modalConfirmacion" class="hidden fixed inset-0 modal-overlay flex justify-center items-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl w-full max-w-sm animate-modal">
            <h4 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Confirmar Eliminación</h4>
            <p class="mb-8 text-gray-700 dark:text-gray-300">¿Estás seguro de eliminar este contribuyente? Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-3">
                <button type="button" id="btnCancelarEliminar" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-xl transition">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition">Eliminar</button>
            </div>
        </div>
    </div>

    {{-- Scripts y Dependencias --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css"/>

    <script>
    let tabla;
    const userRoleId = {!! json_encode(Auth::check() ? Auth::user()->role_id : 0) !!};
    const canEdit = (userRoleId === 2);
    let contribuyenteIdToDelete = null;

    $(document).ready(function() {
        // 1. Inicialización de DataTables
        tabla = $('#tablaContribuyentes').DataTable({
            ajax: '{{ route("contribuyentes.data") }}',
            columns: [
                { data: 'tipo_documento' },
                { data: 'documento' },
                { data: 'nombres' },
                { data: 'apellidos' },
                { data: 'telefono' },
                {
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        let btns = `<button class='btnView text-blue-500 hover:text-blue-700 mx-1 transition-colors' data-id='${data}' title="Ver Detalle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </button>`;
                        if (canEdit) {
                            btns += `<button class='btnEdit text-yellow-500 hover:text-yellow-600 mx-1 transition-colors' data-id='${data}' title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-3.5 3.5a1 1 0 000 1.414L10.586 10l-4 4-4-4 4-4 4-4zM6 16.5V18a2 2 0 002 2h9a2 2 0 002-2v-9a2 2 0 00-2-2h-1.5v-1.5a.5.5 0 00-1 0V6H8a2 2 0 00-2 2v2.586l-2-2L0 13l4 4 4-4-2.586-2.586z"/></svg>
                                    </button>`;
                            btns += `<button class='btnDelete text-red-500 hover:text-red-600 mx-1 transition-colors' data-id='${data}' title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 10-2 0v6a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                                    </button>`;
                        }
                        return btns;
                    }
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar contribuyente...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: { previous: "Anterior", next: "Siguiente" },
                zeroRecords: "No se encontraron registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)"
            },
            dom: '<"flex justify-between items-center mb-4"lf>t<"mt-4"ip>',
            responsive: true,
        });

        // 2. Funciones Auxiliares
        function limpiarErrores() { $('.error-text').text(''); }
        function formatReadableDate(isoString) {
            return isoString ? new Date(isoString).toLocaleString('es-CO', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit'
            }) : 'N/A';
        }

        // 3. Manejo de Modales
        $('#btnCrear').click(function() {
            if (!canEdit) return;
            $('#formContribuyente')[0].reset();
            $('#contribuyenteId').val('');
            $('#frecuenciaLetras').text('La frecuencia de letras se mostrará después de guardar el contribuyente.');
            $('#created_at, #updated_at').val('N/A');
            $('#btnGuardar').show().text('Crear');
            $('#formContribuyente input, #formContribuyente select').prop('disabled', false); // Asegura que estén habilitados
            $('#modalTitle').text('Crear Contribuyente'); // CORRECCIÓN: Título
            limpiarErrores();
            $('#modalContribuyente').removeClass('hidden');
        });

        $('#btnCerrar').click(() => $('#modalContribuyente').addClass('hidden'));

        // 4. Manejo de Guardado/Actualización (Formulario)
        $('#formContribuyente').submit(function(e) {
            e.preventDefault();
            if (!canEdit) return;

            const id = $('#contribuyenteId').val();
            const url = id ? `/contribuyentes/${id}` : '{{ route("contribuyentes.store") }}';
            const method = id ? 'PUT' : 'POST';

            // Limpiar errores antes de la nueva petición
            limpiarErrores();
            $('#btnGuardar').prop('disabled', true).text('Guardando...');

            $.ajax({
                url,
                type: 'POST', // Siempre POST para Laravel con method spoofing
                data: $(this).serialize() + `&_method=${method}`, // Method spoofing para PUT
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: () => {
                    tabla.ajax.reload(null, false); // Recarga la tabla sin resetear la paginación
                    $('#modalContribuyente').addClass('hidden');
                    // Opcional: SweetAlert de éxito aquí
                },
                error: function(err) {
                    if (err.responseJSON && err.responseJSON.errors) {
                        for (let campo in err.responseJSON.errors) {
                            $(`#error_${campo}`).text(err.responseJSON.errors[campo][0]);
                        }
                    } else {
                        console.error('Error al guardar:', err);
                        // Opcional: SweetAlert de error general aquí
                    }
                },
                complete: function() {
                    $('#btnGuardar').prop('disabled', false).text(id ? 'Guardar' : 'Crear');
                }
            });
        });

        // 5. Manejo de Ver y Editar
        $('#tablaContribuyentes').on('click', '.btnView, .btnEdit', function() {
            const id = $(this).data('id');
            const isEdit = $(this).hasClass('btnEdit');

            $('#btnGuardar').toggle(isEdit).text(isEdit ? 'Guardar' : 'N/A');
            $('#formContribuyente input, #formContribuyente select').prop('disabled', !isEdit);
            $('#created_at, #updated_at').prop('disabled', true); // Siempre deshabilitados

            $('#modalTitle').text(isEdit ? 'Editar Contribuyente' : 'Ver Contribuyente'); // CORRECCIÓN: Título
            limpiarErrores();

            $.get(`/contribuyentes/${id}`, function(res) {
                // Rellenar formulario (Se agregaron los campos faltantes)
                $('#contribuyenteId').val(res.contribuyente.id);
                $('#tipo_documento').val(res.contribuyente.tipo_documento);
                $('#documento').val(res.contribuyente.documento);
                $('#nombres').val(res.contribuyente.nombres);
                $('#apellidos').val(res.contribuyente.apellidos);
                $('#celular').val(res.contribuyente.celular); // AGREGADO
                $('#email').val(res.contribuyente.email);
                $('#usuario').val(res.contribuyente.usuario); // AGREGADO
                $('#direccion').val(res.contribuyente.direccion); // AGREGADO
                $('#telefono').val(res.contribuyente.telefono);
                
                // Campos de auditoría
                $('#created_at').val(formatReadableDate(res.contribuyente.created_at));
                $('#updated_at').val(formatReadableDate(res.contribuyente.updated_at));

                // Mostrar frecuencia de letras
                $('#frecuenciaLetras').text(JSON.stringify(res.frecuencia, null, 2));

                $('#modalContribuyente').removeClass('hidden');
            }).fail(() => {
                console.error('Error al obtener contribuyente');
            });
        });

        // 6. Manejo de Eliminación
        $('#tablaContribuyentes').on('click', '.btnDelete', function() {
            if (!canEdit) return;
            contribuyenteIdToDelete = $(this).data('id');
            $('#modalConfirmacion').removeClass('hidden');
        });

        $('#btnCancelarEliminar').click(() => $('#modalConfirmacion').addClass('hidden'));

        $('#btnConfirmarEliminar').click(function() {
            if (!contribuyenteIdToDelete) return;

            // Deshabilitar botón para evitar doble click
            $(this).prop('disabled', true).text('Eliminando...');

            $.ajax({
                url: `/contribuyentes/${contribuyenteIdToDelete}`,
                type: 'POST', // Siempre POST
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { _method: 'DELETE' }, // Method spoofing
                success: () => {
                    tabla.ajax.reload(null, false);
                    $('#modalConfirmacion').addClass('hidden');
                    contribuyenteIdToDelete = null;
                },
                error: err => console.error('Error al eliminar:', err),
                complete: function() {
                    $('#btnConfirmarEliminar').prop('disabled', false).text('Eliminar');
                }
            });
        });
    });
    </script>
</x-app-layout>