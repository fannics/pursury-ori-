@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <strong>Upps!</strong> Han ocurrido errores con su entrada de datos.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <h2>Traducciones</h2>
                            <p>
                                Administre las traducciones y las frases disponibles en el sitio
                            </p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <table class="table table-bordered translation-briefing-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Cantidad de frases</th>
                                        <th class="text-center">Cantidad sin traducir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="count_translations_cell">{{ count($translations) }}</td>
                                        <td id="count_null_cell">{{ $count_null }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-xs-12">
                                    <a class="btn btn-success translation-catalog-edit" id="catalog-add-button" href="#"><i class="fa fa-plus fa-lg"></i></a>
                                    <a class="btn btn-primary translation-catalog-edit" href="#"><i class="fa fa-edit fa-lg"></i></a>
                                    <select name="catalog-select" id="catalog-select" class="form-control translation-catalog-select">
                                        @foreach($catalogs as $cat)
                                            <option {{ $cat->id == $catalog->id ? 'selected="selected"' : '' }} value="{{ $cat->id }}">{{ $cat->target_lang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn btn-warning" id="bulk-translate-missing" href="#"><i class="fa fa-flag fa-lg"></i> Traducir frases pendientes</a>
                                    <a class="btn btn-primary" id="publish_catalog" href="#"><i class="fa fa-check fa-lg"></i> Publicar catálogo</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped" id="trans-table">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">Identificador</th>
                                        <th style="width: 49%;">Frase</th>
                                        <th style="width: 49%">Traducido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @include('admin/translation_items', ['translations' => $translations])
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="catalog-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Catálogo de traducciones</h4>
                </div>
                <div class="modal-body">
                    <form action="" class="form-horizontal">
                        <div class="form-group">
                            <label for="dest_lang" class="control-label col-sm-4 text-right">Idioma de destino</label>
                            <div class="col-sm-4">
                                <input class="form-control" name="dest_lang" id="dest_lang" type="text" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lang_code" class="control-label col-sm-4 text-right">Código de catálogo</label>
                            <div class="col-sm-4">
                                <input class="form-control" name="lang_code" id="lang_code" type="text" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger left">Eliminar</button>
                    <button type="button" id="save-catalog" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade loading-modal" id="translate-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Traducciones automáticas en curso...</h4>
                </div>
                <div class="modal-body">
                    <p>Se están llevando a cabo las traducciones automáticas para las frases pendientes del catálogo seleccionado. Tenga paciencia, este proceso puede demorar</p>
                    <p class="text-center loading-trans">

                    </p>
                    <p class="trans-results"></p>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('javascripts')
    <script type="text/html" id="trans-popover-template">
        <form>
            <div class="form-group">
                <textarea readonly="readonly" class="form-control" name="base_message" id="base_message" cols="40" rows="3">%base_message%</textarea>
            </div>
            <div class="form-group">
                <textarea class="form-control" name="trans_message" id="trans_message" cols="40" rows="3">%trans_message%</textarea>
            </div>
            <div class="form-group text-right">
                <a href="#" id="auto-translate" data-translating-text="Traduciendo..." data-regular-text="Traductor automático" class="btn btn-primary left"><i class="fa fa-flag"></i> <span>Traductor automático</span></a>
                <a href="#" id="close-popover" class="btn btn-danger right"><i class="fa fa-times"></i></a>
                <a href="#" id="save-translation" class="btn btn-success right" style="margin-right: 10px;"><i class="fa fa-check"></i></a>
            </div>
        </form>
    </script>
    <script type="text/javascript">
        $(function(){

            $(document).on('click', '#catalog-add-button', function(e){
                e.preventDefault();
                $('#catalog-modal').modal('show');
            });

            $(document).on('click', '#save-catalog', function(e){
                e.preventDefault();

                var catalogData = {
                    dest: $('#dest_lang').val(),
                    code: $('#lang_code').val()
                };

                $.post(window.app_prefix + '/admin/settings/translations/catalog', catalogData)
                        .success(function(res){
                            if (res.status == 'success'){
                                bootbox.alert(res.message);
                                //reset the form
                                $('#catalog-modal').modal('hide');
                            }
                        })
                        .fail(function(){
                            bootbox.alert('Ha ocurrido un error al realizar la acción');
                        });

            });

            var openedPopover = null;

            $(document).on('click', 'a.trans-popover-trigger', function(e){
                e.preventDefault();

                var link = $(this);

                var base_message = $(this).closest('tr').find('.base-message').text().trim();
                var base_key = $(this).closest('tr').find('.base-key').text().trim();
                var trans_message = $(this).text().trim();



                openedPopover = $(this).webuiPopover({
                    trigger: 'manual',
                    title: 'Ajustar traducción',
                    content: function(data){

                        var html = $('#trans-popover-template').html();

                        html = html.replace(/%base_message%/gi, base_message);

                        html = html.replace(/%trans_message%/gi, trans_message);

                        return html;
                    },
                    closeable: true,
                    width: '450px'
                });

                openedPopover.webuiPopover('show');

            });

            $(document).on('click', '#close-popover', function(e){
                e.preventDefault();

                if (openedPopover){

                    openedPopover.webuiPopover('destroy');
                    openedPopover = null;
                }
            });

            $(document).on('click', '#save-translation', function(e){
                e.preventDefault();

                var translated_text = $(this).closest('form').find('#trans_message').val();

                var catalog_id = $('#catalog-select').val();

                var lang_key = openedPopover.attr('data-phrase-id');

                var self = $(this);

                self.html('<i class="fa fa-spinner fa-spin"></i>');

                $.post('/admin/settings/translations/', {
                    catalog_id: catalog_id,
                    lang_key: lang_key,
                    translated_text: translated_text
                }).success(function(res){

                    if (res.status == 'success'){

                        openedPopover.text(translated_text);
                        //hide the popover
                        openedPopover.webuiPopover('hide');

                    } else {
                        bootbox.alert(res.message);
                    }

                }).fail(function(){
                    bootbox.alert('Ha ocurrido un error al guardar la traducción');
                }).done(function(){
                    self.html('<i class="fa fa-check"></i>')
                });

            });

            $(document).on('click', '#auto-translate', function(e){
                e.preventDefault();

                var lang_key = openedPopover.attr('data-phrase-id');

                var self = $(this);

                self.find('span').first().text(self.attr('data-translating-text'));

                $.post('/admin/settings/translations/autotranslate/', {
                            catalog_id: $('#catalog-select').val(),
                            lang_key: lang_key
                        }).success(function(res){
                            if (res.status == 'success'){

                                var form = self.closest('form');

                                form.find('#trans_message').val(res.translated_text);

                            } else {
                                if (res.message){
                                    bootbox.alert(res.message);
                                } else {
                                    bootbox.alert('Ha ocurrido un error al efectuar la traducción');
                                }
                            }
                        })
                        .fail(function(){

                        })
                        .done(function(){
                            self.find('span').first().text(self.attr('data-regular-text'));
                        });

            });

            $(document).on('change', '#catalog-select', function(){
                $.get('', {catalog_id: $(this).val()})
                        .success(function(res){

                            $('#count_null_cell').text(res.count_null);
                            $('#count_translations_cell').text(res.phrase_count);

                            $('#trans-table tbody').html(res.html);
                        })
                        .fail(function(){

                        });
            });

            $(document).on('click', '#bulk-translate-missing', function(e){

                e.preventDefault();

                $('.loading-trans').html('<i class="fa fa-spinner fa-spin fa-3x"></i>');

                $('.trans-results').text('');

                $('#translate-modal .modal-footer').hide();

                $('#translate-modal').modal({
                    keyboard: false,
                    backdrop: 'static'
                });

                var catalog_id = $('#catalog-select').val();

                $.post('/admin/settings/translations/autotranslate/bulk', {
                    catalog_id: catalog_id
                })
                        .success(function(res){

                            if (res.status !== 'fail'){

                                if (res.status == 'success'){
                                    $('.loading-trans').html('<i class="fa fa-check-circle green fa-3x"></i>');
                                } else {
                                    $('.loading-trans').html('<i class="fa fa-check-circle yellow fa-3x"></i>');
                                }

                                $('#translate-modal .modal-footer').show();

                                $('.trans-results').text(res.message);

                                $.get('/admin/settings/translations/', { catalog_id: catalog_id })
                                        .success(function(res){
                                            $('#count_null_cell').text(res.count_null);
                                            $('#count_translations_cell').text(res.phrase_count);
                                            $('#trans-table tbody').html(res.html);
                                        })
                                        .fail(function(){

                                        });
                            }

                        })
                        .fail(function(){
                            $('#translate-modal .modal-footer').show();
                            bootbox.alert('Ha ocurrido un error al realizar las traducciones automáticas');
                        });

            });

            $(document).on('click', '#publish_catalog', function(e){
                e.preventDefault();

                $.post('/admin/settings/translations/activate', {
                    catalog_id: $('#catalog-select').val()
                }).success(function(res){
                    if (res.status == 'success'){
                        bootbox.alert(res.message);
                    } else {
                        bootbox.alert(res.message);
                    }
                }).fail(function(){
                    bootbox.alert('Ha ocurrido un error al publicar el catálogo');
                });

            });



        });
    </script>
@endsection