{{-- NOTA: Se asume que tu modelo de usuario tiene un campo 'role_id' --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contribuyentes') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #E5E7EB;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #E5E7EB !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: #374151 !important;
            border-color: #374151 !important;
            color: #F9FAFB !important;
        }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper select {
            color: #1F2937;
        }
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.7);
        }
    </style>

    <div class="py-12 bg-black">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(Auth::check() && Auth::user()->role_id == 2)
                <button id="btnCrear" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-md transition">
                    Crear Contribuyente
                </button>
            @endif

            <div class="bg-white dark:bg-gray-900 shadow-xl rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="tablaContribuyentes">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Tipo Documento</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Documento</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Nombres</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Apellidos</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Teléfono</th>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Creación/Edición/Visualización --}}
    <div id="modalContribuyente" class="hidden fixed inset-0 modal-overlay flex justify-center items-center z-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-900 w-full max-w-3xl p-6 rounded-lg shadow-xl overflow-y-scroll max-h-[90vh] md:w-full">
            <h3 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Detalle Contribuyente</h3>

            <form id="formContribuyente" class="space-y-4">
                @csrf
                <input type="hidden" id="contribuyenteId" name="contribuyenteId">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Tipo Documento</label>
                        <input type="text" id="tipo_documento" name="tipo_documento" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_tipo_documento"></span>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Documento</label>
                        <input type="text" id="documento" name="documento" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_documento"></span>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Nombres</label>
                        <input type="text" id="nombres" name="nombres" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_nombres"></span>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_apellidos"></span>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Nombre Completo</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_nombre_completo"></span>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_telefono"></span>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Celular</label>
                        <input type="text" id="celular" name="celular" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_celular"></span>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_email"></span>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Usuario</label>
                        <input type="text" id="usuario" name="usuario" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                        <span class="text-red-500 text-sm error-text" id="error_usuario"></span>
                    </div>

                    {{-- NUEVOS CAMPOS: FECHAS DE AUDITORÍA --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Fecha de Creación</label>
                        <input type="text" id="created_at" name="created_at" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-gray-700 dark:text-gray-300 font-medium">Fecha de Actualización</label>
                        <input type="text" id="updated_at" name="updated_at" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                    </div>
                    {{-- FIN NUEVOS CAMPOS --}}
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" id="btnCerrar" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-md transition">Cerrar</button>
                    @if(Auth::check() && Auth::user()->role_id == 2)
                        <button type="submit" id="btnGuardar" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-md transition">Guardar</button>
                    @endif
                </div>
            </form>

            <hr class="my-4 border-gray-300 dark:border-gray-700">
            <div>
                <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Frecuencia de Letras</h4>
                <pre id="frecuenciaLetras" class="text-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-100 p-2 rounded-md overflow-auto max-h-48"></pre>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Eliminación Personalizado --}}
    <div id="modalConfirmacion" class="hidden fixed inset-0 modal-overlay flex justify-center items-center z-50 p-4">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-xl w-full max-w-sm">
            <h4 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Confirmar Eliminación</h4>
            <p class="mb-6 text-gray-700 dark:text-gray-300">¿Estás seguro de que deseas eliminar este contribuyente? Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-3">
                <button type="button" id="btnCancelarEliminar" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">Eliminar</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css"/>
    
    <script>
    $(document).ready(function() {
        
        // Variable para almacenar el ID del contribuyente a eliminar
        let contribuyenteIdToDelete = null;
        
        // Obtener el ID del rol del usuario autenticado de Blade
        const userRoleId = {!! json_encode(Auth::check() ? Auth::user()->role_id : 0) !!};
        
        // La capacidad de EDITAR, CREAR y ELIMINAR solo pertenece al Administrador (ID 1)
        const canEditCreateOrDelete = (userRoleId === 2);

        // Inicialización de DataTables
        const tabla = $('#tablaContribuyentes').DataTable({
            ajax: { url: '{{ route("contribuyentes.index") }}', dataSrc: 'data' },
            columns: [
                { data: 'tipo_documento' },
                { data: 'documento' },
                { data: 'nombres' },
                { data: 'apellidos' },
                { data: 'telefono' },
                { 
                    data: 'id', 
                    orderable: false,
                    searchable: false,
                    render: function(data){
                        // Botón Ver (Siempre visible para cualquier rol)
                        let buttons = `
                            <button class="btnView bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm mx-1" data-id="${data}">Ver</button>
                        `;

                        // Botones Editar y Eliminar (Solo visible si es Administrador)
                        if (canEditCreateOrDelete) {
                            buttons += `
                                <button class="btnEdit bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm mx-1" data-id="${data}">Editar</button>
                                <button class="btnDelete bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm mx-1" data-id="${data}">Eliminar</button>
                            `;
                        }
                        
                        return buttons;
                    }
                }
            ],
            // Ajustes para DataTables para compatibilidad en modo oscuro
            initComplete: function() {
                // Aplica clases de Tailwind para modo oscuro al contenedor de DataTables
                $('#tablaContribuyentes_wrapper').addClass('dark:bg-gray-900 dark:text-gray-200 p-4 rounded-lg');
                $('#tablaContribuyentes').find('tbody').addClass('text-gray-800 dark:text-gray-200');
            }
        });

        function limpiarErrores() { $('.error-text').text(''); }

        // --- Eventos para Modales de Creación/Edición/Visualización ---

        // Función auxiliar para formatear la fecha ISO a un formato local legible
        function formatReadableDate(isoString) {
            if (!isoString) return 'N/A';
            try {
                // Intenta parsear la fecha. Se usa 'es-CO' como ejemplo de español.
                return new Date(isoString).toLocaleString('es-CO', {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });
            } catch (e) {
                return isoString; // En caso de error, devuelve el string original
            }
        }


        // Mostrar Modal de Creación (Solo Administrador)
        $('#btnCrear').click(function(){
            if (!canEditCreateOrDelete) return; // Chequeo de seguridad JS
            $('#formContribuyente')[0].reset();
            $('#contribuyenteId').val('');
            $('#frecuenciaLetras').text('');
            
            // Los campos de fecha deben limpiarse y deshabilitarse en la creación
            $('#created_at').val('');
            $('#updated_at').val('');
            
            $('#formContribuyente input').prop('disabled', false);
            // Aseguramos que los campos de auditoría (fechas) sigan deshabilitados
            $('#created_at, #updated_at').prop('disabled', true); 

            $('#btnGuardar').show(); 
            limpiarErrores();
            $('#modalContribuyente').removeClass('hidden'); // con esto oculto los modales
        });

        // Cerrar Modal de Contribuyente
        $('#btnCerrar').click(function(){ $('#modalContribuyente').addClass('hidden'); });

        // Enviar Formulario (Crear/Editar - Solo Administrador)
        $('#formContribuyente').submit(function(e){
            e.preventDefault();
            if (!canEditCreateOrDelete) return; // Chequeo de seguridad JS
            
            const id = $('#contribuyenteId').val();
            const url = id ? `/contribuyentes/${id}` : '{{ route("contribuyentes.store") }}';
            const method = id ? 'PUT' : 'POST';
            limpiarErrores();

            $.ajax({
                url: url,
                type: 'POST', 
                // Excluimos explícitamente los campos de fecha de la serialización si los tuviéramosmodalContribuyente
                // Pero como están deshabilitados en creación/edición no se envían.
                data: $(this).serialize() + (id ? '&_method=PUT' : ''), 
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                success: function(res){
                    console.log(`Éxito: ${res.message}`); 
                    tabla.ajax.reload();
                    $('#modalContribuyente').addClass('hidden');
                },
                error: function(err){
                    if(err.responseJSON && err.responseJSON.errors){
                        for(let campo in err.responseJSON.errors){
                            $(`#error_${campo}`).text(err.responseJSON.errors[campo][0]);
                        }
                    } else { console.error('Ocurrió un error al guardar.', err); }
                }
            });
        });

        // Ver / Editar Contribuyente
        $('#tablaContribuyentes').on('click', '.btnView, .btnEdit', function(){
            const id = $(this).data('id'); 
            const esVer = $(this).hasClass('btnView');
            
            // Si es 'Editar' y NO es Administrador, detener la acción.
            if (!esVer && !canEditCreateOrDelete) {
                console.warn('Acción de edición no permitida.');
                return;
            }

            // Configurar visibilidad y estado de campos
            if (esVer) {
                // Ver: Ocultar Guardar y deshabilitar todos los inputs
                $('#btnGuardar').hide();
                $('#formContribuyente input').prop('disabled', true);
            } else {
                // Editar (solo rol 1): Mostrar Guardar y habilitar inputs de formulario
                $('#btnGuardar').show();
                $('#formContribuyente input').prop('disabled', false);
            }
            
            // Los campos de auditoría (fechas) siempre deben estar deshabilitados.
            $('#created_at, #updated_at').prop('disabled', true); 

            $.get(`/contribuyentes/${id}`, function(res){
                // Rellenar el formulario con los datos
                $('#contribuyenteId').val(res.contribuyente.id);
                $('#tipo_documento').val(res.contribuyente.tipo_documento);
                $('#documento').val(res.contribuyente.documento);
                $('#nombres').val(res.contribuyente.nombres);
                $('#apellidos').val(res.contribuyente.apellidos);
                $('#nombre_completo').val(res.contribuyente.nombre_completo);
                $('#telefono').val(res.contribuyente.telefono);
                $('#celular').val(res.contribuyente.celular);
                $('#email').val(res.contribuyente.email);
                $('#usuario').val(res.contribuyente.usuario);
                
                // Rellenar los NUEVOS CAMPOS de fecha y darles formato
                $('#created_at').val(formatReadableDate(res.contribuyente.created_at));
                $('#updated_at').val(formatReadableDate(res.contribuyente.updated_at));
                
                // Mostrar la frecuencia de letras
                $('#frecuenciaLetras').text(JSON.stringify(res.frecuencia, null, 2));
                
                limpiarErrores();
                $('#modalContribuyente').removeClass('hidden');

            }).fail(function() {
                console.error('Error al cargar los datos del contribuyente.');
            });
        });

        // --- Eventos para Modal de Confirmación de Eliminación (Solo Administrador) ---

        // Abrir Modal de Confirmación
        $('#tablaContribuyentes').on('click', '.btnDelete', function(){
            if (!canEditCreateOrDelete) return; // Chequeo de seguridad JS
            contribuyenteIdToDelete = $(this).data('id'); 
            $('#modalConfirmacion').removeClass('hidden');
        });

        // Cancelar Eliminación
        $('#btnCancelarEliminar').click(function(){
            $('#modalConfirmacion').addClass('hidden');
            contribuyenteIdToDelete = null;
        });

        // Confirmar Eliminación y ejecutar AJAX
        $('#btnConfirmarEliminar').click(function(){
            if (!contribuyenteIdToDelete || !canEditCreateOrDelete) return;

            $.ajax({
                url: `/contribuyentes/${contribuyenteIdToDelete}`,
                type: 'POST', 
                data: {_token:'{{ csrf_token() }}', _method: 'DELETE'}, 
                success: function(res){
                    console.log(`Éxito: ${res.message}`);
                    tabla.ajax.reload();
                    $('#modalConfirmacion').addClass('hidden');
                    contribuyenteIdToDelete = null;
                },
                error: function(err){
                    console.error('Error al eliminar el contribuyente.', err);
                    $('#modalConfirmacion').addClass('hidden');
                }
            });
        });

    });
    </script>
</x-app-layout>
