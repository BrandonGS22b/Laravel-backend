

    function aplicarLogicaNombres() {
        const tipoDocumento = $('#tipo_documento').val();
        const nombresInput = $('#nombres');
        const apellidosInput = $('#apellidos');
        const nombreCompletoInput = $('#nombre_completo');

        // Resetear
        nombresInput.removeClass('is-readonly').prop('readonly', false);
        apellidosInput.removeClass('is-readonly').prop('readonly', false);
        nombreCompletoInput.removeClass('is-readonly').prop('readonly', false);

        if (tipoDocumento === 'NIT') {
            const razonSocial = nombreCompletoInput.val().trim();
            const palabras = razonSocial.split(/\s+/).filter(p => p.length > 0);
            
            let nombres = '';
            let apellidos = '';

            if (palabras.length >= 1) {
                nombres = palabras[0];
                apellidos = palabras.slice(1).join(' ');
            }
            
            nombresInput.val(nombres);
            apellidosInput.val(apellidos);
            
            // Aplicar readonly
            nombresInput.addClass('is-readonly').prop('readonly', true);  
            apellidosInput.addClass('is-readonly').prop('readonly', true); 

        } else if (tipoDocumento === 'CC' || tipoDocumento === 'Pasaporte') {
            
            const nombres = nombresInput.val().trim();
            const apellidos = apellidosInput.val().trim();
            
            nombreCompletoInput.val(`${nombres} ${apellidos}`.trim());
            
            // Aplicar readonly
            nombreCompletoInput.addClass('is-readonly').prop('readonly', true);
        }
    }

    function limpiarErrores() { 
        $('.error-text').text(''); 
    }
    
    function formatReadableDate(isoString) {
        return isoString ? new Date(isoString).toLocaleString('es-CO', {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', hour12: true
        }) : 'N/A';
    }

    function setModalState(isEdit = false, isView = false) {
        const isFormEditable = isEdit && canEdit;
        
        // Deshabilitar/Habilitar todos los campos del formulario (excepto los de auditoría)
        $('#formContribuyente input:not(#created_at, #updated_at), #formContribuyente select').prop('disabled', !isFormEditable);
        
        // Si el formulario es editable, re-aplicamos la lógica de nombres para manejar NIT/CC
        if (isFormEditable) {
            aplicarLogicaNombres(); 
        } else {
            // Si es solo para ver, aseguramos que todos los campos relevantes sean readonly.
            $('#nombres, #apellidos, #nombre_completo').addClass('is-readonly').prop('readonly', true);
        }

        // Mostrar/Ocultar botón Guardar
        $('#btnGuardar').toggle(isEdit && canEdit);
        
        let title = 'Detalle Contribuyente';
        if (isEdit) title = 'Editar Contribuyente';
        else if (!isView) title = 'Crear Contribuyente'; 
        $('#modalTitle').text(title);
    }

    $(document).ready(function() {

        tabla = $('#tablaContribuyentes').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("contribuyentes.data") }}',
                data: function(d) {
                    d.nombres = $('#tablaContribuyentes_filter input').val();
                }
            },
            columns: [
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'documento', name: 'documento' },
                { data: 'nombres', name: 'nombres' },
                { data: 'apellidos', name: 'apellidos' },
                { data: 'telefono', name: 'telefono' },
                {
                    data: 'id',
                    name: 'acciones',
                    className: 'text-center whitespace-nowrap',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let btns = `
                            <div class="flex justify-center space-x-2">
                                {{-- Botón Ver --}}
                                <button type="button" title="Ver Detalle" class='btnView text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition transform hover:scale-110 active:scale-95 p-1 rounded-full' data-id='${data}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                        `;

                        if (canEdit) {
                            btns += `
                                {{-- Botón Editar --}}
                                <button type="button" title="Editar" class='btnEdit text-indigo-600 dark:text-indigo-500 hover:text-indigo-800 dark:hover:text-indigo-300 transition transform hover:scale-110 active:scale-95 p-1 rounded-full' data-id='${data}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-9-4l6 6m2-2l4-4a2 2 0 000-2.828l-2.828-2.828a2 2 0 00-2.828 0l-4 4zm-1 5H8v-2.5l6-6 2.5 2.5-6 6z" /></svg>
                                </button>
                                {{-- Botón Eliminar --}}
                                <button type="button" title="Eliminar" class='btnDelete text-red-600 dark:text-red-500 hover:text-red-800 dark:hover:text-red-300 transition transform hover:scale-110 active:scale-95 p-1 rounded-full' data-id='${data}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            `;
                        }
                        
                        btns += `</div>`;
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
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-4"lf>t<"mt-4 flex flex-col sm:flex-row justify-between items-center gap-4"ip>',
            responsive: true,
            initComplete: function () {
                $('#tablaContribuyentes_filter input').addClass('dark:bg-gray-700 dark:text-gray-100');
                $('#tablaContribuyentes_length select').addClass('dark:bg-gray-700 dark:text-gray-100');
            }
        });

        // Eventos de Lógica (Se mantienen)
        $('#tipo_documento').on('change', function() {
            $('#nombre_completo').val('');
            $('#nombres').val('');
            $('#apellidos').val('');
            aplicarLogicaNombres(); 
        });
        $('#nombres, #apellidos, #nombre_completo').on('input', aplicarLogicaNombres);

        // Manejo de Modales (Crear)
        $('#btnCrear').click(function() {
            $('#formContribuyente')[0].reset();
            $('#contribuyenteId').val('');
            $('#frecuenciaLetras').text('La frecuencia de letras se mostrará al editar o ver el contribuyente.');
            $('#created_at, #updated_at').val('N/A');
            limpiarErrores();
            setModalState(true, false); 
            $('#btnGuardar').html('<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7.707 10.293a1 1 0 10-1.414 1.414L8.586 14 14.293 8.293a1 1 0 00-1.414-1.414L8.586 11.414z" /></svg><span>Crear</span>');
            $('#modalContribuyente').removeClass('hidden');
            aplicarLogicaNombres(); 
        });

        // Botones de cierre
        $('#btnCerrar, #btnCerrarTop').click(() => {
            $('#modalContribuyente').addClass('hidden');
        });

        // Manejo de Ver y Editar
        $('#tablaContribuyentes').on('click', '.btnView, .btnEdit', function() {
            const id = $(this).data('id');
            const isEdit = $(this).hasClass('btnEdit') && canEdit;
            const isView = $(this).hasClass('btnView');

            setModalState(isEdit, isView);
            limpiarErrores();
            $('#btnGuardar').html('<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7.707 10.293a1 1 0 10-1.414 1.414L8.586 14 14.293 8.293a1 1 0 00-1.414-1.414L8.586 11.414z" /></svg><span>Guardar</span>');

            $.get(`/contribuyentes/${id}`, function(res) {
                const contribuyente = res.contribuyente;
                $('#contribuyenteId').val(contribuyente.id);
                $('#tipo_documento').val(contribuyente.tipo_documento);
                $('#documento').val(contribuyente.documento);
                $('#nombres').val(contribuyente.nombres);
                $('#apellidos').val(contribuyente.apellidos);
                $('#nombre_completo').val(contribuyente.nombre_completo);
                $('#celular').val(contribuyente.celular); 
                $('#email').val(contribuyente.email);
                $('#usuario').val(contribuyente.usuario); 
                $('#direccion').val(contribuyente.direccion); 
                $('#telefono').val(contribuyente.telefono);
                
                $('#created_at').val(formatReadableDate(contribuyente.created_at));
                $('#updated_at').val(formatReadableDate(contribuyente.updated_at));
                
                $('#frecuenciaLetras').text(JSON.stringify(res.frecuencia, null, 2));

                $('#modalContribuyente').removeClass('hidden');
                
                // CRUCIAL: Aplicar la lógica después de cargar los datos
                aplicarLogicaNombres(); 
            }).fail((err) => {
                console.error('Error al obtener contribuyente:', err);
            });
        });

        // Manejo de Guardado/Actualización (Formulario)
        $('#formContribuyente').submit(function(e) {
            e.preventDefault();
            if (!canEdit) return; 

            aplicarLogicaNombres(); 

            const email = $('#email').val().trim();
            limpiarErrores();
            
            if (!isValidEmail(email)) {
                $('#error_email').text('El formato del correo electrónico no es válido.');
                $('#btnGuardar').prop('disabled', false).html(`<span>${$('#contribuyenteId').val() ? 'Guardar' : 'Crear'}</span>`); 
                return;
            }
            
            const id = $('#contribuyenteId').val();
            const url = id ? `/contribuyentes/${id}` : '{{ route("contribuyentes.store") }}';
            const method = id ? 'PUT' : 'POST';
            const formData = $(this).serialize() + `&_method=${method}`; 

            $('#btnGuardar').prop('disabled', true).html('<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path></svg><span>Guardando...</span>'); 

            $.ajax({
                url: url,
                type: 'POST', 
                data: formData, 
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: () => {
                    tabla.ajax.reload(null, false);
                    $('#modalContribuyente').addClass('hidden');
                },
                error: function(err) {
                    if (err.responseJSON && err.responseJSON.errors) {
                        for (let campo in err.responseJSON.errors) {
                            $(`#error_${campo}`).text(err.responseJSON.errors[campo][0]);
                        }
                    } else {
                        console.error('Error al guardar:', err);
                    }
                },
                complete: function() {
                    $('#btnGuardar').prop('disabled', false).html(`<span>${id ? 'Guardar' : 'Crear'}</span>`);
                }
            });
        });

        // Manejo de Eliminación (Se mantiene)
        $('#tablaContribuyentes').on('click', '.btnDelete', function() {
            if (!canEdit) return;
            contribuyenteIdToDelete = $(this).data('id');
            $('#modalConfirmacion').removeClass('hidden');
        });

        $('#btnCancelarEliminar').click(() => {
            $('#modalConfirmacion').addClass('hidden');
            contribuyenteIdToDelete = null; 
        });

        $('#btnConfirmarEliminar').click(function() {
            if (!contribuyenteIdToDelete) return;

            const $btn = $(this);
            $btn.prop('disabled', true).text('Eliminando...');

            $.ajax({
                url: `/contribuyentes/${contribuyenteIdToDelete}`,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { _method: 'DELETE' },
                success: () => {
                    tabla.ajax.reload(null, false);
                    $('#modalConfirmacion').addClass('hidden');
                },
                error: err => console.error('Error al eliminar:', err),
                complete: function() {
                    $btn.prop('disabled', false).text('Eliminar');
                    contribuyenteIdToDelete = null;
                }
            });
        });
    });